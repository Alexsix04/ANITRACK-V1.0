document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('anime-search-form');
    const grid = document.getElementById('anime-grid');
    const loader = document.getElementById('loading');
    const noResults = document.getElementById('no-results');
    const queryInput = document.getElementById('query');

    if (!form || !grid) return;

    let page = 1;
    let loading = false;
    let hasNextPage = true;

    /**
     * Renderiza las tarjetas de anime en el grid
     * @param {Array} animes
     * @param {Boolean} append - si true, agrega al grid existente
     */
    const renderAnimes = (animes, append = false) => {
        if (!append) grid.innerHTML = '';

        if (!animes || animes.length === 0) {
            if (!append) noResults.classList.remove('hidden');
            return;
        }

        noResults.classList.add('hidden');

        animes.forEach(anime => {
            const card = document.createElement('a');
            card.href = `/animes/${anime.id}`;
            card.innerHTML = `
                <div class="bg-gray-100 rounded-lg overflow-hidden shadow hover:shadow-lg transition">
                    <img src="${anime.coverImage.large}" alt="${anime.title.romaji}" class="w-full h-64 object-cover">
                    <div class="p-2">
                        <h3 class="text-lg font-bold truncate">${anime.title.romaji}</h3>
                        <p class="text-sm text-gray-600">
                            ⭐ ${anime.averageScore ?? 'N/A'} | ${anime.format ?? ''}
                        </p>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });
    };

    /**
     * Realiza la petición AJAX para buscar animes
     */
    const fetchAnimes = async (append = false) => {
        if (loading || (!hasNextPage && append)) return;

        loading = true;
        loader.classList.remove('hidden');
        noResults.classList.add('hidden');

        const formData = new FormData(form);
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
        } catch (error) {
            console.error('Error al cargar los animes:', error);
        } finally {
            loader.classList.add('hidden');
            loading = false;
        }
    };

    /**
     * Debounce para campo de búsqueda
     */
    let typingTimer;
    const debounceDelay = 400;
    queryInput.addEventListener('input', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            page = 1;
            hasNextPage = true;
            fetchAnimes(false);
        }, debounceDelay);
    });

    /**
     * Escucha cambios en selects y reinicia búsqueda
     */
    form.addEventListener('change', () => {
        page = 1;
        hasNextPage = true;
        fetchAnimes(false);
    });

    /**
     * Evita recarga al hacer submit manual
     */
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        page = 1;
        hasNextPage = true;
        fetchAnimes(false);
    });

    /**
     * Scroll infinito (carga siguiente página)
     */
    window.addEventListener('scroll', () => {
        if (
            window.innerHeight + window.scrollY >= document.body.offsetHeight - 400 &&
            !loading &&
            hasNextPage
        ) {
            fetchAnimes(true);
        }
    });
});