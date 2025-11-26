<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AniListService;
use Illuminate\Support\Collection;
use League\CommonMark\CommonMarkConverter;

class VoiceActorsController extends Controller
{
    protected $aniList;

    public function __construct(AniListService $aniList)
    {
        $this->aniList = $aniList;
    }

    public function show($id)
    {
        $voiceActor = $this->aniList->getStaffSummaryById((int)$id);

        if (!$voiceActor) {
            abort(404, 'Actor de voz no encontrado');
        }

        // Markdown converter
        $converter = new CommonMarkConverter([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);

        $descriptionRaw = (string)($voiceActor['description'] ?? 'Sin descripción disponible.');

        // Extraer enlaces tipo [Texto](URL)
        preg_match_all('/\[(.*?)\]\((https?:\/\/.*?)\)/', $descriptionRaw, $matches, PREG_SET_ORDER);

        $externalLinks = collect($matches)->map(fn($m) => [
            'label' => $m[1],
            'url' => $m[2],
        ]);

        // Remover enlaces del texto original
        $descriptionRaw = preg_replace('/\[(.*?)\]\((https?:\/\/.*?)\)/', '', $descriptionRaw);

        // Separar Non-Anime Roles
        $parts = preg_split('/\*\*Non-Anime Roles:\*\*/i', $descriptionRaw);
        $mainDescription = trim($parts[0] ?? '');
        $nonAnimeRoles = $parts[1] ?? '';

        // Formatear Non-Anime Roles
        if (!empty($nonAnimeRoles)) {
            $rolesLines = preg_split('/\r\n|\r|\n/', $nonAnimeRoles);
            $rolesLines = array_filter(array_map(fn($l) => ltrim(trim($l), '- '), $rolesLines));
            $rolesLines = array_slice($rolesLines, 0, 6);

            if (!empty($rolesLines)) {
                $nonAnimeRoles = '<strong>Non-Anime Roles:</strong><br>• ' . implode('<br>• ', $rolesLines);
            }
        }

        // Convertir markdown
        $descriptionHtml = $converter->convert($mainDescription)->getContent();

        // Combinar descripción + roles
        $finalDescription = $descriptionHtml;
        if (!empty($nonAnimeRoles)) {
            $finalDescription .= "<hr class='my-3 border-gray-700'>" . $nonAnimeRoles;
        }

        // Datos del actor
        $actor = [
            'id' => $voiceActor['id'],
            'name' => $voiceActor['name'],
            'image' => $voiceActor['image'],
            'description' => $finalDescription,
            'favourites' => $voiceActor['favourites'] ?? 0,
            'links' => $externalLinks,
        ];

        /*
    |--------------------------------------------------------------------------
    |  ANIMES DONDE ACTUÓ COMO SEIYUU
    |--------------------------------------------------------------------------
    | Sacado desde characterMedia (correcto para TODOS los idiomas)
    */
        $animeList = collect($voiceActor['characterMedia']['edges'] ?? [])
            ->map(fn($edge) => [
                'id' => $edge['node']['id'] ?? null,
                'title' => $edge['node']['title']['romaji'] ?? 'Sin título',
                'coverImage' => $edge['node']['coverImage']['medium'] ?? null,
            ])
            ->whereNotNull('id')
            ->unique('id')
            ->values();

        /*
    |--------------------------------------------------------------------------
    |  PERSONAJES QUE INTERPRETÓ
    |--------------------------------------------------------------------------
    | También basados en characterMedia → mantiene sincronización exacta con los animes
    */
        $characters = collect($voiceActor['characterMedia']['edges'] ?? [])
            ->flatMap(function ($edge) {
                $anime = $edge['node'];

                return collect($edge['characters'] ?? [])->map(fn($char) => [
                    'id' => $char['id'],
                    'name' => $char['name']['full'] ?? 'Sin nombre',
                    'image' => $char['image']['medium'] ?? null,
                    'favourites' => $char['favourites'] ?? 0,
                    'anime' => [
                        'id' => $anime['id'],
                        'title' => $anime['title']['romaji'] ?? '',
                        'coverImage' => $anime['coverImage']['medium'] ?? null,
                    ],
                ]);
            })
            ->unique('id') // sin personajes duplicados
            ->sortByDesc(fn($c) => $c['favourites'] ?? 0)
            ->values();

        return view('animes.voiceactors.show', [
            'actor' => $actor,
            'animeList' => $animeList,
            'characters' => $characters,
        ]);
    }
}