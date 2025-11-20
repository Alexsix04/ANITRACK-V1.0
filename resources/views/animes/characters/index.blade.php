<x-app-layout>
    <!-- Header con altura adaptable -->
    <header class="relative w-full h-auto sm:h-[28rem] md:h-[32rem] lg:h-[36rem] overflow-hidden">
        <!-- Fondo -->
        <div class="absolute inset-0">
            @if (!empty($anime['bannerImage']))
                <img src="{{ $anime['bannerImage'] }}" alt="{{ $anime['title']['romaji'] }}"
                    class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-white"></div>
            @endif
            <div class="absolute inset-0 bg-black opacity-20"></div>
        </div>

        <!-- Contenido principal -->
        <div class="relative flex flex-col lg:flex-row p-4 sm:p-6 lg:p-12 gap-6 lg:gap-12 h-full">

            <!-- Columna izquierda: imagen + botones -->
            <div class="flex-shrink-0 flex flex-col items-start w-full lg:w-auto mb-4 lg:mb-0 order-1 lg:order-1">
                <!-- Imagen responsiva completa -->
                <img src="{{ $anime['coverImage']['large'] }}" alt="{{ $anime['title']['romaji'] }}"
                    class="w-full sm:w-64 lg:w-64 h-auto sm:h-96 lg:h-96 object-contain rounded-lg shadow-lg mb-4">

                @auth
                    @php
                        $isFavorite = \App\Models\AnimeFavorite::where('user_id', auth()->id())
                            ->where('anilist_id', $anime['id'])
                            ->exists();
                    @endphp
                    <div class="flex flex-wrap gap-2">
                        <button
                            class="favoriteButton flex items-center justify-center w-12 h-12 rounded-lg shadow-md transition
                {{ $isFavorite ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-yellow-500 hover:bg-yellow-600' }} text-white"
                            data-anilist-id="{{ $anime['id'] }}" data-anime-title="{{ $anime['title']['romaji'] }}"
                            data-anime-image="{{ $anime['coverImage']['large'] }}"
                            data-is-favorite="{{ $isFavorite ? 'true' : 'false' }}"
                            title="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                            @if ($isFavorite)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.782
                                                     1.4 8.172L12 18.896l-7.334 3.868
                                                     1.4-8.172-5.934-5.782 8.2-1.192z" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2
                                                     9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                </svg>
                            @endif
                        </button>
                        <button id="openAddToListModal"
                            class="flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-md transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Añadir a mi lista
                        </button>
                    </div>
                @else
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('login') }}"
                            class="flex items-center justify-center w-12 h-12 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow-md transition"
                            title="Inicia sesión para guardar favoritos">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2
                                                 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                            </svg>
                        </a>
                        <button class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-md transition">
                            Añadir a mi lista
                        </button>
                    </div>
                @endauth
            </div>

            <!-- Columna derecha: títulos + descripción -->
            <div class="text-white lg:flex-1 max-w-full lg:max-w-3xl flex flex-col order-2 lg:order-2">

                <!-- Títulos + episodios con más espacio -->
                <div class="mb-6 p-6 sm:p-8 bg-gray-800 bg-opacity-70 rounded-lg flex-shrink-0">
                    <h2 class="fittext-one-line font-bold mb-4 overflow-visible text-2xl sm:text-3xl lg:text-4xl">
                        {{ $anime['title']['romaji'] }}
                    </h2>
                    <h3 class="fittext-one-line text-gray-300 mb-3 overflow-visible text-lg sm:text-xl lg:text-2xl">
                        {{ $anime['title']['english'] ?? '' }}
                    </h3>
                    <p class="mb-0 text-base sm:text-lg"><strong>Episodios:</strong> {{ $anime['episodes'] ?? 'N/A' }}
                    </p>
                </div>

                <!-- Descripción -->
                <div
                    class="mt-4 p-4 sm:p-6 bg-gray-800 bg-opacity-70 rounded-lg text-gray-100 text-base sm:text-lg leading-relaxed flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800">
                    {!! $anime['description'] !!}
                </div>

            </div>
        </div>
    </header>

    <!-- Toast de mensaje -->
    <div id="toast"
        class="fixed top-5 right-5 bg-gray-900 text-white px-4 py-2 rounded-lg shadow-lg opacity-0 transition-opacity duration-500 pointer-events-none z-50">
    </div>

    {{-- ===================================================== --}}
    {{-- ============ SECCIÓN PRINCIPAL ====================== --}}
    {{-- ===================================================== --}}
    <section class="w-full p-4 md:p-6 mt-8 flex flex-col md:flex-row gap-6 md:gap-8">

        <x-anime.details-sidebar :anime="$anime" />

        <div class="flex-1">
            <h2 class="text-2xl font-bold mb-4">Secciones</h2>

            <x-anime.tabs :animeId="$anime['id']" />

            <!-- Contenido por defecto: Vista General -->
            <div class="text-gray-700 space-y-8">
                <div class="p-6">
                    <h1 class="text-3xl font-bold mb-6">Personajes</h1>

                    @if (empty($characters))
                        <p class="text-gray-500">No se encontraron personajes para este anime.</p>
                    @else
                        <div id="characters-container"
                            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach ($characters as $char)
                                <a href="{{ route('animes.characters.show', ['anime' => $anime['id'], 'character' => $char['node']['id']]) }}"
                                    class="flex items-center bg-gray-100 p-3 rounded-lg shadow-sm hover:shadow-md transition">
                                    <img src="{{ $char['node']['image']['medium'] }}"
                                        alt="{{ $char['node']['name']['full'] }}"
                                        class="w-16 h-20 object-cover rounded-md mr-3 flex-shrink-0">

                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-semibold text-gray-800 truncate">
                                            {{ $char['node']['name']['full'] }}</h3>
                                        <p class="text-xs text-gray-500">{{ ucfirst(strtolower($char['role'])) }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div id="loading" class="text-center text-gray-500 mt-4 hidden">
                            Cargando más personajes...
                        </div>
                    @endif
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    let page = 1;
                    let loading = false;
                    let hasMore = true;
                    const container = document.getElementById('characters-container');
                    const loadingText = document.getElementById('loading');
                    const animeId = "{{ $anime['id'] }}";

                    async function loadMoreCharacters() {
                        if (loading || !hasMore) return;
                        loading = true;
                        loadingText.classList.remove('hidden');

                        try {
                            const url = `{{ route('animes.characters.index', ':anime') }}?page=${page + 1}`
                                .replace(':anime', animeId);

                            const response = await fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            const data = await response.json();

                            if (!data.html || !data.hasMore) {
                                hasMore = false;
                            } else {
                                container.insertAdjacentHTML('beforeend', data.html);
                                page++;
                            }
                        } catch (error) {
                            console.error('Error cargando más personajes:', error);
                        } finally {
                            loading = false;
                            loadingText.classList.add('hidden');
                        }
                    }

                    window.addEventListener('scroll', () => {
                        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
                            loadMoreCharacters();
                        }
                    });
                });
            </script>
            <x-add-to-list-modal :anime="$anime" />
            <script>
                window.isLoggedIn = @json(auth()->check());
                window.csrfToken = '{{ csrf_token() }}';
                window.toggleFavoriteUrl = '{{ route('favorites.anime.toggle') }}';
                window.createListUrl = '{{ route('anime.list.create') }}';
                window.addToListUrl = '{{ route('anime.addToList') }}';
                window.loginUrl = '{{ route('login') }}';
            </script>

            @vite('resources/js/anime/add-animes-to-favorites.js')
            @vite('resources/js/anime/add-to-list-modal.js')
    </section>
</x-app-layout>
