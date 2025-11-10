<x-app-layout>
    <div class="relative w-full min-h-screen">

        <!-- Banner de fondo -->
        @if (!empty($anime['bannerImage']))
            <div class="absolute inset-0">
                <img src="{{ $anime['bannerImage'] }}" alt="{{ $anime['title']['romaji'] }}"
                    class="w-full h-full object-cover filter brightness-50">
            </div>
        @endif

        <!-- Contenedor de contenido -->
        <div class="relative max-w-6xl mx-auto p-6">

            <!-- Datos del personaje -->
            <div
                class="flex flex-col md:flex-row items-start md:items-center mb-8 bg-gray-900 bg-opacity-70 p-6 rounded-lg shadow-lg text-white">

                <!-- Imagen del personaje -->
                <div class="flex-shrink-0">
                    <img src="{{ $character['image']['large'] ?? $character['image']['medium'] }}"
                        alt="{{ $character['name']['full'] }}"
                        class="w-48 h-64 object-cover rounded-lg shadow-md mb-4 md:mb-2">

                    <!-- Botones debajo de la imagen -->
                    <div class="flex space-x-2 justify-center mt-2">

                        {{-- ============================================= --}}
                        {{-- BOTÓN DE FAVORITO FUNCIONAL (AJAX) --}}
                        {{-- ============================================= --}}
                        @auth
                            @php
                                $isFavorite = auth()
                                    ->user()
                                    ->characterFavorites->where('anilist_id', $character['id'])
                                    ->isNotEmpty();
                            @endphp

                            <button
                                class="favoriteCharacterButton flex items-center justify-center w-10 h-10 rounded-sm shadow-md transition
        {{ $isFavorite ? 'bg-yellow-600 hover:bg-yellow-700 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' }}"
                                data-character-id="{{ $character['id'] }}"
                                data-character-anilist-id="{{ $character['id'] }}"
                                data-character-name="{{ $character['name']['full'] }}"
                                data-character-image="{{ $character['image']['large'] ?? ($character['image']['medium'] ?? '') }}"
                                data-anime-id="{{ $anime['id'] ?? '' }}" data-anime-anilist-id="{{ $anime['id'] ?? '' }}"
                                data-anime-name="{{ $anime['title']['romaji'] ?? '' }}"
                                data-anime-image="{{ $anime['coverImage']['large'] ?? '' }}"
                                data-is-favorite="{{ $isFavorite ? 'true' : 'false' }}"
                                title="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                                @if ($isFavorite)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.888 1.444 8.278L12 18.896l-7.38 3.976 1.444-8.278-6.064-5.888 8.332-1.151z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                    </svg>
                                @endif
                            </button>
                        @else
                            <!-- Si no está autenticado -->
                            <a href="{{ route('login') }}"
                                class="flex items-center justify-center w-10 h-10 bg-yellow-400 text-white rounded-sm hover:bg-yellow-500 transition"
                                title="Inicia sesión para añadir a favoritos">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.888 1.444 8.278L12 18.896l-7.38 3.976 1.444-8.278-6.064-5.888 8.332-1.151z" />
                                </svg>
                            </a>
                        @endauth

                        {{-- ============================================= --}}
                        {{-- BOTÓN DE AÑADIR A LISTA FUNCIONAL (AJAX) --}}
                        {{-- ============================================= --}}
                        @auth
                            <button id="openAddCharacterModal"
                                class="flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-md transition">
                                Añadir a mi lista
                            </button>
                        @endauth
                    </div>

                </div>

                <!-- Información del personaje -->
                <div class="md:ml-6 flex-1 space-y-2 mt-2 md:mt-0">
                    <h1 class="text-4xl font-bold">{{ $character['name']['full'] }}</h1>

                    <!-- Extra attributes (Rol, Edad, Sexo, Altura...) -->
                    @foreach ($character['extra_attributes'] as $key => $value)
                        <p><strong>{{ $key }}:</strong> {!! $value !!}</p>
                    @endforeach
                </div>
            </div>

            <!-- Descripción -->
            <div class="bg-gray-100 bg-opacity-80 p-6 rounded-lg shadow-md mb-8 max-h-96 overflow-y-auto">
                <h2 class="text-2xl font-bold mb-2 border-b border-gray-300 pb-2">Descripción</h2>
                <div class="prose max-w-none text-gray-800">
                    {!! $character['description'] ?? '<p>Descripción no disponible.</p>' !!}
                </div>
            </div>

            <!-- Apariciones en animes -->
            <div class="bg-gray-100 bg-opacity-80 p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-2xl font-bold mb-4 border-b border-gray-300 pb-2">Apariciones en Animes</h2>

                @php
                    $allAppearances = collect($character['media'])->merge($relatedMedia)->unique('node.id');
                @endphp

                @if ($allAppearances->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($allAppearances as $media)
                            @if (!empty($media['node']))
                                <a href="{{ route('animes.show', $media['node']['id']) }}"
                                    class="block bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition transform hover:-translate-y-1">
                                    <div class="flex items-center space-x-3 p-3">
                                        <img src="{{ $media['node']['coverImage']['medium'] ?? '' }}"
                                            alt="{{ $media['node']['title']['romaji'] ?? 'Sin título' }}"
                                            class="w-20 h-28 object-cover rounded-md">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-base font-semibold text-gray-800 truncate">
                                                {{ $media['node']['title']['romaji'] ?? 'Sin título' }}
                                            </h4>
                                            <p class="text-sm text-gray-500">
                                                {{ ucfirst(strtolower($media['role'] ?? '')) }}
                                            </p>
                                            @if (isset($media['relationType']))
                                                <p class="text-sm text-blue-600 font-semibold">
                                                    {{ ucfirst(strtolower($media['relationType'])) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No hay apariciones disponibles.</p>
                @endif
            </div>

            <!-- Actores de voz -->
            @if (!empty($character['voiceActors']))
                <div class="bg-gray-100 bg-opacity-80 p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold mb-4 border-b border-gray-300 pb-2">Actores de Voz</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($character['voiceActors'] as $actor)
                            <a href="{{ route('animes.voiceactors.show', ['id' => $actor['id']]) }}"
                                class="bg-white rounded-lg overflow-hidden shadow-sm p-2 flex flex-col items-center text-center hover:shadow-md hover:-translate-y-1 transform transition">
                                <img src="{{ $actor['image']['medium'] ?? ($actor['image']['large'] ?? '') }}"
                                    alt="{{ $actor['name']['full'] }}"
                                    class="w-24 h-24 object-cover rounded-full mb-2">
                                <p class="text-sm font-semibold text-gray-800">{{ $actor['name']['full'] }}</p>
                                @if (!empty($actor['languageV2']))
                                    <p class="text-xs text-gray-500">{{ $actor['languageV2'] }}</p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- ================= -->
            <!-- Sección Comentarios -->
            <!-- ================= -->
            <div class="max-w-2xl mx-auto mt-10">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Comentarios</h2>

                <!-- Mensaje de éxito -->
                @if (session('success'))
                    <div class="mb-4 rounded-md bg-green-100 p-3 text-green-700 shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Errores de validación -->
                @if ($errors->any())
                    <div class="mb-4 rounded-md bg-red-100 p-3 text-red-700 shadow-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulario de comentario -->
                <form action="{{ route('characters.comments.store') }}" method="POST" enctype="multipart/form-data"
                    class="bg-white rounded-2xl shadow-md p-5 mb-6 border border-gray-100">
                    @csrf
                    <input type="hidden" name="character_id" value="{{ $character['id'] }}">

                    <!-- Nombre usuario -->
                    @auth
                        <p class="text-gray-700 mb-3">
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
                            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none resize-none">{{ old('content') }}</textarea>
                    </div>

                    <!-- Subir imagen -->
                    <div class="mb-3">
                        <label class="block mb-1 font-medium text-gray-700">Adjuntar imagen (opcional)</label>
                        <input type="file" name="image" accept="image/*"
                            class="block text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <!-- Spoiler -->
                    <div class="mb-3 flex items-center gap-2">
                        <input type="checkbox" name="is_spoiler" id="is_spoiler" value="1"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-400">
                        <label for="is_spoiler" class="text-gray-700">Marcar como spoiler</label>
                    </div>

                    <button type="submit"
                        class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition-all shadow-sm">
                        💬 Publicar comentario
                    </button>
                </form>

                <!-- Listado de comentarios -->
                @forelse ($comments as $comment)
                    <div
                        class="bg-white rounded-xl shadow-sm p-4 mb-4 border border-gray-100 flex gap-4 hover:shadow-md transition-shadow">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            @if ($comment->user && $comment->user->avatar_url)
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
                                <strong class="text-gray-800">{{ $comment->user_name ?? 'Anónimo' }}</strong>
                                <small class="text-gray-500">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>

                            <!-- Spoiler -->
                            @if ($comment->is_spoiler)
                                <div class="p-2 bg-gray-100 rounded mb-2">
                                    <button type="button"
                                        class="show-spoiler-btn text-blue-600 hover:underline font-medium">⚠️ Spoiler —
                                        Mostrar</button>
                                    <div class="spoiler-content hidden mt-2 text-gray-700">{{ $comment->content }}
                                    </div>
                                </div>
                            @else
                                <p class="text-gray-700 mb-2 leading-relaxed">{{ $comment->content }}</p>
                            @endif

                            <!-- Imagen adjunta -->
                            @if ($comment->image)
                                <div class="mb-2">
                                    <button type="button"
                                        class="show-image-btn text-blue-600 hover:underline text-sm font-medium">
                                        📷 Ver imagen
                                    </button>
                                    <img src="{{ asset('storage/' . $comment->image) }}" alt="Imagen comentario"
                                        class="mt-2 hidden max-w-full rounded-lg border border-gray-200 shadow-sm">
                                </div>
                            @endif

                            <!-- Likes -->
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-gray-600 like-count text-sm" data-comment-id="{{ $comment->id }}">
                                    {{ $comment->likes_count }} 👍
                                </span>

                                @auth
                                    <button type="button"
                                        class="toggle-like-btn animate-like text-sm font-medium transition-colors 
                    {{ auth()->user()->hasLikedCharacter($comment) ? 'text-red-500 hover:text-red-600' : 'text-blue-500 hover:text-blue-600' }}"
                                        data-comment-id="{{ $comment->id }}">
                                        {{ auth()->user()->hasLikedCharacter($comment) ? '💔 Quitar like' : '👍 Me gusta' }}
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
                    <p class="text-gray-500 text-center">Sé el primero en comentar este personaje 📝</p>
                @endforelse
            </div>

        </div>

        <!-- JS para likes, spoilers e imagen -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {

                // Likes AJAX
                document.querySelectorAll('.toggle-like-btn').forEach(button => {
                    button.addEventListener('click', async () => {
                        const commentId = button.dataset.commentId;
                        try {
                            const response = await fetch(
                                `/character-comments/${commentId}/toggle-like`, {
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
                                button.classList.remove('text-blue-500', 'hover:text-blue-600');
                                button.classList.add('text-red-500', 'hover:text-red-600');
                            } else {
                                button.textContent = '👍 Me gusta';
                                button.classList.remove('text-red-500', 'hover:text-red-600');
                                button.classList.add('text-blue-500', 'hover:text-blue-600');
                            }
                        } catch (error) {
                            console.error('Error al procesar el like:', error);
                        }
                    });
                });

                // Mostrar/ocultar spoiler
                document.querySelectorAll('.show-spoiler-btn').forEach(btn => {
                    btn.addEventListener('click', () => btn.nextElementSibling.classList.toggle('hidden'));
                });

                // Mostrar/ocultar imagen
                document.querySelectorAll('.show-image-btn').forEach(btn => {
                    btn.addEventListener('click', () => btn.nextElementSibling.classList.toggle('hidden'));
                });
            });
        </script>


        {{-- MODAL PRINCIPAL --}}
        <div id="addCharacterModal"
            class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-all opacity-0 scale-95">
            <div
                class="bg-gray-900 text-white p-8 rounded-2xl shadow-2xl w-full max-w-3xl relative flex flex-col md:flex-row gap-6">

                <!-- Botón cerrar -->
                <button id="closeAddCharacterModal"
                    class="absolute top-4 right-4 text-gray-400 hover:text-white text-3xl font-bold">&times;</button>

                <!-- Columna izquierda: imagen + nombre del personaje -->
                <div
                    class="flex flex-col items-center justify-start w-full md:w-2/5 border-b md:border-b-0 md:border-r border-gray-700 pb-6 md:pb-0 md:pr-6">
                    <img id="modalCharacterImage" src="" alt=""
                        class="w-48 h-64 object-cover rounded-xl shadow-lg mb-4">
                    <h3 id="modalCharacterName" class="text-xl font-semibold text-center leading-tight"></h3>
                </div>

                <!-- Columna derecha: formulario -->
                <div class="flex-1">
                    <h2 class="text-2xl font-semibold mb-6">Añadir a mi lista</h2>

                    <form id="addCharacterForm" method="POST" action="{{ route('character.addToList') }}">
                        @csrf
                        <input type="hidden" name="character_anilist_id" id="character_anilist_id">
                        <input type="hidden" name="character_name" id="character_name">
                        <input type="hidden" name="character_image" id="character_image">
                        <input type="hidden" name="anime_anilist_id" id="anime_anilist_id">
                        <input type="hidden" name="anime_title" id="anime_title">
                        <input type="hidden" name="anime_image" id="anime_image">

                        <!-- Selector de lista con opción de crear nueva -->
                        <label for="list_name" class="block mb-2 text-sm text-gray-300">Selecciona una lista</label>
                        <select id="list_name" name="list_name"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 focus:ring-2 focus:ring-blue-500 text-base">
                            @foreach (auth()->user()->characterLists as $list)
                                <option value="{{ $list->name }}">{{ $list->name }}</option>
                            @endforeach
                            <option value="__new__">+ Crear nueva lista...</option>
                        </select>

                        <!-- Puntuación -->
                        <label for="score" class="block mb-2 text-sm text-gray-300">Puntuación (0-10)</label>
                        <input type="number" id="score" name="score" min="0" max="10"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 text-base">

                        <!-- Notas -->
                        <label for="notes" class="block mb-2 text-sm text-gray-300">Notas / observaciones</label>
                        <textarea id="notes" name="notes" rows="2"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 text-base" placeholder="Anota algo aquí..."></textarea>

                        <!-- Botones -->
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" id="cancelAddCharacter"
                                class="px-5 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-base">Cancelar</button>
                            <button type="submit"
                                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-white text-base font-semibold">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- SUBMODAL PARA CREAR NUEVA LISTA --}}
        <div id="createCharacterListModal"
            class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-[100] transition-all opacity-0 scale-95">
            <div class="bg-gray-900 text-white p-6 rounded-2xl shadow-xl w-full max-w-sm relative">
                <button id="closeCreateCharacterListModal"
                    class="absolute top-3 right-3 text-gray-400 hover:text-white text-2xl">&times;</button>
                <h2 class="text-xl font-semibold mb-4">Crear nueva lista</h2>

                <form id="createCharacterListForm">
                    @csrf
                    <label for="list_name_new" class="block mb-2 text-sm text-gray-300">Nombre de la lista</label>
                    <input type="text" id="list_name_new" name="name"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2 mb-3"
                        placeholder="Ej: Mis favoritos..." required>

                    <label for="is_public" class="block mb-2 text-sm text-gray-300">Visibilidad</label>
                    <select id="is_public" name="is_public"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2 mb-4">
                        <option value="1">Pública</option>
                        <option value="0">Privada</option>
                    </select>

                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancelCreateCharacterList"
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg">Cancelar</button>
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white">Crear</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- SCRIPT DE MODALES CON SUBMODAL --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const openBtn = document.getElementById('openAddCharacterModal');
                const modal = document.getElementById('addCharacterModal');
                const closeBtn = document.getElementById('closeAddCharacterModal');
                const cancelBtn = document.getElementById('cancelAddCharacter');

                const characterNameInput = document.getElementById('character_name');
                const characterImageInput = document.getElementById('character_image');
                const characterAnilistInput = document.getElementById('character_anilist_id');
                const modalName = document.getElementById('modalCharacterName');
                const modalImage = document.getElementById('modalCharacterImage');

                const listSelect = document.getElementById('list_name');

                // Submodal
                const createModal = document.getElementById('createCharacterListModal');
                const closeCreateBtn = document.getElementById('closeCreateCharacterListModal');
                const cancelCreateBtn = document.getElementById('cancelCreateCharacterList');
                const createForm = document.getElementById('createCharacterListForm');

                // Abrir modal principal
                openBtn.addEventListener('click', () => {
                    const characterData = {
                        name: "{{ $character['name']['full'] ?? '' }}",
                        image: "{{ $character['image']['large'] ?? ($character['image']['medium'] ?? '') }}",
                        anilist_id: "{{ $character['id'] }}",
                        anime_anilist_id: "{{ $anime['id'] ?? '' }}",
                        anime_title: "{{ $anime['title']['romaji'] ?? '' }}",
                        anime_image: "{{ $anime['coverImage']['large'] ?? '' }}"
                    };

                    characterNameInput.value = characterData.name;
                    characterImageInput.value = characterData.image;
                    characterAnilistInput.value = characterData.anilist_id;
                    document.getElementById('anime_anilist_id').value = characterData.anime_anilist_id;
                    document.getElementById('anime_title').value = characterData.anime_title;
                    document.getElementById('anime_image').value = characterData.anime_image;

                    modalName.textContent = characterData.name;
                    modalImage.src = characterData.image;

                    modal.classList.remove('hidden', 'opacity-0', 'scale-95');
                    modal.classList.add('flex', 'opacity-100', 'scale-100');
                });

                // Cerrar modal principal
                [closeBtn, cancelBtn].forEach(btn => {
                    btn.addEventListener('click', () => {
                        modal.classList.add('opacity-0', 'scale-95');
                        setTimeout(() => modal.classList.add('hidden'), 200);
                    });
                });

                // Abrir submodal al seleccionar "__new__"
                listSelect.addEventListener('change', e => {
                    if (e.target.value === '__new__') {
                        createModal.classList.remove('hidden', 'opacity-0', 'scale-95');
                        createModal.classList.add('flex', 'opacity-100', 'scale-100');
                        modal.classList.add('pointer-events-none');
                    }
                });

                // Cerrar submodal
                [closeCreateBtn, cancelCreateBtn].forEach(btn => {
                    btn.addEventListener('click', () => {
                        createModal.classList.add('opacity-0', 'scale-95');
                        setTimeout(() => createModal.classList.add('hidden'), 200);
                        modal.classList.remove('pointer-events-none');
                        listSelect.value = '';
                    });
                });

                // Crear nueva lista vía AJAX
                createForm.addEventListener('submit', async e => {
                    e.preventDefault();
                    const formData = new FormData(createForm);

                    const response = await fetch("{{ route('character.list.create') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const result = await response.json();
                    if (result.success && result.list) {
                        const option = new Option(result.list.name, result.list.name, true, true);
                        const createOption = listSelect.querySelector('option[value="__new__"]');
                        listSelect.insertBefore(option, createOption);

                        createModal.classList.add('opacity-0', 'scale-95');
                        setTimeout(() => createModal.classList.add('hidden'), 200);
                        modal.classList.remove('pointer-events-none');
                    } else {
                        alert(result.message || 'Error al crear la lista.');
                    }
                });
            });
        </script>

        <!-- Tailwind: animación de like -->
        <style>
            .toggle-like-btn {
                transition: transform 0.15s ease;
            }

            .toggle-like-btn.scale-110 {
                transform: scale(1.2);
            }
        </style>
    </div>
</x-app-layout>
