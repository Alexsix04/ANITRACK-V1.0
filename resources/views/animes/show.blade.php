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
                        <button id="favoriteButton" data-anime-id="{{ $anime['id'] }}"
                            data-anime-title="{{ $anime['title']['romaji'] }}"
                            data-anime-image="{{ $anime['coverImage']['large'] }}"
                            data-is-favorite="{{ $isFavorite ? 'true' : 'false' }}"
                            class="flex items-center justify-center w-12 h-12 rounded-lg shadow-md transition
                                   {{ $isFavorite ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-yellow-500 hover:bg-yellow-600' }} text-white"
                            title="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">

                            @if ($isFavorite)
                                {{-- Ícono relleno ⭐ --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.782
                                                        1.4 8.172L12 18.896l-7.334 3.868
                                                        1.4-8.172-5.934-5.782 8.2-1.192z" />
                                </svg>
                            @else
                                {{-- Ícono vacío ☆ --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2
                                                    9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                </svg>
                            @endif
                        </button>

                        {{-- ===================================================== --}}
                        {{-- ====== BOTÓN "AÑADIR A MI LISTA" (sin AJAX) ========= --}}
                        {{-- ===================================================== --}}

                        <form action="{{ route('anime.addToList') }}" method="POST" class="flex items-center space-x-2">
                            @csrf
                            <input type="hidden" name="anime_id" value="{{ $anime['id'] }}">
                            <input type="hidden" name="anime_title" value="{{ $anime['title']['romaji'] }}">
                            <input type="hidden" name="anime_image" value="{{ $anime['coverImage']['large'] }}">

                            <select name="list_name"
                                class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Pendientes">Pendientes</option>
                                <option value="Vistos">Vistos</option>
                            </select>

                            <button type="submit"
                                class="flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-md transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Añadir a mi lista
                            </button>
                        </form>


                        {{-- Mensaje de éxito --}}
                        @if (session('success'))
                            <div class="mt-3 bg-green-100 text-green-800 px-4 py-2 rounded-lg shadow">
                                {{ session('success') }}
                            </div>
                        @endif


                    </div>
                @else
                    <!-- Usuario no autenticado -->
                    <div class="flex space-x-4">
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

            <!-- ============================================================= -->
            <!-- =========== COLUMNA DERECHA: INFORMACIÓN DEL ANIME =========== -->
            <!-- ============================================================= -->
            <div class="text-white lg:flex-1 max-w-3xl flex flex-col h-96">
                <h2 class="text-4xl lg:text-5xl font-bold mb-2">{{ $anime['title']['romaji'] }}</h2>
                <h3 class="text-2xl mb-2 text-gray-300">{{ $anime['title']['english'] ?? '' }}</h3>
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
    {{-- ============ SCRIPT PARA FAVORITOS (AJAX) ============ --}}
    {{-- ===================================================== --}}
    @auth
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const button = document.getElementById('favoriteButton');
                const toast = document.getElementById('toast');

                async function showToast(message, color = 'bg-green-600') {
                    toast.textContent = message;
                    toast.className =
                        `fixed top-5 right-5 ${color} text-white px-4 py-2 rounded-lg shadow-lg opacity-0 transition-opacity duration-500 pointer-events-none z-50`;
                    setTimeout(() => toast.classList.add('opacity-100'), 10);
                    setTimeout(() => toast.classList.remove('opacity-100'), 3000);
                }

                button.addEventListener('click', async () => {
                    const isFavorite = button.dataset.isFavorite === 'true';
                    const animeId = button.dataset.animeId;
                    const animeTitle = button.dataset.animeTitle;
                    const animeImage = button.dataset.animeImage;

                    try {
                        const response = await fetch('{{ route('favorites.anime.toggle') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                anime_id: animeId,
                                anime_title: animeTitle,
                                anime_image: animeImage
                            })
                        });

                        const data = await response.json();

                        // Actualizar el botón e ícono según respuesta
                        if (data.status === 'added') {
                            button.dataset.isFavorite = 'true';
                            button.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                            button.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
                            button.title = 'Quitar de favoritos';
                            button.innerHTML = `
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.782
                                        1.4 8.172L12 18.896l-7.334 3.868
                                        1.4-8.172-5.934-5.782 8.2-1.192z"/>
                                </svg>`;
                            showToast('✅ Anime añadido a favoritos', 'bg-green-600');
                        } else if (data.status === 'removed') {
                            button.dataset.isFavorite = 'false';
                            button.classList.remove('bg-yellow-600', 'hover:bg-yellow-700');
                            button.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
                            button.title = 'Agregar a favoritos';
                            button.innerHTML = `
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 
                                        9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                </svg>`;
                            showToast('❌ Anime eliminado de favoritos', 'bg-red-600');
                        }

                    } catch (error) {
                        console.error('Error:', error);
                        showToast('⚠️ Error al procesar la solicitud', 'bg-yellow-600');
                    }
                });
            });
        </script>
    @endauth
    <!-- Sección adicional ocupando todo el ancho -->
    <section class="w-full p-6 mt-8 flex gap-8">
        <!-- Columna izquierda: info del anime disponible -->
        <div class="w-64 flex-shrink-0 bg-gray-100 p-6 rounded-lg shadow-md space-y-4" style="align-self: flex-start;">
            <!-- Aquí eliminamos sticky y limitamos altura -->
            <h2 class="text-xl font-bold mb-4">Detalles del Anime</h2>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">FORMAT</h3>
                <p class="text-gray-700 text-sm">{{ $anime['format'] ?? 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">EPISODES</h3>
                <p class="text-gray-700 text-sm">{{ $anime['episodes'] ?? 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">DURATION</h3>
                <p class="text-gray-700 text-sm">{{ $anime['duration'] ? $anime['duration'] . ' mins' : 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">STATUS</h3>
                <p class="text-gray-700 text-sm">{{ $anime['status'] ?? 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">SEASON</h3>
                <p class="text-gray-700 text-sm">{{ $anime['season'] ?? 'N/A' }} {{ $anime['seasonYear'] ?? '' }}</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">START DATE</h3>
                <p class="text-gray-700 text-sm">
                    {{ $anime['startDate']['year'] ?? 'N/A' }}-
                    {{ $anime['startDate']['month'] ?? 'N/A' }}-
                    {{ $anime['startDate']['day'] ?? 'N/A' }}
                </p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">END DATE</h3>
                <p class="text-gray-700 text-sm">
                    {{ $anime['endDate']['year'] ?? 'N/A' }}-
                    {{ $anime['endDate']['month'] ?? 'N/A' }}-
                    {{ $anime['endDate']['day'] ?? 'N/A' }}
                </p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">AVERAGE SCORE</h3>
                <p class="text-gray-700 text-sm">{{ $anime['averageScore'] ?? 'N/A' }}%</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">POPULARITY</h3>
                <p class="text-gray-700 text-sm">{{ $anime['popularity'] ?? 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">GENRES</h3>
                @if (!empty($anime['genres']))
                    <div class="text-gray-700 text-sm space-y-1">
                        @foreach ($anime['genres'] as $genre)
                            <p>{{ $genre }}</p>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-700 text-sm">N/A</p>
                @endif
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">SOURCE</h3>
                <p class="text-gray-700 text-sm">{{ $anime['source'] ?? 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">STUDIOS</h3>
                @if (!empty($anime['studios']['edges']))
                    <div class="text-gray-700 text-sm space-y-1">
                        @foreach ($anime['studios']['edges'] as $studio)
                            <p>{{ $studio['node']['name'] }}</p>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-700 text-sm">N/A</p>
                @endif
            </div>
        </div>

        <!-- Columna derecha: menú de secciones -->
        <div class="flex-1">
            <h2 class="text-2xl font-bold mb-4">Secciones</h2>

            <!-- Menú de botones tipo tabs -->
            <div class="flex space-x-2 mb-6 border-b border-gray-300 overflow-x-auto">
                @foreach (['General', 'Personajes', 'Staff', 'Episodios', 'Comentarios'] as $section)
                    @if ($section === 'General')
                        <!-- Botón funcional que lleva a la vista general del anime -->
                        <a href="{{ route('animes.show', ['id' => $anime['id']]) }}"
                            class="py-2 px-4 font-semibold border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors rounded-t">
                            {{ $section }}
                        </a>
                    @elseif ($section === 'Personajes')
                        <!-- Botón funcional que lleva a la vista de personajes -->
                        <a href="{{ route('animes.characters.index', ['anime' => $anime['id']]) }}"
                            class="py-2 px-4 font-semibold border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors rounded-t">
                            {{ $section }}
                        </a>
                    @elseif ($section === 'Staff')
                        <!-- Botón funcional que lleva a la vista de personajes -->
                        <a href="{{ route('animes.staff.index', ['anime' => $anime['id']]) }}"
                            class="py-2 px-4 font-semibold border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors rounded-t">
                            {{ $section }}
                        </a>
                    @elseif ($section === 'Episodios')
                        <!-- Botón funcional que lleva a la vista de personajes -->
                        <a href="{{ route('animes.episodes.index', ['anime' => $anime['id']]) }}"
                            class="py-2 px-4 font-semibold border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors rounded-t">
                            {{ $section }}
                        </a>
                    @else
                        <!-- Botones estáticos -->
                        <button
                            class="py-2 px-4 font-semibold border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors rounded-t">
                            {{ $section }}
                        </button>
                    @endif
                @endforeach
            </div>



            <!-- Contenido por defecto: Vista General -->
            <div class="text-gray-700 space-y-8">
                <!-- Relaciones -->
                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-3 border-b border-gray-300 pb-2">Relaciones</h3>

                    @php
                        // Tipos permitidos y orden
                        $allowedTypes = ['PREQUEL', 'SEQUEL', 'ALTERNATIVE'];

                        // Filtramos solo relaciones tipo ANIME permitidas
                        $animeRelations = collect($anime['relations']['edges'] ?? [])->filter(
                            fn($r) => ($r['node']['type'] ?? null) === 'ANIME' &&
                                in_array(strtoupper($r['relationType'] ?? ''), $allowedTypes),
                        );

                        // Ordenar según el orden deseado
                        $animeRelations = $animeRelations->sortBy(
                            fn($r) => array_search(strtoupper($r['relationType']), $allowedTypes),
                        );
                    @endphp

                    @if ($animeRelations->isNotEmpty())
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach ($animeRelations as $relation)
                                <a href="{{ route('animes.show', $relation['node']['id']) }}"
                                    class="block bg-gray-100 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
                                    <div class="flex items-center space-x-3 p-3">
                                        <img src="{{ $relation['node']['coverImage']['medium'] ?? '' }}"
                                            alt="{{ $relation['node']['title']['romaji'] ?? 'Sin título' }}"
                                            class="w-20 h-28 object-cover rounded-md">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-base font-semibold text-gray-800 truncate">
                                                {{ $relation['node']['title']['romaji'] ?? 'Sin título' }}
                                            </h4>
                                            <p class="text-sm text-gray-500">
                                                {{ ucfirst(strtolower($relation['relationType'] ?? '')) }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No hay relaciones disponibles.</p>
                    @endif
                </div>

                <!-- Personajes (resumen 10, MAIN primero) -->
                <div>
                    <h3 class="text-xl font-bold mb-4 border-b border-gray-300 pb-2">Personajes Destacados</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-3">
                        @php
                            $characters = $anime['characters']['edges'] ?? [];
                            $mainCharacters = array_filter($characters, fn($c) => $c['role'] === 'MAIN');
                            $otherCharacters = array_filter($characters, fn($c) => $c['role'] !== 'MAIN');
                            $charactersToShow = array_slice(array_merge($mainCharacters, $otherCharacters), 0, 10);
                        @endphp
                        @foreach ($charactersToShow as $char)
                            <a href="{{ route('animes.characters.show', ['anime' => $anime['id'], 'character' => $char['node']['id']]) }}"
                                class="block">
                                <div
                                    class="flex items-center bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-300 p-2">
                                    <img src="{{ $char['node']['image']['medium'] }}"
                                        alt="{{ $char['node']['name']['full'] }}"
                                        class="w-20 h-20 object-contain rounded-lg flex-shrink-0">
                                    <div class="ml-3 flex flex-col justify-center">
                                        <p class="text-sm font-semibold">{{ $char['node']['name']['full'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $char['role'] }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Staff (resumen 5) -->
                <div class="mt-6">
                    <h3 class="text-xl font-bold mb-4 border-b border-gray-300 pb-2">Staff Destacado</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-3">
                        @php
                            $staffList = $anime['staff']['edges'] ?? [];
                            $staffToShow = array_slice($staffList, 0, 6);
                        @endphp
                        @foreach ($staffToShow as $staff)
                            <a href="{{ route('animes.staff.show', ['anime' => $anime['id'], 'staff' => $staff['node']['id']]) }}"
                                class="block">
                                <div
                                    class="flex items-center bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-300 p-2">
                                    <img src="{{ $staff['node']['image']['medium'] }}"
                                        alt="{{ $staff['node']['name']['full'] }}"
                                        class="w-20 h-20 object-contain rounded-lg flex-shrink-0">
                                    <div class="ml-3 flex flex-col justify-center">
                                        <p class="text-sm font-semibold">{{ $staff['node']['name']['full'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $staff['role'] }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                <!-- Trailer -->
                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-3 border-b border-gray-300 pb-2">Trailer</h3>

                    @if (!empty($anime['trailer']) && $anime['trailer']['site'] === 'youtube')
                        <div class="w-full max-w-3xl rounded-lg overflow-hidden shadow-md">
                            <iframe src="https://www.youtube.com/embed/{{ $anime['trailer']['id'] }}"
                                title="YouTube trailer" frameborder="0" allowfullscreen
                                class="w-full h-72 rounded-lg">
                            </iframe>
                        </div>
                    @elseif(!empty($anime['trailer']) && $anime['trailer']['site'] === 'dailymotion')
                        <div class="w-full max-w-3xl rounded-lg overflow-hidden shadow-md">
                            <iframe src="https://www.dailymotion.com/embed/video/{{ $anime['trailer']['id'] }}"
                                title="Dailymotion trailer" frameborder="0" allowfullscreen
                                class="w-full h-72 rounded-lg">
                            </iframe>
                        </div>
                    @else
                        <p class="text-gray-500">Trailer no disponible.</p>
                    @endif
                </div>



                <!-- Episodios -->
                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-3 border-b border-gray-300 pb-2">Episodios</h3>

                    @if (!empty($anime['streamingEpisodes']))
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach (collect($anime['streamingEpisodes'])->take(4) as $episode)
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
                <!-- =============== -->
                <!-- Sección Comentarios -->
                <!-- =============== -->
                <div class="max-w-2xl mx-auto mt-10">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Comentarios</h2>

                    <!-- Mensaje de éxito -->
                    @if (session('success'))
                        <div class="mb-4 rounded-md bg-green-100 p-3 text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Errores de validación -->
                    @if ($errors->any())
                        <div class="mb-4 rounded-md bg-red-100 p-3 text-red-700">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Formulario de comentario -->
                    <form action="{{ route('anime-comments.store') }}" method="POST" enctype="multipart/form-data"
                        class="bg-white rounded-xl shadow-md p-4 mb-6 border border-gray-100">
                        @csrf
                        <input type="hidden" name="anime_id" value="{{ $anime['id'] }}">

                        <!-- Nombre usuario -->
                        @auth
                            <p class="text-gray-700 mb-2">
                                Comentando como:
                                <span class="font-semibold text-blue-600">{{ auth()->user()->name }}</span>
                            </p>
                            <input type="hidden" name="user_name" value="{{ auth()->user()->name }}">
                        @else
                            <div class="mb-3">
                                <input type="text" name="user_name" placeholder="Tu nombre"
                                    class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                                    value="{{ old('user_name') }}">
                            </div>
                        @endauth

                        <div class="mb-3">
                            <textarea name="content" placeholder="Escribe algo..." rows="3"
                                class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">{{ old('content') }}</textarea>
                        </div>

                        <!-- Subir imagen -->
                        <div class="mb-3">
                            <label class="block mb-1">Adjuntar imagen (opcional)</label>
                            <input type="file" name="image" accept="image/*">
                        </div>

                        <!-- Spoiler -->
                        <div class="mb-3 flex items-center gap-2">
                            <input type="checkbox" name="is_spoiler" id="is_spoiler" value="1">
                            <label for="is_spoiler">Marcar como spoiler</label>
                        </div>

                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            💬 Publicar comentario
                        </button>
                    </form>

                    <!-- Listado de comentarios -->
                    @forelse ($comments as $comment)
                        <div class="bg-white rounded-xl shadow-sm p-4 mb-4 border border-gray-100 flex gap-3">
                            <!-- Avatar -->
                            <div>
                                @if ($comment->user)
                                    <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}"
                                        class="w-12 h-12 rounded-full object-cover border border-gray-200 shadow-sm">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->user_name ?? 'Anónimo') }}&background=0D8ABC&color=fff&bold=true"
                                        alt="{{ $comment->user_name ?? 'Anónimo' }}"
                                        class="w-12 h-12 rounded-full object-cover border border-gray-200 shadow-sm">
                                @endif
                            </div>

                            <!-- Contenido -->
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <strong class="text-gray-800">
                                        {{ $comment->user->name ?? ($comment->user_name ?? 'Anónimo') }}
                                    </strong>
                                    <small class="text-gray-500">{{ $comment->created_at->diffForHumans() }}</small>
                                </div>

                                <!-- Spoiler -->
                                @if ($comment->is_spoiler)
                                    <div class="p-2 bg-gray-200 rounded mb-2">
                                        <button type="button" class="show-spoiler-btn text-blue-600 hover:underline">
                                            ⚠️ Contenido oculto por spoiler. Mostrar
                                        </button>
                                        <div class="spoiler-content hidden mt-2">{{ $comment->content }}</div>
                                    </div>
                                @else
                                    <p class="text-gray-700 mb-2">{{ $comment->content }}</p>
                                @endif

                                <!-- Imagen adjunta -->
                                @if ($comment->image)
                                    <div class="mb-2">
                                        <button type="button" class="show-image-btn text-blue-600 hover:underline">📷
                                            Ver imagen</button>
                                        <img src="{{ asset('storage/' . $comment->image) }}" alt="Imagen comentario"
                                            class="mt-2 hidden max-w-full rounded">
                                    </div>
                                @endif

                                <!-- Likes -->
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-600 like-count" data-comment-id="{{ $comment->id }}">
                                        {{ $comment->likes_count }} 👍
                                    </span>

                                    @auth
                                        <button type="button"
                                            class="toggle-like-btn animate-like text-sm font-medium transition-colors 
                                {{ auth()->user()->hasLiked($comment) ? 'text-red-500 hover:text-red-600' : 'text-blue-500 hover:text-blue-600' }}"
                                            data-comment-id="{{ $comment->id }}">
                                            {{ auth()->user()->hasLiked($comment) ? '💔 Quitar like' : '👍 Me gusta' }}
                                        </button>
                                    @else
                                        <button type="button"
                                            class="toggle-like-btn text-blue-500 hover:text-blue-600 text-sm font-medium"
                                            data-comment-id="{{ $comment->id }}">
                                            👍 Me gusta
                                        </button>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Sé el primero en comentar este anime 📝</p>
                    @endforelse
                </div>

                <!-- JS -->
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        // Likes AJAX
                        document.querySelectorAll('.toggle-like-btn').forEach(button => {
                            button.addEventListener('click', async () => {
                                const commentId = button.dataset.commentId;
                                try {
                                    const response = await fetch(
                                        `/anime-comments/${commentId}/toggle-like`, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            }
                                        });
                                    if (!response.ok) {
                                        if (response.status === 403) alert(
                                            'Debes iniciar sesión para dar like.');
                                        return;
                                    }
                                    const data = await response.json();
                                    const likeCount = document.querySelector(
                                        `.like-count[data-comment-id="${commentId}"]`);
                                    if (likeCount) likeCount.textContent = `${data.likes_count} 👍`;

                                    button.classList.add('scale-110');
                                    setTimeout(() => button.classList.remove('scale-110'), 150);

                                    if (data.liked) {
                                        button.textContent = '💔 Quitar like';
                                        button.classList.replace('text-blue-500', 'text-red-500');
                                    } else {
                                        button.textContent = '👍 Me gusta';
                                        button.classList.replace('text-red-500', 'text-blue-500');
                                    }
                                } catch (error) {
                                    console.error('Error al procesar el like:', error);
                                }
                            });
                        });

                        // Mostrar/ocultar spoiler
                        document.querySelectorAll('.show-spoiler-btn').forEach(btn => {
                            btn.addEventListener('click', () => {
                                const content = btn.nextElementSibling;
                                content.classList.toggle('hidden');
                            });
                        });

                        // Mostrar/ocultar imagen
                        document.querySelectorAll('.show-image-btn').forEach(btn => {
                            btn.addEventListener('click', () => {
                                const img = btn.nextElementSibling;
                                img.classList.toggle('hidden');
                            });
                        });
                    });
                </script>

                <style>
                    .toggle-like-btn {
                        transition: transform 0.15s ease;
                    }

                    .toggle-like-btn.scale-110 {
                        transform: scale(1.2);
                    }
                </style>

                <script>
                    // Obtener elementos
                    const form = document.getElementById('commentForm');
                    const input = document.getElementById('commentInput');
                    const commentsList = document.getElementById('commentsList');

                    // Manejar envío del formulario
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        const commentText = input.value.trim();
                        if (!commentText) return;

                        // Crear nuevo comentario temporal
                        const newComment = document.createElement('div');
                        newComment.classList.add('bg-gray-100', 'p-3', 'rounded-md', 'shadow');
                        newComment.innerHTML = `
            <p class="text-sm font-semibold">Usuario Anónimo</p>
            <p class="text-sm text-gray-700 mt-1">${commentText}</p>
        `;

                        // Añadir al listado
                        commentsList.prepend(newComment);

                        // Limpiar textarea
                        input.value = '';
                    });
                </script>
            </div>
        </div>

    </section>

</x-app-layout>
