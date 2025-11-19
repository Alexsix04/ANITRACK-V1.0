<x-app-layout>

    <!-- Header con altura fija -->
    <header class="relative w-full h-128 overflow-hidden">
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
        <div class="relative flex flex-col lg:flex-row p-6 lg:p-12 h-full items-start">
            <!-- ============================================================= -->
            <!-- =========== COLUMNA IZQUIERDA: IMAGEN + BOTONES ============== -->
            <!-- ============================================================= -->
            <div class="flex-shrink-0 mb-4 lg:mb-0 lg:mr-8 flex flex-col items-start">
                <!-- Imagen del anime -->
                <img src="{{ $anime['coverImage']['large'] }}" alt="{{ $anime['title']['romaji'] }}"
                    class="w-64 h-96 object-cover rounded-lg shadow-lg mb-4">

                @auth
                    @php
                        $isFavorite = auth()->user()->animeFavorites->where('anime_id', $anime['id'])->isNotEmpty();
                    @endphp

                    <!-- Bloque de botones -->
                    <div class="flex space-x-4">
                        {{-- ===================================================== --}}
                        {{-- ============ BOTÓN DE FAVORITOS (⭐ AJAX) ============ --}}
                        {{-- ===================================================== --}}
                        @php
                            $isFavorite = false;
                            if (auth()->check()) {
                                $isFavorite = \App\Models\AnimeFavorite::where('user_id', auth()->id())
                                    ->where('anilist_id', $anime['id']) // siempre anilist_id
                                    ->exists();
                            }
                        @endphp

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



                        {{-- ===================================================== --}}
                        {{-- ====== BOTÓN "AÑADIR A MI LISTA" (abre modal) ======= --}}
                        {{-- ===================================================== --}}

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
                    <!-- Usuario no autenticado -->
                    <div class="flex space-x-4">
                        <a href="{{ route('login') }}"
                            class="flex items-center justify-center w-12 h-12 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow-md transition"
                            title="Inicia sesión para guardar favoritos">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2
                                                                                                                                                9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                            </svg>
                        </a>

                        <button class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-md transition">
                            Añadir a mi lista
                        </button>
                    </div>
                @endauth
            </div>

            <!-- ============================================================= -->
            <!-- =========== COLUMNA DERECHA: INFORMACIÓN DEL ANIME =========== -->
            <!-- ============================================================= -->
            <div class="text-white lg:flex-1 max-w-3xl flex flex-col h-96">
                <h2 class="fit-title font-bold mb-2">{{ $anime['title']['romaji'] }}</h2>
                <h3 class="fit-title text-gray-300 mb-2">{{ $anime['title']['english'] ?? '' }}</h3>
                <p class="mb-2 text-lg"><strong>Episodios:</strong> {{ $anime['episodes'] ?? 'N/A' }}</p>

                <div
                    class="mt-4 p-6 bg-gray-800 bg-opacity-70 rounded-lg text-gray-100 text-lg leading-relaxed flex-1 overflow-y-scroll scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800">
                    {!! $anime['description'] !!}
                </div>
            </div>
        </div>
    </header>

    {{-- ===================================================== --}}
    {{-- ============ TOAST DE MENSAJE ======================== --}}
    {{-- ===================================================== --}}
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
            <div class="mt-8">
                <h3 class="text-xl font-bold mb-3 border-b border-gray-300 pb-2">Episodios</h3>

                @if (!empty($anime['streamingEpisodes']))
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach (collect($anime['streamingEpisodes']) as $episode)
                            <a href="{{ $episode['url'] }}" target="_blank"
                                class="block bg-gray-100 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition">
                                <img src="{{ $episode['thumbnail'] }}" alt="{{ $episode['title'] }}"
                                    class="w-full object-cover h-40">
                                <div class="p-3">
                                    <h4 class="text-sm font-semibold text-gray-800 truncate">
                                        {{ $episode['title'] }}</h4>
                                    <p class="text-xs text-gray-500">{{ $episode['site'] }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Episodios no disponibles.</p>
                @endif
            </div>
        </div>
        <x-add-to-list-modal :anime="$anime" />
        <script>
            window.csrfToken = '{{ csrf_token() }}';
            window.toggleFavoriteUrl = '{{ route('favorites.anime.toggle') }}';
            window.createListUrl = '{{ route('anime.list.create') }}';
            window.addToListUrl = '{{ route('anime.addToList') }}';
        </script>
        @vite('resources/js/anime/add-animes-to-favorites.js')
        @vite('resources/js/anime/add-to-list-modal.js')

    </section>

</x-app-layout>
