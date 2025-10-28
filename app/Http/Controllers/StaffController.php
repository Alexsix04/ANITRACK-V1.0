<?php

namespace App\Http\Controllers;

use App\Services\AniListService;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    protected $aniList;

    public function __construct(AniListService $aniList)
    {
        $this->aniList = $aniList;
    }

    /**
     * Lista de staff de un anime (25 por página, scroll infinito)
     */
    public function index(Request $request, $animeId)
    {
        $anime = $this->aniList->getAnimeById((int)$animeId);
        if (!$anime) abort(404, 'Anime no encontrado');

        $page = (int)$request->get('page', 1);
        $perPage = 25;

        // Obtenemos solo la página que necesitamos
        $staffPage = $this->aniList->getStaffByAnime((int)$animeId, $page, $perPage);

        $staff = $staffPage['edges'] ?? [];
        $hasMore = $staffPage['pageInfo']['hasNextPage'] ?? false;

        if ($request->ajax()) {
            $html = '';
            foreach ($staff as $s) {
                $html .= '
                <a href="' . route('animes.staff.show', [
                    'anime' => $anime['id'],
                    'staff' => $s['node']['id']
                ]) . '" class="flex items-center bg-gray-100 p-3 rounded-lg shadow-sm hover:shadow-md transition">
                    <img src="' . $s['node']['image']['medium'] . '"
                        alt="' . e($s['node']['name']['full']) . '"
                        class="w-16 h-20 object-cover rounded-md mr-3 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-800 truncate">'
                    . e($s['node']['name']['full']) . '</h3>
                        <p class="text-xs text-gray-500">' . ucfirst(strtolower($s['role'])) . '</p>
                    </div>
                </a>';
            }

            return response()->json([
                'html' => $html,
                'hasMore' => $hasMore,
            ]);
        }

        return view('animes.staff.index', compact('anime', 'staff'));
    }

    /**
     * Detalle de un miembro del staff
     */
    public function show($animeId, $staffId)
    {
        $anime = $this->aniList->getAnimeById((int)$animeId);
        if (!$anime) abort(404, 'Anime no encontrado');

        $staffMember = $this->aniList->getStaffById((int)$staffId);

        if (!$staffMember) abort(404, 'Miembro del staff no encontrado');

        $staff = [
            'id' => $staffMember['id'],
            'name' => $staffMember['name'],
            'image' => $staffMember['image'],
            'description' => $staffMember['description'] ?? null,
            'favourites' => $staffMember['favourites'] ?? 0,
            'media' => $staffMember['staffMedia']['edges'] ?? [],
        ];

        return view('animes.staff.show', compact('staff'));
    }
}