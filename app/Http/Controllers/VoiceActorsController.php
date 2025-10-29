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

        // ✅ Configurar el convertidor Markdown
        $converter = new CommonMarkConverter([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);

        $descriptionRaw = $voiceActor['description'] ?? 'Sin descripción disponible.';

        // ✅ Extraer enlaces (formato [Texto](URL))
        preg_match_all('/\[(.*?)\]\((https?:\/\/.*?)\)/', $descriptionRaw, $matches, PREG_SET_ORDER);

        $externalLinks = collect($matches)->map(fn($m) => [
            'label' => $m[1],
            'url' => $m[2],
        ]);

        // ✅ Quitar los enlaces del texto original
        $descriptionRaw = preg_replace('/\[(.*?)\]\((https?:\/\/.*?)\)/', '', $descriptionRaw);

        // ✅ Separar "Non-Anime Roles"
        $parts = explode('**Non-Anime Roles:**', $descriptionRaw);
        $mainDescription = trim($parts[0]);
        $nonAnimeRoles = $parts[1] ?? '';

        // ✅ Limitar Non-Anime Roles (por ejemplo, a los primeros 6)
        if (!empty($nonAnimeRoles)) {
            $rolesLines = preg_split('/\r\n|\r|\n|- /', $nonAnimeRoles);
            $rolesLines = array_filter(array_map('trim', $rolesLines));
            $rolesLines = array_slice($rolesLines, 0, 6);
            $nonAnimeRoles = implode('<br>• ', $rolesLines);
            $nonAnimeRoles = '<strong>Non-Anime Roles:</strong><br>• ' . $nonAnimeRoles;
        }

        // ✅ Convertir Markdown → HTML
        $descriptionHtml = $converter->convert($mainDescription)->getContent();

        // ✅ Combinar descripción principal + roles
        $finalDescription = $descriptionHtml;
        if (!empty($nonAnimeRoles)) {
            $finalDescription .= "<hr class='my-3 border-gray-700'>" . $nonAnimeRoles;
        }

        // ✅ Datos base del actor
        $actor = [
            'id' => $voiceActor['id'],
            'name' => $voiceActor['name'],
            'image' => $voiceActor['image'],
            'description' => $finalDescription,
            'favourites' => $voiceActor['favourites'] ?? 0,
            'links' => $externalLinks,
        ];

        // ✅ Animes (sin duplicados)
        $animeList = collect($voiceActor['staffMedia']['edges'] ?? [])
            ->map(fn($edge) => [
                'id' => $edge['node']['id'] ?? null,
                'title' => $edge['node']['title']['romaji'] ?? 'Sin título',
                'coverImage' => $edge['node']['coverImage']['medium'] ?? null,
            ])
            ->filter(fn($anime) => !empty($anime['id']))
            ->unique('id')
            ->values();

        // ✅ Personajes (sin duplicados)
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
            ->unique('id')
            ->sortByDesc('favourites')
            ->values();

        return view('animes.voiceactors.show', [
            'actor' => $actor,
            'animeList' => $animeList,
            'characters' => $characters,
        ]);
    }
}