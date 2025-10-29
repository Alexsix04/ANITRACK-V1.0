<?php

namespace App\Http\Controllers;

use App\Services\AniListService;
use Illuminate\Http\Request;

class CharactersController extends Controller
{
    protected $aniList;

    public function __construct(AniListService $aniList)
    {
        $this->aniList = $aniList;
    }

    // Lista de personajes de un anime (scroll infinito 25 por página)
    public function index(Request $request, $animeId)
    {
        $anime = $this->aniList->getAnimeById((int)$animeId);
        if (!$anime) abort(404, 'Anime no encontrado');

        $page = (int)$request->get('page', 1);
        $perPage = 25;

        // Obtenemos solo la página que necesitamos
        $charactersPage = $this->aniList->getCharactersByAnime((int)$animeId, $page, $perPage);

        $characters = $charactersPage['edges'] ?? [];
        $hasMore = $charactersPage['pageInfo']['hasNextPage'] ?? false;

        // Respuesta AJAX
        if ($request->ajax()) {
            $html = '';
            foreach ($characters as $char) {
                $html .= '
                <a href="' . route('animes.characters.show', [
                    'anime' => $anime['id'],
                    'character' => $char['node']['id']
                ]) . '" class="flex items-center bg-gray-100 p-3 rounded-lg shadow-sm hover:shadow-md transition">
                    <img src="' . $char['node']['image']['medium'] . '"
                        alt="' . e($char['node']['name']['full']) . '"
                        class="w-16 h-20 object-cover rounded-md mr-3 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-800 truncate">'
                    . e($char['node']['name']['full']) . '</h3>
                        <p class="text-xs text-gray-500">' . ucfirst(strtolower($char['role'])) . '</p>
                    </div>
                </a>';
            }

            return response()->json([
                'html' => $html,
                'hasMore' => $hasMore,
            ]);
        }

        return view('animes.characters.index', compact('anime', 'characters'));
    }

    // Detalle de un personaje
    public function show($animeId, $characterId)
    {
        // Obtener el anime completo con relaciones
        $anime = $this->aniList->getAnimeById((int)$animeId, ['relations']);
        if (!$anime) abort(404, 'Anime no encontrado');

        // Buscar el personaje dentro del anime (paginando de a 50)
        $page = 1;
        $perPage = 50;
        $characterData = null;
        $voiceActors = []; // <-- Inicializamos los actores de voz

        do {
            $charactersPage = $this->aniList->getCharactersByAnime((int)$animeId, $page, $perPage);
            $edges = $charactersPage['edges'] ?? [];

            foreach ($edges as $char) {
                if ((int)$char['node']['id'] === (int)$characterId) {
                    $characterData = $char['node'];
                    $role = $char['role'] ?? null;
                    $voiceActors = $char['voiceActors'] ?? []; // <-- Guardamos solo los actores de este personaje
                    break 2;
                }
            }

            $page++;
            $hasMore = $charactersPage['pageInfo']['hasNextPage'] ?? false;
        } while ($hasMore && !$characterData);

        if (!$characterData) abort(404, 'Personaje no encontrado');

        // Procesar descripción y extraer atributos
        $rawDescription = $characterData['description'] ?? '';
        $attributes = [];
        $cleanDescription = $rawDescription;

        // Traducciones de atributos
        $translations = [
            'Height' => 'Altura',
            'Initial Height' => 'Altura inicial',
            'Weight' => 'Peso',
            'Position' => 'Posición',
            'Age' => 'Edad',
            'Gender' => 'Género',
            'Blood Type' => 'Tipo de sangre',
            'Affiliation' => 'Afiliación',
            'Occupation' => 'Ocupación',
            'Relatives' => 'Familiares',
            'Hair Color' => 'Color de cabello',
            'Eye Color' => 'Color de ojos',
            'Origin' => 'Origen',
            'Species' => 'Especie',
            'Alias' => 'Alias',
            'Weapon' => 'Arma',
        ];

        // Detectar atributos tipo __Height__: o **Height:**
        if (preg_match_all('/(?:__|\*\*)\s*([A-Za-z0-9\s\'\-]+)\s*(?:__|\*\*)?:\s*([^\n]+)/', $rawDescription, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $key = trim($match[1]);
                $value = trim($match[2]);
                $translatedKey = $translations[$key] ?? $key;

                // Reemplazar enlaces de personajes dentro del valor
                $value = preg_replace_callback(
                    '/\[(.*?)\]\(https:\/\/anilist\.co\/character\/(\d+)(?:\/[^\)]*)?\)/',
                    function ($m) use ($animeId) {
                        $text = e($m[1]);
                        $id = (int)$m[2];
                        $url = route('animes.characters.show', ['anime' => $animeId, 'character' => $id]);
                        return "<a href=\"{$url}\" class=\"text-blue-600 hover:underline\">{$text}</a>";
                    },
                    $value
                );

                $attributes[$translatedKey] = $value;
            }

            // Eliminar los atributos detectados del cuerpo
            $cleanDescription = preg_replace('/(?:__|\*\*)\s*[A-Za-z0-9\s\'\-]+\s*(?:__|\*\*)?:\s*[^\n]+/', '', $rawDescription);
        }

        // Reemplazar enlaces de personajes en la descripción general
        $cleanDescription = preg_replace_callback(
            '/\[(.*?)\]\(https:\/\/anilist\.co\/character\/(\d+)(?:\/[^\)]*)?\)/',
            function ($m) use ($animeId) {
                $text = e($m[1]);
                $id = (int)$m[2];
                $url = route('animes.characters.show', ['anime' => $animeId, 'character' => $id]);
                return "<a href=\"{$url}\" class=\"text-blue-600 hover:underline\">{$text}</a>";
            },
            $cleanDescription
        );

        // Añadir datos fijos del personaje si existen y no están ya
        if (!empty($characterData['age']) && !isset($attributes['Edad'])) {
            $attributes['Edad'] = e($characterData['age']);
        }

        if (!empty($characterData['gender']) && !isset($attributes['Género'])) {
            $attributes['Género'] = e($characterData['gender']);
        }

        if (!empty($characterData['bloodType']) && !isset($attributes['Tipo de sangre'])) {
            $attributes['Tipo de sangre'] = e($characterData['bloodType']);
        }

        // Cumpleaños: si existe dateOfBirth con day/month/year
        if (
            !empty($characterData['dateOfBirth']) &&
            (!empty($characterData['dateOfBirth']['day']) || !empty($characterData['dateOfBirth']['month']))
        ) {

            $day = $characterData['dateOfBirth']['day'] ?? null;
            $month = $characterData['dateOfBirth']['month'] ?? null;
            $year = $characterData['dateOfBirth']['year'] ?? null;

            $birthday = trim(($day ? $day : '') . '/' . ($month ? $month : '') . ($year ? '/' . $year : ''));
            if ($birthday && !isset($attributes['Cumpleaños'])) {
                $attributes['Cumpleaños'] = $birthday;
            }
        }

        // Mapear personaje al formato de la vista
        $character = [
            'id' => $characterData['id'],
            'name' => $characterData['name'],
            'image' => $characterData['image'],
            'role' => $role,
            'description' => $cleanDescription,
            'favourites' => $characterData['favourites'] ?? 0,
            'media' => $characterData['media']['edges'] ?? [],
            'voiceActors' => $voiceActors, // <-- actores de voz solo para este personaje en este anime
            'extra_attributes' => $attributes,
        ];

        // Procesar prequels/sequels del anime
        $relatedMedia = collect($anime['relations']['edges'] ?? [])->map(function ($edge) {
            return [
                'id' => $edge['node']['id'] ?? null,
                'title' => $edge['node']['title']['romaji'] ?? 'Sin título',
                'relationType' => $edge['relationType'] ?? null,
                'coverImage' => $edge['node']['coverImage']['medium'] ?? null,
                'node' => $edge['node'] ?? null,
            ];
        })->filter(function ($item) {
            return in_array($item['relationType'], ['PREQUEL', 'SEQUEL']) && !empty($item['node']);
        });

        // Enviar todo a la vista
        return view('animes.characters.show', compact('character', 'anime', 'relatedMedia'));
    }
}
