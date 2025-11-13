<x-app-layout>

    <!-- ========================================= -->
    <!-- 🏞️ BANNER DE PERFIL -->
    <!-- ========================================= -->
    <div class="relative w-full h-64 bg-gradient-to-r from-indigo-400 to-indigo-600 overflow-hidden">
        <!-- Imagen de banner -->
        <img src="{{ $user->banner ? asset('storage/' . $user->banner) : asset('images/default-banner.jpg') }}"
            class="absolute inset-0 w-full h-full object-cover opacity-90" alt="Banner de {{ $user->name }}">
        <div class="absolute inset-0 bg-black bg-opacity-30"></div>

        <!-- Contenido -->
        <div class="relative flex items-center justify-start h-full max-w-6xl mx-auto px-6 md:px-10">
            <!-- Avatar -->
            <div class="relative group cursor-pointer flex-shrink-0 mr-8" id="openEditModal">
                <img class="h-36 w-36 rounded-full object-cover border-4 border-white shadow-lg"
                    src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/avatars/default-avatar.png') }}"
                    alt="Avatar de {{ $user->name }}">


                <div
                    class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                    <span class="text-white text-sm font-medium">Editar</span>
                </div>
            </div>

            <!-- Info -->
            <div class="text-white">
                <h1 class="text-3xl font-bold mb-2">{{ $user->name }}</h1>
                <p class="text-gray-100 mb-4 max-w-lg">
                    {{ $user->bio ?? 'Este usuario no ha agregado una descripción.' }}
                </p>
                <a href="{{ route('profile.edit') }}"
                    class="bg-white text-indigo-700 font-semibold px-5 py-2 rounded-full hover:bg-gray-100 transition">
                    Editar Perfil
                </a>
            </div>
        </div>
    </div>

    <!-- ========================================= -->
    <!-- ✨ MODAL DE EDICIÓN -->
    <!-- ========================================= -->
    <div id="editModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 opacity-0 scale-95">
        <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4">Editar Perfil</h2>

            <form method="POST" action="{{ route('profile.updateBioAvatar') }}" enctype="multipart/form-data">
                @csrf

                <!-- Banner -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nueva imagen de portada</label>
                    <input type="file" name="banner"
                        class="block w-full text-sm border border-gray-300 rounded p-2">
                </div>

                <!-- Avatar -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo Avatar</label>
                    <input type="file" name="avatar"
                        class="block w-full text-sm border border-gray-300 rounded p-2">
                </div>

                <!-- Bio -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="bio" rows="3" class="w-full border rounded-md p-2">{{ $user->bio }}</textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" id="closeEditModal"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Sección Anime Guardados --}}
            <div>
                <h3 class="text-2xl font-bold mb-4 text-blue-600">Listas de Anime guardadas</h3>

                @if ($savedAnimeLists->isEmpty())
                    <p class="text-gray-500 text-center py-6">No tienes listas de anime guardadas.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($savedAnimeLists as $list)
                            <div class="bg-white rounded-2xl shadow-lg p-4 hover:shadow-xl transition cursor-pointer relative group"
                                data-modal-target="#animeSavedModal-{{ $list->id }}">

                                {{-- Botón de guardar lista --}}
                                <div x-data="{
                                    saved: true,
                                    toggleSave() {
                                        fetch('{{ route('listas.anime.toggle-save', $list->id) }}', {
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                            })
                                            .then(res => res.json())
                                            .then(data => { this.saved = data.saved })
                                            .catch(err => console.error(err));
                                    }
                                }" class="absolute top-3 right-3">
                                    <button @click.stop="toggleSave()" class="group p-1 focus:outline-none transition">
                                        <svg x-show="!saved" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            class="w-7 h-7 text-gray-400 group-hover:text-blue-600 transition-all duration-200">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M5 5v14l7-5 7 5V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2z" />
                                        </svg>
                                        <svg x-show="saved" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 24 24"
                                            class="w-7 h-7 text-blue-600 transition-all duration-200">
                                            <path d="M5 3a2 2 0 0 0-2 2v16l9-6 9 6V5a2 2 0 0 0-2-2H5z" />
                                        </svg>
                                    </button>
                                </div>

                                <h2 class="text-xl font-bold text-gray-800 mb-2 truncate">{{ $list->name }}</h2>

                                @if (!$list->items->isEmpty())
                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        @foreach ($list->items->take(4) as $item)
                                            @php $image = $item->anime->cover_image ?? $item->anime_image; @endphp
                                            @if ($image)
                                                <img src="{{ $image }}" alt="{{ $item->anime_title }}"
                                                    class="w-full h-24 object-cover rounded-lg">
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Modal Moderno para Listas de Anime Guardadas --}}
                            <div id="animeSavedModal-{{ $list->id }}"
                                class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 opacity-0 scale-95 transition-all duration-200">
                                <div
                                    class="bg-white p-6 rounded-xl shadow-xl w-11/12 max-w-5xl max-h-[85vh] overflow-y-auto relative flex flex-col gap-6">

                                    <!-- Encabezado -->
                                    <div class="flex justify-between items-start border-b pb-3">
                                        <div class="space-y-3">
                                            <h2 class="text-2xl font-bold text-gray-800">{{ $list->name }}</h2>

                                            @if ($list->user)
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $list->user->avatar_url }}"
                                                        alt="{{ $list->user->name }}"
                                                        class="w-10 h-10 rounded-full ring-2 ring-gray-200">
                                                    <span
                                                        class="text-gray-700 font-semibold">{{ $list->user->name }}</span>
                                                </div>
                                            @endif

                                            {{-- Contenedor de descripción --}}
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                                @if ($list->description)
                                                    <p class="text-gray-700 text-sm">{{ $list->description }}</p>
                                                @else
                                                    <p class="text-gray-400 text-sm italic">El usuario no ha
                                                        proporcionado información para esta lista.</p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Botón cerrar -->
                                        <button
                                            class="close-modal text-gray-600 hover:text-gray-800 text-2xl font-bold bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center">✕</button>
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
        @endforeach
    </div>
    @endif
    </div>

    {{-- Modal de nota --}}
    <div id="noteModal"
        class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-[100] opacity-0 scale-95 transition-all duration-200">
        <div class="bg-white rounded-xl shadow-xl w-11/12 max-w-md p-6 relative transform transition-all duration-200">
            <button
                class="absolute top-3 right-3 text-gray-600 hover:text-gray-800 text-xl font-bold close-note-modal">✕</button>
            <h4 class="text-lg font-semibold mb-2 text-gray-800">📝 Nota del usuario</h4>
            <p id="noteContent" class="text-gray-700 leading-relaxed"></p>
        </div>
    </div>

    {{-- Script para abrir/cerrar modales igual que en la pública --}}
    <script>
        document.querySelectorAll('[data-modal-target]').forEach(card => {
            card.addEventListener('click', () => {
                const modalId = card.getAttribute('data-modal-target');
                const modal = document.querySelector(modalId);
                modal.classList.remove('hidden', 'opacity-0', 'scale-95');
                modal.classList.add('opacity-100', 'scale-100');
            });
        });

        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.fixed');
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            });
        });

        document.querySelectorAll('.fixed').forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) {
                    modal.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => modal.classList.add('hidden'), 200);
                }
            });
        });

        document.querySelectorAll('.open-note-modal').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                e.preventDefault();
                const note = btn.getAttribute('data-note');
                const modal = document.getElementById('noteModal');
                document.getElementById('noteContent').innerText = note;
                modal.classList.remove('hidden', 'opacity-0', 'scale-95');
                modal.classList.add('opacity-100', 'scale-100');
            });
        });

        document.querySelectorAll('.close-note-modal').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.fixed');
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            });
        });

        document.getElementById('noteModal').addEventListener('click', e => {
            if (e.target.id === 'noteModal') {
                const modal = e.target;
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            }
        });
    </script>

    {{-- Sección Personajes Guardados --}}
    <div>
        <h3 class="text-2xl font-bold mb-4 text-blue-600">Listas de Personajes guardadas</h3>

        @if ($savedCharacterLists->isEmpty())
            <p class="text-gray-500 text-center py-6">No tienes listas de personajes guardadas.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($savedCharacterLists as $list)
                    <div class="bg-white rounded-2xl shadow-lg p-4 hover:shadow-xl transition cursor-pointer relative group"
                        data-modal-target="#characterSavedModal-{{ $list->id }}">

                        {{-- Botón de guardar lista --}}
                        <div x-data="{
                            saved: true,
                            toggleSave() {
                                fetch('{{ route('listas.characters.save', $list->id) }}', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    })
                                    .then(res => res.json())
                                    .then(data => { this.saved = data.saved })
                                    .catch(err => console.error(err));
                            }
                        }" class="absolute top-3 right-3">
                            <button @click.stop="toggleSave()" class="group p-1 focus:outline-none transition">
                                <svg x-show="!saved" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    class="w-7 h-7 text-gray-400 group-hover:text-blue-600 transition-all duration-200">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5 5v14l7-5 7 5V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2z" />
                                </svg>
                                <svg x-show="saved" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 24 24" class="w-7 h-7 text-blue-600 transition-all duration-200">
                                    <path d="M5 3a2 2 0 0 0-2 2v16l9-6 9 6V5a2 2 0 0 0-2-2H5z" />
                                </svg>
                            </button>
                        </div>

                        <h2 class="text-xl font-bold text-gray-800 mb-2 truncate">{{ $list->name }}</h2>

                        @if (!$list->items->isEmpty())
                            <div class="grid grid-cols-2 gap-2 mt-2">
                                @foreach ($list->items->take(4) as $item)
                                    @php $image = $item->character->image_url ?? $item->character_image; @endphp
                                    @if ($image)
                                        <img src="{{ $image }}"
                                            alt="{{ $item->character_name ?? $item->character->name }}"
                                            class="w-full h-24 object-cover rounded-lg">
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Modal Moderno para Listas de Personajes Guardadas --}}
                    <div id="characterSavedModal-{{ $list->id }}"
                        class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 opacity-0 scale-95 transition-all duration-200">
                        <div
                            class="bg-white p-6 rounded-xl shadow-xl w-11/12 max-w-5xl max-h-[85vh] overflow-y-auto relative flex flex-col gap-6">

                            <!-- Encabezado -->
                            <div class="flex justify-between items-start border-b pb-3">
                                <div class="space-y-3">
                                    <h2 class="text-2xl font-bold text-gray-800">{{ $list->name }}</h2>

                                    @if ($list->user)
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $list->user->avatar_url }}" alt="{{ $list->user->name }}"
                                                class="w-10 h-10 rounded-full ring-2 ring-gray-200">
                                            <span class="text-gray-700 font-semibold">{{ $list->user->name }}</span>
                                        </div>
                                    @endif

                                    {{-- Contenedor de descripción --}}
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        @if ($list->description)
                                            <p class="text-gray-700 text-sm">{{ $list->description }}</p>
                                        @else
                                            <p class="text-gray-400 text-sm italic">El usuario no ha proporcionado
                                                información para esta lista.</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Botón cerrar -->
                                <button
                                    class="close-modal-character text-gray-600 hover:text-gray-800 text-2xl font-bold bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center">✕</button>
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
    @endif
    </div>

    {{-- Modal de nota para personajes --}}
    <div id="noteModalCharacter"
        class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-[100] opacity-0 scale-95 transition-all duration-200">
        <div class="bg-white rounded-xl shadow-xl w-11/12 max-w-md p-6 relative transform transition-all duration-200">
            <button
                class="absolute top-3 right-3 text-gray-600 hover:text-gray-800 text-xl font-bold close-note-modal-character">✕</button>
            <h4 class="text-lg font-semibold mb-2 text-gray-800">📝 Nota del usuario</h4>
            <p id="noteContentCharacter" class="text-gray-700 leading-relaxed"></p>
        </div>
    </div>

    {{-- Script para abrir/cerrar modales igual que en públicas --}}
    <script>
        document.querySelectorAll('[data-modal-target^="#characterSavedModal"]').forEach(card => {
            card.addEventListener('click', () => {
                const modalId = card.getAttribute('data-modal-target');
                const modal = document.querySelector(modalId);
                modal.classList.remove('hidden', 'opacity-0', 'scale-95');
                modal.classList.add('opacity-100', 'scale-100');
            });
        });

        document.querySelectorAll('.close-modal-character').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.fixed');
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            });
        });

        document.querySelectorAll('[id^="characterSavedModal"]').forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) {
                    modal.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => modal.classList.add('hidden'), 200);
                }
            });
        });

        document.querySelectorAll('.open-note-modal-character').forEach(btn => {
            btn.addEventListener('click', e => {
                e.stopPropagation();
                e.preventDefault();
                const note = btn.getAttribute('data-note');
                const modal = document.getElementById('noteModalCharacter');
                document.getElementById('noteContentCharacter').innerText = note;
                modal.classList.remove('hidden', 'opacity-0', 'scale-95');
                modal.classList.add('opacity-100', 'scale-100');
            });
        });

        document.querySelectorAll('.close-note-modal-character').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.fixed');
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            });
        });

        document.getElementById('noteModalCharacter').addEventListener('click', e => {
            if (e.target.id === 'noteModalCharacter') {
                const modal = e.target;
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            }
        });
    </script>
    </div>
    </div>
    <!-- ===================================================== -->
    <!-- ⚙️ SCRIPT MODAL DE EDICIÓN DE PERFIL -->
    <!-- ===================================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openEditBtn = document.getElementById('openEditModal');
            const editModal = document.getElementById('editModal');
            const closeEditBtn = document.getElementById('closeEditModal');

            if (!openEditBtn || !editModal) return;

            //  Abrir modal
            openEditBtn.addEventListener('click', () => {
                editModal.classList.remove('hidden', 'opacity-0', 'scale-95');
                editModal.classList.add('flex', 'opacity-100', 'scale-100');
            });

            //  Cerrar modal (botón o clic fuera)
            closeEditBtn.addEventListener('click', () => {
                editModal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => editModal.classList.add('hidden'), 200);
            });

            editModal.addEventListener('click', (e) => {
                if (e.target === editModal) {
                    editModal.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => editModal.classList.add('hidden'), 200);
                }
            });
        });
    </script>
</x-app-layout>
