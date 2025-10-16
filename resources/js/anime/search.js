document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('anime-search-form');
    const grid = document.getElementById('anime-grid');
    const loader = document.getElementById('loading');
    const noResults = document.getElementById('no-results');
    const queryInput = document.getElementById('query');
    const pageTitle = document.getElementById('anime-page-title'); // contenedor del título

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

    // Función para actualizar el título y URL al cambiar filtros
    const updateState = () => {
        page = 1;
        hasNextPage = true;
        fetchAnimes(false);

        if (history.replaceState) {
            const formData = new FormData(form);
            formData.delete('filter'); // siempre ignoramos filter del home si se toca formulario
            const params = new URLSearchParams(formData).toString();
            const newUrl = params ? '/animes?' + params : '/animes';
            history.replaceState(null, '', newUrl);
        }

        if (pageTitle) pageTitle.textContent = 'Buscar Animes';
    };

    // Debounce para input de búsqueda
    let typingTimer;
    const debounceDelay = 400;
    queryInput.addEventListener('input', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(updateState, debounceDelay);
    });

    // Cambio en cualquier filtro
    form.addEventListener('change', updateState);

    // Submit del formulario
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        updateState();
    });

    // Scroll infinito
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