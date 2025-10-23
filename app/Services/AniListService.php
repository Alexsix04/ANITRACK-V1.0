<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AniListService
{
    protected $endpoint = 'https://graphql.anilist.co';
    protected $excludedGenres = ['Ecchi', 'Hentai'];

    /**
     * Método genérico para enviar consultas a la API.
     */
    public function query(string $query, array $variables = [])
    {
        $variables = array_filter($variables, fn($v) => $v !== null);

        $response = Http::post($this->endpoint, [
            'query' => $query,
            'variables' => $variables,
        ]);

        $data = $response->json();

        if (!empty($data['errors'])) {
            Log::error('AniList API Error', $data['errors']);
            return ['data' => []];
        }

        return $data;
    }

    /**
     * Obtiene los detalles de un anime por su ID.
     */
    public function getAnimeById(int $id)
    {
        $query = '
            query ($id: Int) {
                Media(id: $id, type: ANIME) {
                    id
                    title { romaji english native }
                    coverImage { large medium extraLarge }
                    bannerImage
                    description
                    averageScore
                    popularity
                    format
                    episodes
                    season
                    seasonYear
                    genres
                    status
                    source
                    duration
                    studios { edges { node { name } } }
                    startDate { year month day }
                    endDate { year month day }
                    tags { name }
                    characters(page: 1, perPage: 10) { edges { role node { id name { full } image { large medium } } } }
                    staff(page: 1, perPage: 10) { edges { role node { id name { full } image { large medium } } } }
                }
            }
        ';

        return $this->query($query, ['id' => $id])['data']['Media'] ?? null;
    }

    /**
     * Obtiene los géneros disponibles excluyendo explícitos.
     */
    public function getGenres()
    {
        $query = 'query { GenreCollection }';
        $response = $this->query($query);

        return array_diff($response['data']['GenreCollection'] ?? [], $this->excludedGenres);
    }

    /**
     * Búsqueda de animes con filtros complejos.
     */
    public function searchAnimes(array $filters = [], int $page = 1, int $perPage = 100)
    {
        $query = '
            query ($search: String, $page: Int, $perPage: Int, $genre_in: [String], $season: MediaSeason, $seasonYear: Int, $format: MediaFormat, $status: MediaStatus, $sort: [MediaSort], $minScore: Int) {
                Page(page: $page, perPage: $perPage) {
                    pageInfo { total currentPage lastPage hasNextPage }
                    media(
                        type: ANIME,
                        search: $search,
                        genre_in: $genre_in,
                        season: $season,
                        seasonYear: $seasonYear,
                        format: $format,
                        status: $status,
                        sort: $sort,
                        averageScore_greater: $minScore
                    ) {
                        id
                        title { romaji english native }
                        coverImage { large medium extraLarge }
                        averageScore
                        popularity
                        format
                        episodes
                        season
                        seasonYear
                        genres
                        status
                    }
                }
            }
        ';

        $variables = array_merge([
            'search' => $filters['search'] ?? null,
            'genre_in' => $filters['genre_in'] ?? null,
            'season' => $filters['season'] ?? null,
            'seasonYear' => $filters['seasonYear'] ?? null,
            'format' => $filters['format'] ?? null,
            'status' => $filters['status'] ?? null,
            'sort' => $filters['sort'] ?? ['POPULARITY_DESC', 'START_DATE_DESC'],
            'minScore' => $filters['minScore'] ?? null,
            'page' => $page,
            'perPage' => $perPage,
        ], []);

        $data = $this->query($query, $variables)['data']['Page'] ?? [];
        $animes = $data['media'] ?? [];
        $pageInfo = $data['pageInfo'] ?? [];

        // Excluir géneros explícitos
        $animes = array_filter($animes, fn($a) => empty(array_intersect($a['genres'], $this->excludedGenres)));

        // Ordenar según sort
        if (isset($filters['sort']) && in_array('SCORE_DESC', $filters['sort'])) {
            usort($animes, fn($a, $b) => ($b['averageScore'] ?? 0) <=> ($a['averageScore'] ?? 0));
        } else {
            usort($animes, fn($a, $b) => ($b['popularity'] ?? 0) <=> ($a['popularity'] ?? 0));
        }

        return compact('animes', 'pageInfo');
    }
    public function searchAnimeBySeason(array $params = [])
    {
        $filters = [];

        if (isset($params['status'])) $filters['status'] = $params['status'];
        if (isset($params['season'])) $filters['season'] = $params['season'];
        if (isset($params['seasonYear'])) $filters['seasonYear'] = $params['seasonYear'];
        if (isset($params['sort'])) $filters['sort'] = $params['sort'];
        if (isset($params['minScore'])) $filters['minScore'] = $params['minScore'];

        $page = $params['page'] ?? 1;
        $perPage = $params['perPage'] ?? 10;

        return $this->searchAnimes($filters, $page, $perPage)['animes'] ?? [];
    }
}
