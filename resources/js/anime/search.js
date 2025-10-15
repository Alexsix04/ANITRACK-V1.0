/**
 * search.js
 * Maneja búsqueda dinámica y scroll infinito de animes
 * Compatible con AnimeController AJAX JSON
 */

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form"); // formulario de búsqueda
    const grid = document.getElementById("anime-grid"); // contenedor de animes
    const loader = document.getElementById("loading"); // spinner
    const baseUrl = window.location.pathname; // /animes

    let currentPage = window.pageInfo?.currentPage ?? 1;
    let lastPage = window.pageInfo?.lastPage ?? 1;
    let loading = false;

    // Guardamos los filtros actuales
    let currentFilters = Object.fromEntries(new FormData(form));

    /**
     * Renderiza un anime en el grid
     */
    function appendAnime(anime) {
        const html = `
        <a href="/animes/${anime.id}">
            <div class="bg-gray-100 rounded-lg overflow-hidden shadow hover:shadow-lg transition">
                <img src="${anime.coverImage.large}" 
                     alt="${anime.title.romaji}" 
                     class="w-full h-64 object-cover">
                <div class="p-2">
                    <h3 class="text-lg font-bold truncate">${anime.title.romaji}</h3>
                    <p class="text-sm text-gray-600">
                        ⭐ ${anime.averageScore ?? "N/A"} | ${anime.format ?? ""}
                    </p>
                </div>
            </div>
        </a>`;
        grid.insertAdjacentHTML("beforeend", html);
    }

    /**
     * Carga una página específica usando los filtros actuales
     */
    async function loadPage(page) {
        if (loading || page > lastPage) return;
        loading = true;
        loader.classList.remove("hidden");

        try {
            const params = new URLSearchParams(currentFilters);
            params.set("page", page);

            const res = await fetch(`${baseUrl}?${params.toString()}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });

            if (!res.ok) throw new Error("Error cargando animes");

            const data = await res.json();

            data.animes.forEach(appendAnime);
            currentPage = data.pageInfo.currentPage;
            lastPage = data.pageInfo.lastPage;
        } catch (err) {
            console.error(err);
        } finally {
            loader.classList.add("hidden");
            loading = false;
        }
    }

    /**
     * Maneja scroll infinito
     */
    function handleScroll() {
        const scrollPos = window.innerHeight + window.scrollY;
        const threshold = document.body.offsetHeight - 400;
        if (scrollPos >= threshold) {
            loadPage(currentPage + 1);
        }
    }

    /**
     * Búsqueda dinámica (submit del formulario)
     */
    async function searchAnimes(e) {
        e.preventDefault();
        loading = true;
        loader.classList.remove("hidden");
        grid.innerHTML = ""; // limpiar resultados anteriores
        currentPage = 1;

        // Guardamos filtros actuales
        currentFilters = Object.fromEntries(new FormData(form));

        await loadPage(currentPage);
        loading = false;
    }

    // Evento submit
    if (form) {
        form.addEventListener("submit", searchAnimes);
    }

    // Scroll infinito
    window.addEventListener("scroll", handleScroll);
});