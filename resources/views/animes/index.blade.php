<x-app-layout>
    <x-slot name="header">
        <h2 id="anime-page-title" class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ $title ?? 'Buscar Animes' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-[95rem] mx-auto sm:px-4 lg:px-6">
            <div class="bg-white p-6 shadow-lg rounded-xl">

                <!-- FORMULARIO -->
                <form id="anime-search-form" method="GET" action="{{ route('animes.index') }}"
                    class="mb-6 flex flex-wrap gap-5 items-end">
                    @if ($filter)
                        <input type="hidden" name="filter" value="{{ $filter }}">
                    @endif

                    <!-- Nombre -->
                    <div class="flex flex-col flex-1 min-w-[200px]">
                        <label for="query" class="text-base font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" id="query" name="query" value="{{ request('query') }}"
                            placeholder="Buscar anime..."
                            class="border rounded-lg p-3 w-full text-base focus:ring-2 focus:ring-blue-400">
                    </div>

                    <!-- Género -->
                    <div class="flex flex-col flex-1 min-w-[160px]">
                        <label for="genre" class="text-base font-medium text-gray-700 mb-1">Género</label>
                        <select id="genre" name="genre"
                            class="border rounded-lg p-3 w-full text-base focus:ring-2 focus:ring-blue-400">
                            <option value="">Cualquiera</option>
                            @foreach ($genres as $g)
                                <option value="{{ $g }}" {{ request('genre') == $g ? 'selected' : '' }}>
                                    {{ $g }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Temporada -->
                    <div class="flex flex-col flex-1 min-w-[140px]">
                        <label for="season" class="text-base font-medium text-gray-700 mb-1">Temporada</label>
                        <select id="season" name="season"
                            class="border rounded-lg p-3 w-full text-base focus:ring-2 focus:ring-blue-400">
                            <option value="">Cualquiera</option>
                            <option value="WINTER" {{ request('season') == 'WINTER' ? 'selected' : '' }}>Invierno
                            </option>
                            <option value="SPRING" {{ request('season') == 'SPRING' ? 'selected' : '' }}>Primavera
                            </option>
                            <option value="SUMMER" {{ request('season') == 'SUMMER' ? 'selected' : '' }}>Verano</option>
                            <option value="FALL" {{ request('season') == 'FALL' ? 'selected' : '' }}>Otoño</option>
                        </select>
                    </div>

                    <!-- Año -->
                    <div class="flex flex-col flex-1 min-w-[130px]">
                        <label for="seasonYear" class="text-base font-medium text-gray-700 mb-1">Año</label>
                        <select id="seasonYear" name="seasonYear"
                            class="border rounded-lg p-3 w-full text-base focus:ring-2 focus:ring-blue-400">
                            <option value="">Cualquiera</option>
                            @for ($year = date('Y') + 1; $year >= 1985; $year--)
                                <option value="{{ $year }}"
                                    {{ request('seasonYear') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Formato -->
                    <div class="flex flex-col flex-1 min-w-[140px]">
                        <label for="format" class="text-base font-medium text-gray-700 mb-1">Formato</label>
                        <select id="format" name="format"
                            class="border rounded-lg p-3 w-full text-base focus:ring-2 focus:ring-blue-400">
                            <option value="">Cualquiera</option>
                            @foreach (['TV', 'MOVIE', 'OVA', 'ONA', 'SPECIAL', 'MUSIC'] as $f)
                                <option value="{{ $f }}" {{ request('format') == $f ? 'selected' : '' }}>
                                    {{ $f }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estado -->
                    <div class="flex flex-col flex-1 min-w-[140px]">
                        <label for="status" class="text-base font-medium text-gray-700 mb-1">Estado</label>
                        <select id="status" name="status"
                            class="border rounded-lg p-3 w-full text-base focus:ring-2 focus:ring-blue-400">
                            <option value="">Cualquiera</option>
                            <option value="FINISHED" {{ request('status') == 'FINISHED' ? 'selected' : '' }}>Finalizado
                            </option>
                            <option value="RELEASING" {{ request('status') == 'RELEASING' ? 'selected' : '' }}>En
                                emisión</option>
                            <option value="NOT_YET_RELEASED"
                                {{ request('status') == 'NOT_YET_RELEASED' ? 'selected' : '' }}>No estrenado</option>
                            <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>
                                Cancelado</option>
                        </select>
                    </div>
                </form>

                <!-- ===================================================== -->
                <!-- GRID DE RESULTADOS -->
                <!-- ===================================================== -->
                <div id="anime-grid"
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-7">
                    @foreach ($animes as $anime)
                        <div class="relative group rounded-xl overflow-hidden shadow-md hover:shadow-xl transition">
                            <!-- Tarjeta principal con enlace -->
                            <a href="{{ route('animes.show', $anime['id']) }}" class="block">
                                <img src="{{ $anime['coverImage']['large'] }}" alt="{{ $anime['title']['romaji'] }}"
                                    class="w-full h-64 object-cover rounded-xl">
                                <div class="p-2.5 bg-gray-100">
                                    <h3 class="text-base font-semibold truncate">{{ $anime['title']['romaji'] }}</h3>
                                    <p class="text-sm text-gray-600">
                                        ⭐ {{ $anime['averageScore'] ?? 'N/A' }} | {{ $anime['format'] ?? '' }}
                                    </p>
                                </div>
                            </a>

                            <!-- Botón para añadir a lista -->
                            <button
                                class="absolute top-2 right-2 w-8 h-8 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center text-lg font-bold opacity-0 group-hover:opacity-100 transition-shadow shadow-md"
                                data-anime-id="{{ $anime['id'] }}" data-anilist-id="{{ $anime['id'] }}"
                                data-anime-title="{{ $anime['title']['romaji'] }}"
                                data-anime-image="{{ $anime['coverImage']['large'] }}"
                                data-anime-episodes="{{ $anime['episodes'] ?? 0 }}"
                                data-anime-genres="{{ implode(', ', $anime['genres'] ?? []) }}">
                                +
                            </button>
                        </div>
                    @endforeach
                </div>

                <!-- ===================================================== -->
                <!-- MODAL PRINCIPAL: AÑADIR A MI LISTA -->
                <!-- ===================================================== -->
                <div id="addToListModal"
                    class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-all opacity-0 scale-95 p-4">
                    <div
                        class="bg-gray-900 text-white p-6 md:p-8 rounded-2xl shadow-2xl w-full max-w-4xl relative flex flex-col md:flex-row gap-6 md:gap-8 overflow-y-auto max-h-[90vh]">

                        <!-- Botón cerrar -->
                        <button id="closeAddToListModal"
                            class="absolute top-4 right-4 text-gray-400 hover:text-white text-3xl font-bold">&times;</button>

                        <!-- Columna izquierda -->
                        <div
                            class="flex flex-col items-center w-full md:w-2/5 border-b md:border-b-0 md:border-r border-gray-700 pb-6 md:pb-0 md:pr-6">
                            <img id="modalAnimeImage" src="" alt=""
                                class="w-40 h-56 md:w-56 md:h-80 object-cover rounded-xl shadow-lg mb-4">
                            <h3 id="modalAnimeTitle" class="text-lg md:text-xl font-semibold text-center leading-tight">
                            </h3>
                        </div>

                        <!-- Columna derecha -->
                        <div class="flex-1">
                            <h2 class="text-2xl md:text-3xl font-semibold mb-6">Añadir a mi lista</h2>

                            <form action="{{ route('anime.addToList') }}" method="POST" id="addToListForm">
                                @csrf
                                <input type="hidden" name="anime_id" value="">
                                <input type="hidden" name="anilist_id" value="">
                                <input type="hidden" name="anime_title" value="">
                                <input type="hidden" name="anime_image" value="">
                                <input type="hidden" name="anime_genres" value="">

                                <!-- Selector de lista -->
                                <label for="list_name" class="block mb-2 text-sm text-gray-300">Selecciona una
                                    lista</label>
                                <select id="list_name" name="list_name"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 focus:ring-2 focus:ring-blue-500 text-base">
                                    @auth
                                        @foreach (auth()->user()->animeLists as $list)
                                            <option value="{{ $list->name }}">{{ $list->name }}</option>
                                        @endforeach
                                        <option value="__new__">+ Crear nueva lista...</option>
                                    @else
                                        <option value="">Debes iniciar sesión para añadir a una lista</option>
                                    @endauth
                                </select>

                                <!-- Estado -->
                                <label for="status" class="block mb-2 text-sm text-gray-300">Estado</label>
                                <select id="status" name="status"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 focus:ring-2 focus:ring-blue-500 text-base">
                                    <option value="watching">Viendo</option>
                                    <option value="completed">Completado</option>
                                    <option value="on_hold">En pausa</option>
                                    <option value="dropped">Dropeado</option>
                                    <option value="plan_to_watch" selected>Pendiente</option>
                                </select>

                                <!-- Progreso -->
                                <label for="episode_progress" class="block mb-2 text-sm text-gray-300">Progreso de
                                    episodios</label>
                                <input type="number" id="episode_progress" name="episode_progress" min="0"
                                    max="0"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 text-base"
                                    placeholder="Ej: 5">

                                <!-- Puntuación -->
                                <label for="score" class="block mb-2 text-sm text-gray-300">Puntuación
                                    (0-10)</label>
                                <input type="number" id="score" name="score" min="0" max="10"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 text-base">

                                <!-- Notas -->
                                <label for="notes" class="block mb-2 text-sm text-gray-300">Notas /
                                    observaciones</label>
                                <textarea id="notes" name="notes" rows="2"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 text-base" placeholder="Anota algo aquí..."></textarea>

                                <!-- Rewatch -->
                                <input type="hidden" name="is_rewatch" value="0">
                                <div class="flex items-center mb-4">
                                    <input type="checkbox" id="is_rewatch" name="is_rewatch" value="1"
                                        class="w-5 h-5 text-blue-600 border-gray-300 rounded">
                                    <label for="is_rewatch" class="ml-2 text-sm text-gray-300">Rewatch</label>
                                </div>

                                <!-- Rewatch count -->
                                <label for="rewatch_count" class="block mb-2 text-sm text-gray-300">Veces
                                    rewatch</label>
                                <input type="number" id="rewatch_count" name="rewatch_count" min="0"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 text-base"
                                    placeholder="Ej: 1">

                                <!-- Botones -->
                                <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                                    <button type="button" id="cancelAddToList"
                                        class="px-5 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-base">Cancelar</button>

                                    <button type="submit"
                                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-white text-base font-semibold">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- SUB-MODAL: CREAR NUEVA LISTA -->
                <!-- ===================================================== -->
                <div id="createListModal"
                    class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-[100] transition-all opacity-0 scale-95">
                    <div class="bg-gray-900 text-white p-6 rounded-2xl shadow-xl w-full max-w-sm relative">
                        <button id="closeCreateListModal"
                            class="absolute top-3 right-3 text-gray-400 hover:text-white text-2xl">&times;</button>
                        <h2 class="text-xl font-semibold mb-4">Crear nueva lista</h2>

                        <form id="createListForm">
                            @csrf
                            <label for="list_name_new" class="block mb-2 text-sm text-gray-300">Nombre de la
                                lista</label>
                            <input type="text" id="list_name_new" name="name"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2 mb-3"
                                placeholder="Ej: En curso, Mis favoritos..." required>

                            <label for="is_public" class="block mb-2 text-sm text-gray-300">Visibilidad</label>
                            <select id="is_public" name="is_public"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2 mb-4">
                                <option value="1">Pública</option>
                                <option value="0">Privada</option>
                            </select>

                            <div class="flex justify-end space-x-2">
                                <button type="button" id="cancelCreateList"
                                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg">Cancelar</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- TOAST MODERN MESSAGE -->
                <!-- ===================================================== -->
                <div id="toastMessage"
                    class="fixed bottom-6 right-6 bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg opacity-0 pointer-events-none transition-all duration-300 z-[200]">
                    Acción realizada con éxito
                </div>

                <!-- ===================================================== -->
                <!-- SCRIPT DE MODALES DINÁMICOS CON TOASTS -->
                <!-- ===================================================== -->
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const isLoggedIn = @json(auth()->check());

                        const addModal = document.getElementById('addToListModal');
                        const createModal = document.getElementById('createListModal');
                        const toast = document.getElementById('toastMessage');

                        const closeAddBtn = document.getElementById('closeAddToListModal');
                        const cancelAddBtn = document.getElementById('cancelAddToList');
                        const closeCreateBtn = document.getElementById('closeCreateListModal');
                        const cancelCreateBtn = document.getElementById('cancelCreateList');

                        const listSelect = document.getElementById('list_name');
                        const statusSelect = document.getElementById('status');
                        const episodeInput = document.getElementById('episode_progress');

                        const animeIdInput = document.querySelector('#addToListForm input[name="anime_id"]');
                        const anilistIdInput = document.querySelector('#addToListForm input[name="anilist_id"]');
                        const animeTitleInput = document.querySelector('#addToListForm input[name="anime_title"]');
                        const animeImageInput = document.querySelector('#addToListForm input[name="anime_image"]');
                        const animeGenresInput = document.querySelector('#addToListForm input[name="anime_genres"]');
                        const modalAnimeImage = document.getElementById('modalAnimeImage');
                        const modalAnimeTitle = document.getElementById('modalAnimeTitle');

                        let currentMaxEpisodes = 0;

                        function showToast(message, color = 'green') {
                            toast.textContent = message;
                            toast.style.backgroundColor = color;
                            toast.classList.remove('opacity-0', 'pointer-events-none');
                            setTimeout(() => {
                                toast.classList.add('opacity-0', 'pointer-events-none');
                            }, 2500);
                        }

                        function updateListAndEpisodesByStatus() {
                            if (statusSelect.value === 'completed') {
                                Array.from(listSelect.options).forEach(opt => opt.disabled = opt.value !== 'Vistos');
                                listSelect.value = 'Vistos';
                                episodeInput.value = currentMaxEpisodes;
                            } else {
                                Array.from(listSelect.options).forEach(opt => opt.disabled = false);
                                const defaultOption = Array.from(listSelect.options).find(opt => opt.value === 'Pendientes');
                                listSelect.value = defaultOption ? 'Pendientes' : listSelect.options[0].value;
                                episodeInput.value = 0;
                            }
                        }

                        document.getElementById('anime-grid').addEventListener('click', (e) => {
                            const btn = e.target.closest('button[data-anime-id]');
                            if (!btn) return;

                            if (!isLoggedIn) {
                                window.location = "{{ route('login') }}";
                                return;
                            }

                            const animeId = btn.dataset.animeId || '';
                            const anilistId = btn.dataset.anilistId || '';
                            const animeTitle = btn.dataset.animeTitle || '';
                            const animeImage = btn.dataset.animeImage || '';
                            currentMaxEpisodes = parseInt(btn.dataset.animeEpisodes) || 0;

                            animeIdInput.value = animeId;
                            anilistIdInput.value = anilistId;
                            animeTitleInput.value = animeTitle;
                            animeImageInput.value = animeImage;
                            animeGenresInput.value = btn.dataset.animeGenres || '';

                            modalAnimeImage.src = animeImage;
                            modalAnimeTitle.textContent = animeTitle;

                            episodeInput.max = currentMaxEpisodes;
                            episodeInput.placeholder = currentMaxEpisodes > 0 ? `Ej: 1 - ${currentMaxEpisodes}` :
                                'Ej: 1';
                            episodeInput.value = 0;

                            addModal.classList.remove('hidden', 'opacity-0', 'scale-95');
                            addModal.classList.add('flex', 'opacity-100', 'scale-100');

                            updateListAndEpisodesByStatus();
                        });

                        statusSelect.addEventListener('change', updateListAndEpisodesByStatus);

                        [closeAddBtn, cancelAddBtn].forEach(btn => btn.addEventListener('click', () => {
                            addModal.classList.add('opacity-0', 'scale-95');
                            setTimeout(() => addModal.classList.add('hidden'), 200);
                        }));

                        listSelect.addEventListener('change', e => {
                            if (e.target.value === '__new__') {
                                createModal.classList.remove('hidden', 'opacity-0', 'scale-95');
                                createModal.classList.add('flex', 'opacity-100', 'scale-100');
                                addModal.classList.add('pointer-events-none');
                            }
                        });

                        [closeCreateBtn, cancelCreateBtn].forEach(btn => btn.addEventListener('click', () => {
                            createModal.classList.add('opacity-0', 'scale-95');
                            setTimeout(() => createModal.classList.add('hidden'), 200);
                            addModal.classList.remove('pointer-events-none');
                            listSelect.value = '';
                        }));

                        // AJAX Crear nueva lista
                        const createForm = document.getElementById('createListForm');
                        createForm.addEventListener('submit', async (e) => {
                            e.preventDefault();
                            const formData = new FormData(createForm);

                            const response = await fetch("{{ route('anime.list.create') }}", {
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
                                addModal.classList.remove('pointer-events-none');

                                showToast('Lista creada con éxito 🎉');
                            } else {
                                showToast(result.message || 'Error al crear la lista.', 'red');
                            }
                        });

                        // AJAX Añadir anime a lista
                        const addForm = document.getElementById('addToListForm');
                        addForm.addEventListener('submit', async (e) => {
                            e.preventDefault();
                            const formData = new FormData(addForm);

                            try {
                                const response = await fetch(addForm.action, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: formData
                                });

                                const text = await response.text(); // primero texto
                                let result;
                                try {
                                    result = JSON.parse(text); // intentamos parsear
                                } catch {
                                    // Si no es JSON, asumimos que se guardó correctamente
                                    showToast('Anime añadido a tu lista ✅');
                                    addModal.classList.add('opacity-0', 'scale-95');
                                    setTimeout(() => addModal.classList.add('hidden'), 200);
                                    return;
                                }

                                if (result.success) {
                                    showToast('Anime añadido a tu lista ✅');
                                    addModal.classList.add('opacity-0', 'scale-95');
                                    setTimeout(() => addModal.classList.add('hidden'), 200);
                                } else {
                                    showToast(result.message || 'Error al añadir anime.', 'red');
                                }

                            } catch (err) {
                                showToast('Error en la petición. Intenta de nuevo.', 'red');
                                console.error(err);
                            }
                        });

                    });
                </script>

            </div>
        </div>
    </div>
    </div>

    <!-- LOADER -->
    <div id="loading" class="hidden text-center py-6">
        <div class="flex justify-center items-center space-x-2">
            <div class="w-5 h-5 border-2 border-t-transparent border-indigo-600 rounded-full animate-spin"></div>
            <span class="text-gray-600">Cargando animes...</span>
        </div>
    </div>

    <!-- SIN RESULTADOS -->
    <p id="no-results" class="hidden text-gray-500 mt-4">No se encontraron resultados.</p>

</x-app-layout>
