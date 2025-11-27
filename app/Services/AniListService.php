<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AniListService
{
    protected $endpoint = 'https://graphql.anilist.co';
    protected $excludedGenres = ['Yuri', 'Hentai', 'Yaoi',];

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
        $cacheKey = "anime_{$id}";

        return Cache::remember($cacheKey, 3600, function () use ($id) {
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
                    characters(page: 1, perPage: 50) { 
                        edges { role node { id name { full } image { large medium } } } 
                    }
                    staff(page: 1, perPage: 10) { 
                        edges { role node { id name { full } image { large medium } } } 
                    }
                    trailer { id site thumbnail }
                    streamingEpisodes { title thumbnail url site }
                    relations {
                        edges {
                            relationType
                            node {
                                id
                                title { romaji english }
                                coverImage { medium }
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
        });
    }

    /**
     * Obtiene todos los personajes de un anime (con cache por página).
     */
    public function getCharactersByAnime(int $animeId, int $page = 1, int $perPage = 50)
    {
        $cacheKey = "anime_{$animeId}_characters_page_{$page}_per_{$perPage}";

        return Cache::remember($cacheKey, 3600, function () use ($animeId, $page, $perPage) {
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

            return $this->query($query, [
                'id' => $animeId,
                'page' => $page,
                'perPage' => $perPage
            ])['data']['Media']['characters'] ?? [];
        });
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
    /**
     * Obtiene el staff de un anime (con cache por página).
     */
    public function getStaffByAnime(int $animeId, int $page = 1, int $perPage = 50)
    {
        $cacheKey = "anime_{$animeId}_staff_page_{$page}_per_{$perPage}";

        return Cache::remember($cacheKey, 3600, function () use ($animeId, $page, $perPage) {
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

            return $this->query($query, [
                'id' => $animeId,
                'page' => $page,
                'perPage' => $perPage
            ])['data']['Media']['staff'] ?? [];
        });
    }

    /**
     * Obtiene la información de un miembro del staff y sus animes (cache por página).
     */
    public function getStaffById(int $staffId, int $page = 1, int $perPage = 25)
    {
        $cacheKey = "staff_{$staffId}_page_{$page}_per_{$perPage}";

        return Cache::remember($cacheKey, 3600, function () use ($staffId, $page, $perPage) {
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

            return $this->query($query, [
                'id' => $staffId,
                'page' => $page,
                'perPage' => $perPage
            ])['data']['Staff'] ?? null;
        });
    }

    /**
     * Obtiene un resumen del staff y sus animes/personajes (cache por página).
     */
    public function getStaffSummaryById(int $staffId, int $page = 1, int $perPage = 25)
    {
        $cacheKey = "staff_summary_{$staffId}_page_{$page}_per_{$perPage}";

        return Cache::remember($cacheKey, 3600, function () use ($staffId, $page, $perPage) {
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
        });
    }

    /**
     * Obtiene episodios de un anime (cache por 1 hora).
     */
    public function getEpisodesByAnime(int $animeId)
    {
        $cacheKey = "anime_{$animeId}_episodes";

        return Cache::remember($cacheKey, 3600, function () use ($animeId) {
            $query = '
            query ($id: Int) {
                Media(id: $id, type: ANIME) {
                    episodes
                }
            }
        ';

            return $this->query($query, ['id' => $animeId])['data']['Media']['episodes'] ?? 0;
        });
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

        // Generar una key de cache única según los parámetros
        $cacheKey = 'search_anime_' . md5(json_encode($params));

        return Cache::remember($cacheKey, 3600, function () use ($filters, $page, $perPage) {
            return $this->searchAnimes($filters, $page, $perPage)['animes'] ?? [];
        });
    }

    /**
     * Buscar ANIMES por nombre (parcial).
     */
    public function searchAnimesByName(string $search, int $limit = 8): array
    {
        $query = '
        query ($search: String, $limit: Int) {
          Page(perPage: $limit) {
            media(search: $search, type: ANIME) {
              id
              title {
                romaji
                english
                native
              }
              coverImage {
                large
                medium
              }
            }
          }
        }';

        $result = $this->query($query, [
            'search' => $search,
            'limit' => $limit,
        ]);

        if (!$result) return [];

        $items = $result['data']['Page']['media'] ?? [];

        return collect($items)->map(function ($anime) {
            return [
                'id' => $anime['id'],
                'title' => $anime['title']['romaji']
                    ?? $anime['title']['english']
                    ?? $anime['title']['native'],
                'image' => $anime['coverImage']['medium'] ?? null,
            ];
        })->toArray();
    }

    /**
     * Buscar PERSONAJES por nombre (parcial).
     */
    public function searchCharactersByName(string $search, int $limit = 8): array
    {
        $query = '
    query ($search: String, $limit: Int) {
      Page(perPage: $limit) {
        characters(search: $search) {
          id
          name {
            full
          }
          image {
            large
            medium
          }
          media {
            nodes {
              id
              popularity
              averageScore
            }
          }
        }
      }
    }';

        $result = $this->query($query, [
            'search' => $search,
            'limit' => $limit,
        ]);

        if (!$result) return [];

        $items = $result['data']['Page']['characters'] ?? [];

        return collect($items)->map(function ($char) {

            $media = collect($char['media']['nodes']);

            //  Filtrar nodos sin id
            $media = $media->filter(fn($m) => !empty($m['id']));

            //  Orden automático por importancia (popularity > averageScore)
            $media = $media->sortByDesc(function ($m) {
                return [
                    $m['popularity'] ?? 0,
                    $m['averageScore'] ?? 0,
                ];
            });

            // Seleccionar anime principal
            $mainAnime = $media->first();
            $animeId = $mainAnime['id'] ?? null;

            return [
                'id' => $char['id'],
                'name' => $char['name']['full'],
                'image' => $char['image']['medium'] ?? null,
                'anime_id' => $animeId,
            ];
        })->toArray();
    }
}
