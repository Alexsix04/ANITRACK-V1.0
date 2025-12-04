<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AnimeThemesService
{
    private string $endpoint = 'https://graphql.animethemes.moe/';

    /**
     * Buscar un anime en AnimeThemes usando AniList ID
     * Solo devuelve el resultado de findAnimeByExternalSite
     *
     * @param int $anilistId
     * @return array|null
     */
    public function getThemesByAnilistId(int $anilistId): ?array
    {
        // 1️⃣ Obtener el nombre y el slug del anime
        $findAnimeQuery = <<<'GRAPHQL'
        query ($ids: [Int!]) {
            findAnimeByExternalSite(site: ANILIST, id: $ids) {
                name
                slug
            }
        }
        GRAPHQL;

        $response = Http::post($this->endpoint, [
            'query' => $findAnimeQuery,
            'variables' => ['ids' => [$anilistId]],
        ]);

        if (!$response->successful()) {
            return null;
        }

        $animeArray = $response->json('data.findAnimeByExternalSite');

        if (empty($animeArray) || !is_array($animeArray)) {
            return null;
        }

        $anime = $animeArray[0];
        $slug = $anime['slug'] ?? null;

        if (!$slug) {
            return null; // No hay slug disponible
        }

        // 2️⃣ Obtener los animethemes usando el slug
        $themesQuery = <<<'GRAPHQL'
        query ($slug: String!) {
            anime(slug: $slug) {
                name
                animethemes {
                    id
                    type
                    sequence
                    song {
                        id
                        title
                    }
                }
            }
        }
        GRAPHQL;

        $themesResponse = Http::post($this->endpoint, [
            'query' => $themesQuery,
            'variables' => ['slug' => $slug],
        ]);

        if (!$themesResponse->successful()) {
            return null;
        }

        $themesData = $themesResponse->json('data.anime.animethemes');

        return $themesData ?: null;
    }
}