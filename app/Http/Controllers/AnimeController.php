<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AniListService;

class AnimeController extends Controller
{
    protected $aniList;

    public function __construct(AniListService $aniList)
    {
        $this->aniList = $aniList;
    }

    public function index(Request $request)
    {
        $perPage = 100;
        $page = $request->input('page', 1);
        $filterFromHome = $request->input('filter');

        // Filtros manuales
        $query = $request->input('query');
        $genre = $request->input('genre');
        $season = $request->input('season');
        $seasonYear = $request->input('seasonYear');
        $format = $request->input('format');
        $status = $request->input('status');

        // Construir filtros para el servicio
        $filters = [];
        if ($genre) $filters['genre_in'] = [$genre];
        if ($season) $filters['season'] = $season;
        if ($seasonYear) $filters['seasonYear'] = (int)$seasonYear;
        if ($format) $filters['format'] = $format;
        if ($status) $filters['status'] = $status;
        if ($query) $filters['search'] = $query;

        // Filtros predefinidos
        $applyFilter = $filterFromHome && !$query && !$genre && !$season && !$seasonYear && !$format && !$status;
        $filter = $applyFilter ? $filterFromHome : null;
        $title = $applyFilter ? ucfirst(str_replace('-', ' ', $filterFromHome)) : 'Buscar Animes';

        if ($applyFilter && $filter) {
            switch ($filter) {
                case 'mas-populares-de-la-temporada':
                    $month = now()->month;
                    $filters['season'] = match (true) {
                        $month >= 3 && $month <= 5 => 'SPRING',
                        $month >= 6 && $month <= 8 => 'SUMMER',
                        $month >= 9 && $month <= 11 => 'FALL',
                        default => 'WINTER',
                    };
                    $filters['seasonYear'] = now()->year;
                    $filters['sort'] = ['POPULARITY_DESC'];
                    break;
                case 'estrenos':
                case 'en-emision':
                    $filters['status'] = 'RELEASING';
                    $filters['sort'] = ['START_DATE_DESC', 'POPULARITY_DESC'];
                    $filters['minScore'] = 1;
                    break;
                case 'mejor-ranqueados':
                case 'mejor-valorados':
                    $filters['sort'] = ['SCORE_DESC'];
                    $filters['status'] = 'FINISHED';
                    break;
                case 'proximos-estrenos':
                case 'proximos-estrenos-2026':
                    $filters['status'] = 'NOT_YET_RELEASED';
                    $filters['sort'] = ['POPULARITY_DESC'];
                    $filters['seasonYear'] = now()->year + 1;
                    //  $filters['minScore'] = 1;
                    break;
            }
        }

        $result = $this->aniList->searchAnimes($filters, $page, $perPage);

        if ($request->ajax()) {
            return response()->json($result);
        }

        $genres = $this->aniList->getGenres();

        return view('animes.index', array_merge($result, [
            'genres' => $genres,
            'filter' => $filter,
            'title' => $title,
            'query' => $query,
            'genre' => $genre,
            'season' => $season,
            'seasonYear' => $seasonYear,
            'format' => $format,
            'status' => $status,
        ]));
    }

    public function show($id, $seccion = null)
    {
        $anime = $this->aniList->getAnimeById((int)$id);

        if (!$anime) abort(404, 'Anime no encontrado');

        $seccion = $seccion ?? 'opcion1';

        return view('animes.show', compact('anime', 'seccion'));
    }
}