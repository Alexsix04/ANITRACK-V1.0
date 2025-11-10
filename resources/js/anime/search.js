document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('anime-search-form');
    const grid = document.getElementById('anime-grid');
    const loader = document.getElementById('loading');
    const noResults = document.getElementById('no-results');
    const queryInput = document.getElementById('query');
    const pageTitle = document.getElementById('anime-page-title');

    if (!form || !grid) return;

    let page = 1;
    let loading = false;
    let hasNextPage = true;

    const renderAnimes = (animes, append = false) => {
        if (!append) grid.innerHTML = '';
        if (!animes || animes.length === 0) {
            if (!append) noResults.classList.remove('hidden');
            return;
        }
        noResults.classList.add('hidden');

        animes.forEach(anime => {
            // Contenedor principal de la tarjeta
            const card = document.createElement('div');
            card.className = "relative group rounded-xl overflow-hidden shadow-md hover:shadow-xl transition";

            card.innerHTML = `
            <a href="/animes/${anime.id}" class="block bg-gray-100 rounded-xl overflow-hidden">
                <img src="${anime.coverImage.large}" alt="${anime.title.romaji}" class="w-full h-64 object-cover">
                <div class="p-2.5">
                    <h3 class="text-base font-semibold truncate">${anime.title.romaji}</h3>
                    <p class="text-sm text-gray-600">
                        ⭐ ${anime.averageScore ?? 'N/A'} | ${anime.format ?? ''}
                    </p>
                </div>
            </a>

            <!-- Botón pequeño en la esquina superior derecha -->
            <button
                class="absolute top-2 right-2 w-8 h-8 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center text-lg font-bold opacity-0 group-hover:opacity-100 transition-shadow shadow-md"
                data-anime-id="${anime.id ?? ''}"
                data-anilist-id="${anime.anilist_id ?? (anime.id ?? '')}"
                data-anime-title="${anime.title.romaji}"
                data-anime-image="${anime.coverImage.large}">
                +
            </button>
        `;

            grid.appendChild(card);
        });
    };

    const fetchAnimes = async (append = false, useDefaults = false) => {
        if (loading || (!hasNextPage && append)) return;

        loading = true;
        loader.classList.remove('hidden');
        noResults.classList.add('hidden');

        const formData = new FormData(form);

        if (useDefaults) {
            // Si queremos resultados por defecto, eliminamos todos los filtros y búsqueda
            formData.delete('query');
            formData.delete('genre');
            formData.delete('season');
            formData.delete('seasonYear');
            formData.delete('format');
            formData.delete('status');
        }

        if (append) formData.set('page', page + 1);
        const params = new URLSearchParams(formData).toString();

        try {
            const response = await fetch(`${form.action}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error(`Error ${response.status}`);
            const data = await response.json();
            if (append) page++;
            hasNextPage = data.pageInfo?.hasNextPage ?? false;
            renderAnimes(data.animes, append);
        } catch (e) {
            console.error('Error al cargar animes:', e);
        } finally {
            loader.classList.add('hidden');
            loading = false;
        }
    };

    const updateState = () => {
        page = 1;
        hasNextPage = true;

        // Detectar si todos los filtros y el buscador están vacíos
        const isEmpty = !queryInput.value.trim() &&
            !form.genre.value &&
            !form.season.value &&
            !form.seasonYear.value &&
            !form.format.value &&
            !form.status.value;

        if (isEmpty) {
            // Recargar la página para mostrar resultados por defecto
            window.location.href = '/animes';
            return;
        }

        // Si hay algún filtro o búsqueda, usar AJAX
        fetchAnimes(false);

        // Mantener URL limpia
        if (history.replaceState) {
            history.replaceState(null, '', '/animes');
        }

        if (pageTitle) pageTitle.textContent = 'Buscar Animes';
    };


    // Debounce input
    let typingTimer;
    const debounceDelay = 400;
    queryInput.addEventListener('input', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(updateState, debounceDelay);
    });

    form.addEventListener('change', updateState);
    form.addEventListener('submit', (e) => { e.preventDefault(); updateState(); });

    // Scroll infinito
    window.addEventListener('scroll', () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 400 && !loading && hasNextPage) {
            fetchAnimes(true);
        }
    });

    // Manejo de botón atrás
    window.addEventListener('popstate', () => {
        if (window.location.pathname === '/animes') updateState();
    });
});