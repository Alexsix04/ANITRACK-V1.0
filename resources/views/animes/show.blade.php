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
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-4 mb-4 border border-gray-100 dark:border-gray-700 flex gap-4 transition-transform hover:scale-[1.02]">

                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <a href="{{ $comment->user ? route('profile.show', $comment->user->id) : '#' }}"
                                    class="block">
                                    <img src="{{ $comment->user && $comment->user->avatar_url ? $comment->user->avatar_url : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user_name ?? 'Anónimo') . '&background=0D8ABC&color=fff&bold=true' }}"
                                        alt="{{ $comment->user_name ?? 'Anónimo' }}"
                                        class="w-12 h-12 rounded-full object-cover border border-gray-200 dark:border-gray-600 shadow-sm hover:opacity-80 transition">
                                </a>
                            </div>

                            <!-- Contenido -->
                            <div class="flex-1 flex flex-col">
                                <!-- Nombre y fecha -->
                                <div class="flex items-center justify-between mb-2">
                                    <a href="{{ $comment->user ? route('profile.show', $comment->user->id) : '#' }}"
                                        class="text-gray-900 dark:text-gray-100 font-semibold hover:underline">
                                        {{ $comment->user->name ?? ($comment->user_name ?? 'Anónimo') }}
                                    </a>
                                    <small
                                        class="text-gray-400 dark:text-gray-300 text-sm">{{ $comment->created_at->diffForHumans() }}</small>
                                </div>

                                <!-- Spoiler -->
                                @if ($comment->is_spoiler)
                                    <div class="p-2 bg-yellow-50 dark:bg-yellow-900 rounded-lg mb-2">
                                        <button type="button"
                                            class="show-spoiler-btn text-yellow-600 dark:text-yellow-400 hover:underline font-medium">
                                            ⚠️ Contenido oculto por spoiler. Mostrar
                                        </button>
                                        <div class="spoiler-content hidden mt-2 text-gray-800 dark:text-gray-200">
                                            {{ $comment->content }}
                                        </div>
                                    </div>
                                @else
                                    <p class="text-gray-700 dark:text-gray-200 mb-2 leading-relaxed">
                                        {{ $comment->content }}</p>
                                @endif

                                <!-- Imagen adjunta -->
                                @if ($comment->image)
                                    <div class="mb-2">
                                        <button type="button"
                                            class="show-image-btn text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                                            Ver imagen
                                        </button>
                                        <img src="{{ asset('storage/' . $comment->image) }}" alt="Imagen comentario"
                                            class="mt-2 hidden max-w-full rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm">
                                    </div>
                                @endif

                                <!-- Likes: solo corazón -->
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-gray-500 dark:text-gray-400 like-count text-sm"
                                        data-comment-id="{{ $comment->id }}">
                                        {{ $comment->likes_count }}
                                    </span>

                                    @auth
                                        <button type="button"
                                            class="toggle-like-btn flex items-center transition-colors
                            {{ auth()->user()->hasLiked($comment) ? 'text-red-500 hover:text-red-600' : 'text-gray-400 hover:text-blue-500' }}"
                                            data-comment-id="{{ $comment->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                                fill="{{ auth()->user()->hasLiked($comment) ? 'currentColor' : 'none' }}"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                            </svg>
                                        </button>
                                    @else
                                        <button type="button"
                                            class="flex items-center text-gray-400 hover:text-blue-500 transition-colors"
                                            data-comment-id="{{ $comment->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                            </svg>
                                        </button>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center">Sé el primero en comentar este anime 📝
                        </p>
                    @endforelse

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
                                        if (likeCount) likeCount.textContent = data.likes_count;

                                        button.classList.add('scale-110');
                                        setTimeout(() => button.classList.remove('scale-110'), 150);

                                        const svg = button.querySelector('svg');
                                        if (data.liked) {
                                            button.classList.replace('text-gray-400', 'text-red-500');
                                            svg.setAttribute('fill', 'currentColor');
                                        } else {
                                            button.classList.replace('text-red-500', 'text-gray-400');
                                            svg.setAttribute('fill', 'none');
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
                            transition: transform 0.15s ease, color 0.2s ease;
                        }

                        .toggle-like-btn.scale-110 {
                            transform: scale(1.2);
                        }
                    </style>

                </div>
            </div>
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
