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

        dd([
        'animeId' => $animeId,
        'characterId' => $characterId
    ]);
    
        $anime = $this->aniList->getAnimeById((int)$animeId);
        if (!$anime) abort(404, 'Anime no encontrado');

        $characterData = $this->aniList->getCharacterById((int)$characterId);
        if (!$characterData) abort(404, 'Personaje no encontrado');

        // Mapear al formato que espera la vista
        $character = [
            'id' => $characterData['id'],
            'name' => $characterData['name'],
            'image' => $characterData['image'],
            'description' => $characterData['description'] ?? null,
            'favourites' => $characterData['favourites'] ?? 0,
            'media' => $characterData['media']['edges'] ?? [],
        ];

        return view('animes.characters.show', compact('character'));
    }
}