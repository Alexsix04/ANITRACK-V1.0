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
                    characters(page: 1, perPage: 50) { edges { role node { id name { full } image { large medium } } } }
                    staff(page: 1, perPage: 10) { edges { role node { id name { full } image { large medium } } } }
                    trailer {
                    id
                    site
                    thumbnail
                    }
                    streamingEpisodes {
                    title
                    thumbnail
                    url
                    site
                    }
                    relations {
                        edges {
                            relationType
                                node {
                                    id
                                        title {
                                            romaji
                                            english
                                            }
                                        coverImage {
                                            medium
                                            }
                                        type
                                        format
                                        status
                                    }
                                }
                            }
                        }
                    }
                ';

        return $this->query($query, ['id' => $id])['data']['Media'] ?? null;
    }

    /**
     * Obtiene todos los personajes de un anime.
     */
    public function getCharactersByAnime(int $animeId, int $page = 1, int $perPage = 50)
    {
        $query = '
    query ($id: Int, $page: Int, $perPage: Int) {
        Media(id: $id, type: ANIME) {
            characters(page: $page, perPage: $perPage) {
                pageInfo { total currentPage lastPage hasNextPage }
                edges {
                    role
                    node {
                        id
                        name { full }
                        image { large medium }
                        description
                        age
                        gender
                        bloodType
                        dateOfBirth { year month day }
                    }
                    voiceActors(sort: [RELEVANCE, ID]) {
                        id
                        name { full native }
                        languageV2
                        image { large medium }
                    }
                }
            }
        }
    }
';
        return $this->query($query, ['id' => $animeId, 'page' => $page, 'perPage' => $perPage])['data']['Media']['characters'] ?? [];
    }

    /**
     * Obtiene un personaje específico por su ID.
     */
    public function getCharacterById(int $characterId)
    {
        $query = '
query ($id: Int) {
    Character(id: $id) {
        id
        name { full native }
        image { large medium }
        description
        age
        gender
        bloodType
        dateOfBirth { day month year }
        favourites
        media {
            edges {
                role
                node { 
                    id
                    title { romaji english }
                    coverImage { large medium }
                }
            }
        }
        voiceActors {
            id
            name { full native }
            languageV2
            image { medium large }
        }
    }
}
';


        return $this->query($query, ['id' => $characterId])['data']['Character'] ?? null;
    }

    /**
     * Obtiene el staff de un anime.
     */
    public function getStaffByAnime(int $animeId, int $page = 1, int $perPage = 50)
    {
        $query = '
            query ($id: Int, $page: Int, $perPage: Int) {
                Media(id: $id, type: ANIME) {
                    staff(page: $page, perPage: $perPage) {
                        pageInfo { total currentPage lastPage hasNextPage }
                        edges { role node { id name { full } image { large medium } } }
                    }
                }
            }
        ';

        return $this->query($query, ['id' => $animeId, 'page' => $page, 'perPage' => $perPage])['data']['Media']['staff'] ?? [];
    }

    /**
     * Obtiene un miembro de staff por su ID.
     */
    public function getStaffById(int $staffId, int $page = 1, int $perPage = 25)
{
    $query = '
        query ($id: Int, $page: Int, $perPage: Int) {
            Staff(id: $id) {
                id
                name { full native }
                image { large medium }
                description
                favourites
                staffMedia(page: $page, perPage: $perPage) {
                    pageInfo { total currentPage lastPage hasNextPage }
                    edges { node { id title { romaji english } coverImage { large medium } } }
                }
            }
        }
    ';

    return $this->query($query, ['id' => $staffId, 'page' => $page, 'perPage' => $perPage])['data']['Staff'] ?? null;
}

public function getStaffSummaryById(int $staffId, int $page = 1, int $perPage = 25)
{
    $query = '
        query ($id: Int, $page: Int, $perPage: Int) {
            Staff(id: $id) {
                id
                name { full native }
                image { large medium }
                description
                favourites
                staffMedia(page: $page, perPage: $perPage, sort: POPULARITY_DESC) {
                    edges {
                        node {
                            id
                            title { romaji english }
                            coverImage { large medium }
                        }
                    }
                }
                characterMedia(page: 1, perPage: 50, sort: POPULARITY_DESC) {
                    edges {
                        node {
                            id
                            title { romaji english }
                            coverImage { large medium }
                        }
                        characters {
                            id
                            name { full native }
                            image { large medium }
                            favourites
                        }
                    }
                }
            }
        }
    ';

    return $this->query($query, [
        'id' => $staffId,
        'page' => $page,
        'perPage' => $perPage,
    ])['data']['Staff'] ?? null;
}

    /**
     * Obtiene episodios de un anime.
     */
    public function getEpisodesByAnime(int $animeId)
    {
        $query = '
            query ($id: Int) {
                Media(id: $id, type: ANIME) {
                    episodes
                }
            }
        ';

        return $this->query($query, ['id' => $animeId])['data']['Media']['episodes'] ?? 0;
    }

    /**
     * Obtiene comentarios (reviews) de un anime.
     */
    public function getReviewsByAnime(int $animeId, int $page = 1, int $perPage = 20)
    {
        $query = '
            query ($id: Int, $page: Int, $perPage: Int) {
                Media(id: $id, type: ANIME) {
                    reviews(page: $page, perPage: $perPage) {
                        pageInfo { total currentPage lastPage hasNextPage }
                        nodes { id summary body rating user { id name } }
                    }
                }
            }
        ';

        return $this->query($query, ['id' => $animeId, 'page' => $page, 'perPage' => $perPage])['data']['Media']['reviews'] ?? [];
    }

    /**
     * Obtiene géneros disponibles excluyendo explícitos.
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
