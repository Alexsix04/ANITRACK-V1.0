<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Listas públicas</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Sección Anime --}}
            <div>
                <h3 class="text-2xl font-bold mb-4 text-blue-600 hover:underline cursor-pointer"
                    onclick="window.location='{{ route('listas.anime.public') }}'">
                    Últimas listas de Anime
                </h3>

                @if ($publicAnimeLists->isEmpty())
                    <p class="text-gray-500 text-center py-6">No hay listas públicas de anime disponibles.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($publicAnimeLists as $list)
                            <div class="bg-white rounded-2xl shadow-lg p-4 hover:shadow-xl transition cursor-pointer relative group"
                                data-modal-target="#listModal-{{ $list->id }}">

                                {{-- Contenedor conjunto de guardar + like --}}
                                <div class="absolute top-3 right-3 flex items-center gap-3">

                                    {{-- Botón de guardar lista --}}
                                    <div x-data="{
                                        saved: {{ in_array($list->id, $savedAnimeIds) ? 'true' : 'false' }},
                                        toggleSave() {
                                            fetch('{{ route('listas.anime.toggle-save', $list->id) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    },
                                                })
                                                .then(res => res.json())
                                                .then(data => {
                                                    this.saved = data.saved;
                                                })
                                                .catch(err => console.error(err));
                                        }
                                    }">
                                        <button @click.stop="toggleSave()"
                                            class="group p-1 focus:outline-none transition">
                                            {{-- Ícono sin guardar --}}
                                            <svg x-show="!saved" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                class="w-7 h-7 text-gray-400 group-hover:text-blue-600 transition-all duration-200">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M5 5v14l7-5 7 5V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2z" />
                                            </svg>

                                            {{-- Ícono guardado --}}
                                            <svg x-show="saved" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 24 24"
                                                class="w-7 h-7 text-blue-600 transition-all duration-200">
                                                <path d="M5 3a2 2 0 0 0-2 2v16l9-6 9 6V5a2 2 0 0 0-2-2H5z" />
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Botón de likes --}}
                                    <div x-data="{
                                        liked: {{ $list->is_liked ? 'true' : 'false' }},
                                        likeCount: {{ $list->likes_count }},
                                        toggleLike() {
                                            fetch('{{ route('listas.anime.like', $list->id) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    },
                                                })
                                                .then(res => res.json())
                                                .then(data => {
                                                    this.liked = data.liked;
                                                    this.likeCount = data.likes_count;
                                                })
                                                .catch(err => console.error(err));
                                        }
                                    }" class="flex items-center gap-1">

                                        <button @click.stop="toggleLike()"
                                            class="group p-1 focus:outline-none transition">
                                            {{-- Corazón vacío --}}
                                            <svg x-show="!liked" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                class="w-7 h-7 text-gray-400 group-hover:text-red-500 transition-all duration-200">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 21l-7.682-8.318a4.5 4.5 0 0 1 0-6.364z" />
                                            </svg>

                                            {{-- Corazón relleno --}}
                                            <svg x-show="liked" xmlns="http://www.w3.org/2000/svg" fill="red"
                                                viewBox="0 0 24 24" class="w-7 h-7 transition-all duration-200">
                                                <path
                                                    d="M12 21s-6.716-6.188-9.364-8.836A6 6 0 0 1 12 4.636a6 6 0 0 1 9.364 8.528C18.716 14.812 12 21 12 21z" />
                                            </svg>
                                        </button>

                                        <span class="text-sm text-gray-600" x-text="likeCount"></span>
                                    </div>

                                </div>

                                <h2 class="text-xl font-bold text-gray-800 mb-2 truncate">{{ $list->name }}</h2>
                                @if (!$list->items->isEmpty())
                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        @foreach ($list->items->take(4) as $item)
                                            {{-- Solo las primeras 4 --}}
                                            @php
                                                $image = $item->anime->cover_image ?? $item->anime_image;
                                            @endphp
                                            @if ($image)
                                                <img src="{{ $image }}" alt="{{ $item->anime_title }}"
                                                    class="w-full h-24 object-cover rounded-lg">
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Modal Moderno para Listas Públicas de Anime --}}
                            <div id="listModal-{{ $list->id }}"
                                class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 opacity-0 scale-95 transition-all duration-200">
                                <div
                                    class="bg-white p-6 rounded-xl shadow-xl w-11/12 max-w-5xl max-h-[85vh] overflow-y-auto relative flex flex-col gap-6">

                                    <!-- Encabezado -->
                                    <div class="flex justify-between items-start border-b pb-3">
                                        <div class="space-y-3">
                                            <h2 class="text-2xl font-bold text-gray-800">{{ $list->name }}</h2>

                                            @if ($list->user)
                                                <a href="{{ route('profile.show', $list->user->id) }}"
                                                    class="flex items-center gap-3">
                                                    <img src="{{ $list->user->avatar_url }}"
                                                        alt="{{ $list->user->name }}"
                                                        class="w-10 h-10 rounded-full ring-2 ring-gray-200 hover:opacity-80 transition">

                                                    <span class="text-gray-700 font-semibold hover:underline">
                                                        {{ $list->user->name }}
                                                    </span>
                                                </a>
                                            @endif

                                            {{-- Contenedor de descripción --}}
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                                @if ($list->description)
                                                    <p class="text-gray-700 text-sm">{{ $list->description }}</p>
                                                @else
                                                    <p class="text-gray-400 text-sm italic">
                                                        El usuario no ha proporcionado información para esta lista.
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Botón cerrar -->
                                        <button
                                            class="close-modal text-gray-600 hover:text-gray-800 text-2xl font-bold bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center">
                                            ✕
                                        </button>
                                    </div>

                                    <!-- Contenido de la lista -->
                                    @if ($list->items->isEmpty())
                                        <p class="text-gray-500 text-center py-10">Esta lista no contiene animes.</p>
                                    @else
                                        <div
                                            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                            @foreach ($list->items as $item)
                                                @php
                                                    $anime = $item->anime ?? null;
                                                    $image = $anime->cover_image ?? $item->anime_image;
                                                    $animeName = $anime->title ?? $item->anime_title;
                                                    $hasNotes = !empty($item->notes);
                                                    $anilistId = $anime->anilist_id ?? null;
                                                @endphp

                                                @if ($anilistId)
                                                    <a href="{{ url('/animes/' . $anilistId) }}"
                                                        class="bg-gray-800 text-white p-4 rounded-2xl shadow-md hover:shadow-lg transition block hover:scale-[1.03] cursor-pointer relative">
                                                    @else
                                                        <div
                                                            class="bg-gray-800 text-white p-4 rounded-2xl shadow-md opacity-90 block cursor-not-allowed relative">
                                                @endif

                                                @if ($image)
                                                    <img src="{{ $image }}" alt="{{ $animeName }}"
                                                        class="w-full h-56 object-cover rounded-lg mb-4">
                                                @else
                                                    <div
                                                        class="w-full h-56 bg-gray-600 flex items-center justify-center rounded-lg mb-4">
                                                        <span class="text-gray-300 text-sm">Sin imagen</span>
                                                    </div>
                                                @endif

                                                <h3 class="text-lg font-bold mb-1 truncate">{{ $animeName }}</h3>

                                                @if ($item->score)
                                                    <p class="text-sm text-yellow-400">
                                                        Puntuación: <span
                                                            class="font-semibold">{{ $item->score }}/10</span>
                                                    </p>
                                                @endif

                                                {{-- Botón para mostrar nota --}}
                                                @if ($hasNotes)
                                                    <button
                                                        class="mt-2 text-xs text-blue-400 hover:underline open-note-modal"
                                                        data-note="{{ $item->notes }}">
                                                        Ver nota 📝
                                                    </button>
                                                @endif

                                                @if ($anilistId)
                                                    </a>
                                                @else
                                        </div>
                                    @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Modal de nota general --}}
        <div id="noteModal"
            class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-[100] opacity-0 scale-95 transition-all duration-200">
            <div
                class="bg-white rounded-xl shadow-xl w-11/12 max-w-md p-6 relative transform transition-all duration-200">
                <button
                    class="absolute top-3 right-3 text-gray-600 hover:text-gray-800 text-xl font-bold close-note-modal">✕</button>
                <h4 class="text-lg font-semibold mb-2 text-gray-800">📝 Nota del usuario</h4>
                <p id="noteContent" class="text-gray-700 leading-relaxed"></p>
            </div>
        </div>
        @endforeach
    </div>
    @endif
    </div>

    {{-- Script para abrir/cerrar modales --}}
    <script>
        // Abrir modal principal
        document.querySelectorAll('[data-modal-target]').forEach(card => {
            card.addEventListener('click', () => {
                const modalId = card.getAttribute('data-modal-target');
                const modal = document.querySelector(modalId);
                modal.classList.remove('hidden', 'opacity-0', 'scale-95');
                modal.classList.add('opacity-100', 'scale-100');
            });
        });

        // Cerrar modal principal
        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.fixed');
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            });
        });

        // Cerrar al hacer clic fuera
        document.querySelectorAll('.fixed').forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) {
                    modal.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => modal.classList.add('hidden'), 200);
                }
            });
        });

        // Abrir modal de nota
        document.querySelectorAll('.open-note-modal').forEach(btn => {
            btn.addEventListener('click', (e) => {
                // 🔒 Evita que el click abra el enlace del anime
                e.stopPropagation();
                e.preventDefault();

                const note = btn.getAttribute('data-note');
                const modal = document.getElementById('noteModal');
                document.getElementById('noteContent').innerText = note;
                modal.classList.remove('hidden', 'opacity-0', 'scale-95');
                modal.classList.add('opacity-100', 'scale-100');
            });
        });

        // Cerrar modal de nota
        document.querySelectorAll('.close-note-modal').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.fixed');
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            });
        });

        // Cerrar modal de nota al hacer click fuera
        document.getElementById('noteModal').addEventListener('click', e => {
            if (e.target.id === 'noteModal') {
                const modal = e.target;
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            }
        });
    </script> 

    {{-- Sección Personajes --}}
    <div>
        <h3 class="text-2xl font-bold mb-4 text-blue-600 hover:underline cursor-pointer"
            onclick="window.location='{{ route('listas.characters.public') }}'">
            Últimas listas de Personajes
        </h3>

        @if ($publicCharacterLists->isEmpty())
            <p class="text-gray-500 text-center py-6">No hay listas públicas de personajes disponibles.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($publicCharacterLists as $list)
                    <div class="bg-white rounded-2xl shadow-lg p-4 hover:shadow-xl transition cursor-pointer relative group"
                        data-modal-target="#characterListModal2-{{ $list->id }}">

                        {{-- Contenedor conjunto de guardar + like --}}
                        <div class="absolute top-3 right-3 flex items-center gap-3">

                            {{-- Botón de guardar lista --}}
                            <div x-data="{
                                saved: {{ in_array($list->id, $savedCharacterIds) ? 'true' : 'false' }},
                                message: '',
                                showToast: false,
                                toggleSave() {
                                    fetch('{{ route('listas.characters.save', $list->id) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            },
                                        })
                                        .then(res => res.json())
                                        .then(data => {
                                            this.saved = data.saved;
                                            this.message = data.saved ?
                                                '✅ Lista guardada correctamente' :
                                                '❌ Ya no tienes guardada esta lista';
                                            this.showToast = true;
                                            setTimeout(() => this.showToast = false, 2000);
                                        })
                                        .catch(err => console.error(err));
                                }
                            }">
                                <button @click.stop="toggleSave()" class="group p-1 focus:outline-none transition">
                                    {{-- Ícono sin guardar --}}
                                    <svg x-show="!saved" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        class="w-7 h-7 text-gray-400 group-hover:text-blue-600 transition-all duration-200">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M5 5v14l7-5 7 5V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2z" />
                                    </svg>

                                    {{-- Ícono guardado --}}
                                    <svg x-show="saved" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        viewBox="0 0 24 24" class="w-7 h-7 text-blue-600 transition-all duration-200">
                                        <path d="M5 3a2 2 0 0 0-2 2v16l9-6 9 6V5a2 2 0 0 0-2-2H5z" />
                                    </svg>
                                </button>

                                {{-- Toast de confirmación --}}
                                <template x-if="showToast">
                                    <div x-transition
                                        class="absolute top-10 right-0 bg-gray-900 text-white text-sm font-medium py-2 px-3 rounded-lg shadow-lg">
                                        <span x-text="message"></span>
                                    </div>
                                </template>
                            </div>

                            {{-- Botón de likes --}}
                            <div x-data="{
                                liked: {{ $list->is_liked ? 'true' : 'false' }},
                                likeCount: {{ $list->likes_count }},
                                toggleLike() {
                                    fetch('{{ route('listas.characters.like', $list->id) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            },
                                        })
                                        .then(res => res.json())
                                        .then(data => {
                                            this.liked = data.liked;
                                            this.likeCount = data.likes_count;
                                        })
                                        .catch(err => console.error(err));
                                }
                            }" class="flex items-center gap-1">

                                <button @click.stop="toggleLike()" class="group p-1 focus:outline-none transition">
                                    {{-- Corazón vacío --}}
                                    <svg x-show="!liked" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        class="w-7 h-7 text-gray-400 group-hover:text-red-500 transition-all duration-200">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 21l-7.682-8.318a4.5 4.5 0 0 1 0-6.364z" />
                                    </svg>

                                    {{-- Corazón relleno --}}
                                    <svg x-show="liked" xmlns="http://www.w3.org/2000/svg" fill="red"
                                        viewBox="0 0 24 24" class="w-7 h-7 transition-all duration-200">
                                        <path
                                            d="M12 21s-6.716-6.188-9.364-8.836A6 6 0 0 1 12 4.636a6 6 0 0 1 9.364 8.528C18.716 14.812 12 21 12 21z" />
                                    </svg>
                                </button>

                                <span class="text-sm text-gray-600" x-text="likeCount"></span>
                            </div>

                        </div>
                        <h2 class="text-xl font-bold text-gray-800 mb-2 truncate">{{ $list->name }}</h2>

                        @if (!$list->items->isEmpty())
                            <div class="grid grid-cols-2 gap-2 mt-2">
                                @foreach ($list->items->take(4) as $item)
                                    @php
                                        $image = $item->character->image_url ?? $item->character_image;
                                    @endphp
                                    @if ($image)
                                        <img src="{{ $image }}"
                                            alt="{{ $item->character->name ?? $item->character_name }}"
                                            class="w-full h-24 object-cover rounded-lg">
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Modal Moderno para listas públicas de personajes --}}
                    <div id="characterListModal2-{{ $list->id }}"
                        class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 opacity-0 scale-95 transition-all duration-200">
                        <div
                            class="bg-white p-6 rounded-xl shadow-xl w-11/12 max-w-5xl max-h-[85vh] overflow-y-auto relative flex flex-col gap-6">

                            <!-- Encabezado -->
                            <div class="flex justify-between items-start border-b pb-3">
                                <div class="space-y-3">
                                    <h2 class="text-2xl font-bold text-gray-800">{{ $list->name }}</h2>

                                    @if ($list->user)
                                        <a href="{{ route('profile.show', $list->user->id) }}"
                                            class="flex items-center gap-3">
                                            <img src="{{ $list->user->avatar_url }}" alt="{{ $list->user->name }}"
                                                class="w-10 h-10 rounded-full ring-2 ring-gray-200 hover:opacity-80 transition">

                                            <span class="text-gray-700 font-semibold hover:underline">
                                                {{ $list->user->name }}
                                            </span>
                                        </a>
                                    @endif

                                    {{-- Contenedor de descripción --}}
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        @if ($list->description)
                                            <p class="text-gray-700 text-sm">{{ $list->description }}</p>
                                        @else
                                            <p class="text-gray-400 text-sm italic">
                                                El usuario no ha proporcionado información para esta lista.
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Botón cerrar -->
                                <button
                                    class="close-modal-character text-gray-600 hover:text-gray-800 text-2xl font-bold bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center">
                                    ✕
                                </button>
                            </div>

                            <!-- Contenido de la lista -->
                            @if ($list->items->isEmpty())
                                <p class="text-gray-500 text-center py-10">Esta lista no contiene personajes.</p>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                    @foreach ($list->items as $item)
                                        @php
                                            $character = $item->character ?? null;
                                            $image = $character->image_url ?? $item->character_image;
                                            $name = $character->name ?? $item->character_name;
                                            $hasNotes = !empty($item->notes);
                                            $animeAnilistId =
                                                $character->anime->anilist_id ?? ($item->anime_anilist_id ?? null);
                                            $characterAnilistId =
                                                $character->anilist_id ?? ($item->character_anilist_id ?? null);
                                        @endphp

                                        @if ($animeAnilistId && $characterAnilistId)
                                            <a href="{{ url('/animes/' . $animeAnilistId . '/personajes/' . $characterAnilistId) }}"
                                                class="bg-gray-800 text-white p-4 rounded-2xl shadow-md hover:shadow-lg transition block hover:scale-[1.03] cursor-pointer relative">
                                            @else
                                                <div
                                                    class="bg-gray-800 text-white p-4 rounded-2xl shadow-md opacity-90 block cursor-not-allowed relative">
                                        @endif

                                        @if ($image)
                                            <img src="{{ $image }}" alt="{{ $name }}"
                                                class="w-full h-56 object-cover rounded-lg mb-4">
                                        @else
                                            <div
                                                class="w-full h-56 bg-gray-600 flex items-center justify-center rounded-lg mb-4">
                                                <span class="text-gray-300 text-sm">Sin imagen</span>
                                            </div>
                                        @endif

                                        <h3 class="text-lg font-bold mb-1 truncate">{{ $name }}</h3>

                                        @if ($item->score)
                                            <p class="text-sm text-yellow-400">
                                                Puntuación: <span class="font-semibold">{{ $item->score }}/10</span>
                                            </p>
                                        @endif

                                        {{-- Botón para mostrar nota --}}
                                        @if ($hasNotes)
                                            <button
                                                class="mt-2 text-xs text-blue-400 hover:underline open-note-modal-character"
                                                data-note="{{ $item->notes }}">
                                                Ver nota 📝
                                            </button>
                                        @endif

                                        @if ($animeAnilistId && $characterAnilistId)
                                            </a>
                                        @else
                                </div>
                            @endif
                @endforeach
            </div>
        @endif
    </div>
    </div>
    @endforeach
    </div>

    {{-- Modal de nota general --}}
    <div id="noteModalCharacter"
        class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-[100] opacity-0 scale-95 transition-all duration-200">
        <div class="bg-white rounded-xl shadow-xl w-11/12 max-w-md p-6 relative transform transition-all duration-200">
            <button
                class="absolute top-3 right-3 text-gray-600 hover:text-gray-800 text-xl font-bold close-note-modal-character">✕</button>
            <h4 class="text-lg font-semibold mb-2 text-gray-800">📝 Nota del usuario</h4>
            <p id="noteContentCharacter" class="text-gray-700 leading-relaxed"></p>
        </div>
    </div>

    {{-- Script para abrir/cerrar modales --}}
    <script>
        // Abrir modal principal
        document.querySelectorAll('[data-modal-target^="#characterListModal2"]').forEach(card => {
            card.addEventListener('click', () => {
                const modalId = card.getAttribute('data-modal-target');
                const modal = document.querySelector(modalId);
                modal.classList.remove('hidden', 'opacity-0', 'scale-95');
                modal.classList.add('opacity-100', 'scale-100');
            });
        });

        // Cerrar modal principal
        document.querySelectorAll('.close-modal-character').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.fixed');
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            });
        });

        // Cerrar al hacer clic fuera
        document.querySelectorAll('[id^="characterListModal2"]').forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) {
                    modal.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => modal.classList.add('hidden'), 200);
                }
            });
        });

        // Abrir modal de nota
        document.querySelectorAll('.open-note-modal-character').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                e.preventDefault();

                const note = btn.getAttribute('data-note');
                const modal = document.getElementById('noteModalCharacter');
                document.getElementById('noteContentCharacter').innerText = note;
                modal.classList.remove('hidden', 'opacity-0', 'scale-95');
                modal.classList.add('opacity-100', 'scale-100');
            });
        });

        // Cerrar modal de nota
        document.querySelectorAll('.close-note-modal-character').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.fixed');
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            });
        });

        // Cerrar modal de nota al hacer click fuera
        document.getElementById('noteModalCharacter').addEventListener('click', e => {
            if (e.target.id === 'noteModalCharacter') {
                const modal = e.target;
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            }
        });
    </script>

    @endif
    </div>
    </div>
    </div>
</x-app-layout>
