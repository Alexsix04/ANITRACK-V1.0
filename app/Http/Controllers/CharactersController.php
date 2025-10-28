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

    // Lista de personajes de un anime
    public function index(Request $request, $animeId)
    {
        $anime = $this->aniList->getAnimeById((int)$animeId);
        if (!$anime) abort(404, 'Anime no encontrado');

        // Traer todos los personajes del anime
        $allCharacters = $this->aniList->getAllCharactersByAnime((int)$animeId);

        // Filtrar MAIN primero
        $allCharacters = collect($allCharacters)
            ->sortByDesc(fn($c) => $c['role'] === 'MAIN')
            ->values()
            ->all();

        // Paginación manual para el scroll infinito
        $perPage = 25;
        $page = (int) $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        $characters = array_slice($allCharacters, $offset, $perPage);

        // Respuesta AJAX para scroll infinito
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
                'hasMore' => ($offset + $perPage) < count($allCharacters),
            ]);
        }

        // Vista normal
        return view('animes.characters.index', compact('anime', 'characters'));
    }
    // Detalle de un personaje
    public function show($animeId, $characterId)
    {
        $anime = $this->aniList->getAnimeById((int)$animeId);
        if (!$anime) abort(404, 'Anime no encontrado');

        $characters = $this->aniList->getAllCharactersByAnime((int)$animeId);

        $character = collect($characters)
            ->firstWhere('node.id', (int)$characterId);

        if (!$character) abort(404, 'Personaje no encontrado');

        // Mapear al formato que espera tu vista
        $character = [
            'id' => $character['node']['id'],
            'name' => $character['node']['name'],
            'image' => $character['node']['image'],
            'role' => $character['role'],
            'description' => $character['node']['description'] ?? null,
            'gender' => $character['node']['gender'] ?? null,
            'age' => $character['node']['age'] ?? null,
            'voiceActors' => $character['voiceActors'] ?? [],
            'media' => $character['media'] ?? [],
        ];

        return view('animes.characters.show', compact('character'));
    }
}
