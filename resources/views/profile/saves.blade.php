<x-app-layout>

    <!-- ========================================= -->
    <!-- 🏞️ BANNER DE PERFIL -->
    <!-- ========================================= -->
    <div class="relative w-full h-72 sm:h-64 bg-gradient-to-r from-indigo-400 to-indigo-600 overflow-hidden">
        <!-- Imagen de banner -->
        <img src="{{ $user->banner ? asset('storage/' . $user->banner) : asset('images/default-banner.jpg') }}"
            class="absolute inset-0 w-full h-full object-cover opacity-90" alt="Banner de {{ $user->name }}">
        <div class="absolute inset-0 bg-black bg-opacity-30"></div>

        <!-- Contenido -->
        <div
            class="relative flex flex-col sm:flex-row items-center sm:items-start h-full max-w-6xl mx-auto px-6 md:px-10 py-4">

            <!-- Avatar -->
            <div class="flex-shrink-0 mb-4 sm:mb-0 sm:mr-8">
                <img class="h-36 w-36 rounded-full object-cover border-4 border-white shadow-lg"
                    src="{{ $user->avatar_url }}" alt="Avatar de {{ $user->name }}">
            </div>

            <!-- Info -->
            <div class="text-white w-full sm:w-auto text-center sm:text-left">
                <h1 class="text-3xl font-bold mb-2 truncate">{{ $user->name }}</h1>

                <!-- Descripción con scroll vertical controlado -->
                <div
                    class="max-w-full sm:max-w-lg max-h-48 overflow-y-auto overflow-x-hidden p-2 bg-black bg-opacity-20 rounded break-words whitespace-normal">
                    <p class="text-gray-100">
                        {{ $user->bio ?? 'Este usuario no ha agregado una descripción.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Sección Anime Guardados --}}
            <div class="px-3 sm:px-6">

                <h3 class="text-2xl font-bold mb-4 text-blue-600 text-center sm:text-left">
                    Listas de Anime guardadas
                </h3>

                @if ($savedAnimeLists->isEmpty())
                    <p class="text-gray-500 text-center py-6">No tienes listas de anime guardadas.</p>
                @else
                    {{-- GRID MÁS RESPONSIVE --}}
                    <div
                        class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">
                        @foreach ($savedAnimeLists as $list)
                            <div class="bg-white rounded-2xl shadow-lg p-4 hover:shadow-xl transition cursor-pointer relative group"
                                data-modal-target="#animeSavedModal-{{ $list->id }}">

                                {{-- Botón de guardar --}}
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
                                            class="w-6 h-6 text-gray-400 group-hover:text-blue-600">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M5 5v14l7-5 7 5V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2z" />
                                        </svg>
                                        <svg x-show="saved" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 24 24" class="w-6 h-6 text-blue-600">
                                            <path d="M5 3a2 2 0 0 0-2 2v16l9-6 9 6V5a2 2 0 0 0-2-2H5z" />
                                        </svg>
                                    </button>
                                </div>

                                <h2 class="text-lg font-bold text-gray-800 mb-2 truncate">{{ $list->name }}</h2>

                                {{-- Miniaturas --}}
                                @if (!$list->items->isEmpty())
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach ($list->items->take(4) as $item)
                                            @php $image = $item->anime->cover_image ?? $item->anime_image; @endphp
                                            @if ($image)
                                                <img src="{{ $image }}" alt="{{ $item->anime_title }}"
                                                    class="w-full aspect-[3/4] object-cover rounded-lg" />
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                            </div>

                            {{-- MODAL RESPONSIVE --}}
                            <div id="animeSavedModal-{{ $list->id }}"
                                class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-3">

                                <div
                                    class="bg-white w-full max-w-4xl rounded-xl shadow-xl max-h-[90vh] overflow-y-auto p-5 relative animate-scale-in">

                                    {{-- ENCABEZADO --}}
                                    <div class="flex justify-between items-start border-b pb-3 gap-3">
                                        <div class="space-y-3 w-full">

                                            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $list->name }}
                                            </h2>

                                            {{-- Usuario --}}
                                            @if ($list->user)
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $list->user->avatar_url }}"
                                                        alt="{{ $list->user->name }}"
                                                        class="w-10 h-10 rounded-full border" />
                                                    <span class="text-gray-700 font-semibold text-sm sm:text-base">
                                                        {{ $list->user->name }}
                                                    </span>
                                                </div>
                                            @endif

                                            {{-- Descripción --}}
                                            <div class="bg-gray-50 border rounded-lg p-3">
                                                @if ($list->description)
                                                    <p class="text-gray-700 text-sm">{{ $list->description }}</p>
                                                @else
                                                    <p class="text-gray-400 text-sm italic">
                                                        El usuario no ha proporcionado descripción.
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Botón cerrar --}}
                                        <button
                                            class="close-modal text-gray-600 hover:text-gray-800 text-xl bg-gray-200 rounded-full w-8 h-8 flex justify-center items-center">✕</button>
                                    </div>

                                    {{-- CONTENIDO --}}
                                    @if ($list->items->isEmpty())
                                        <p class="text-gray-500 text-center py-10">Esta lista no contiene animes.</p>
                                    @else
                                        <div
                                            class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4">

                                            @foreach ($list->items as $item)
                                                @php
                                                    $anime = $item->anime ?? null;
                                                    $image = $anime->cover_image ?? $item->anime_image;
                                                    $animeName = $anime->title ?? $item->anime_title;
                                                    $anilistId = $anime->anilist_id ?? null;
                                                    $hasNotes = !empty($item->notes);
                                                @endphp

                                                <a href="{{ $anilistId ? url('/animes/' . $anilistId) : '#' }}"
                                                    class="{{ $anilistId ? 'cursor-pointer' : 'cursor-not-allowed opacity-80' }} 
                                            bg-gray-800 text-white p-4 rounded-2xl shadow-md hover:shadow-xl hover:scale-[1.03] transition block">

                                                    @if ($image)
                                                        <img src="{{ $image }}"
                                                            class="w-full aspect-[3/4] rounded-lg object-cover mb-3" />
                                                    @else
                                                        <div
                                                            class="w-full aspect-[3/4] bg-gray-600 rounded-lg flex items-center justify-center text-gray-300">
                                                            Sin imagen
                                                        </div>
                                                    @endif

                                                    <h3 class="font-bold text-sm sm:text-base truncate">
                                                        {{ $animeName }}</h3>

                                                    @if ($item->score)
                                                        <p class="text-xs sm:text-sm text-yellow-400">
                                                            Puntuación: <strong>{{ $item->score }}/10</strong>
                                                        </p>
                                                    @endif

                                                    @if ($hasNotes)
                                                        <button
                                                            class="mt-2 text-xs text-blue-400 underline open-note-modal"
                                                            data-note="{{ $item->notes }}">
                                                            Ver nota 📝
                                                        </button>
                                                    @endif

                                                </a>
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
                <div
                    class="bg-white rounded-xl shadow-xl w-11/12 max-w-md p-6 relative transform transition-all duration-200">
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
            <div class="px-3 sm:px-6">

                <h3 class="text-2xl font-bold mb-4 text-blue-600 text-center sm:text-left">
                    Listas de Personajes guardadas
                </h3>

                @if ($savedCharacterLists->isEmpty())
                    <p class="text-gray-500 text-center py-6">No tienes listas de personajes guardadas.</p>
                @else
                    {{-- GRID RESPONSIVE --}}
                    <div
                        class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">
                        @foreach ($savedCharacterLists as $list)
                            <div class="bg-white rounded-2xl shadow-lg p-4 hover:shadow-xl transition cursor-pointer relative group"
                                data-modal-target="#characterSavedModal-{{ $list->id }}">

                                {{-- Botón guardar --}}
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
                                        <svg x-show="!saved"
                                            class="w-6 h-6 text-gray-400 group-hover:text-blue-600 transition"
                                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M5 5v14l7-5 7 5V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2z" />
                                        </svg>

                                        <svg x-show="saved" class="w-6 h-6 text-blue-600 transition" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M5 3a2 2 0 0 0-2 2v16l9-6 9 6V5a2 2 0 0 0-2-2H5z" />
                                        </svg>
                                    </button>
                                </div>

                                <h2 class="text-lg font-bold text-gray-800 mb-2 truncate">{{ $list->name }}</h2>

                                {{-- Miniaturas --}}
                                @if (!$list->items->isEmpty())
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach ($list->items->take(4) as $item)
                                            @php $image = $item->character->image_url ?? $item->character_image; @endphp

                                            @if ($image)
                                                <img src="{{ $image }}"
                                                    alt="{{ $item->character_name ?? $item->character->name }}"
                                                    class="w-full aspect-[3/4] object-cover rounded-lg" />
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                            </div>

                            {{-- MODAL RESPONSIVE --}}
                            <div id="characterSavedModal-{{ $list->id }}"
                                class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-3">

                                <div
                                    class="bg-white w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-xl p-5 shadow-xl relative animate-scale-in">

                                    {{-- ENCABEZADO --}}
                                    <div class="flex justify-between items-start border-b pb-3 gap-3">

                                        <div class="space-y-3 w-full">

                                            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">
                                                {{ $list->name }}</h2>

                                            {{-- Usuario --}}
                                            @if ($list->user)
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $list->user->avatar_url }}"
                                                        alt="{{ $list->user->name }}"
                                                        class="w-10 h-10 rounded-full border" />
                                                    <span class="text-gray-700 font-semibold text-sm sm:text-base">
                                                        {{ $list->user->name }}
                                                    </span>
                                                </div>
                                            @endif

                                            {{-- Descripción --}}
                                            <div class="bg-gray-50 border rounded-lg p-3">
                                                @if ($list->description)
                                                    <p class="text-gray-700 text-sm">{{ $list->description }}</p>
                                                @else
                                                    <p class="text-gray-400 text-sm italic">El usuario no ha
                                                        proporcionado información para esta lista.</p>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Cerrar --}}
                                        <button
                                            class="close-modal-character bg-gray-200 text-gray-600 hover:text-gray-800 rounded-full w-8 h-8 flex justify-center items-center font-bold text-xl">✕</button>
                                    </div>

                                    {{-- CONTENIDO --}}
                                    @if ($list->items->isEmpty())
                                        <p class="text-gray-500 text-center py-10">Esta lista no contiene personajes.
                                        </p>
                                    @else
                                        <div
                                            class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4">

                                            @foreach ($list->items as $item)
                                                @php
                                                    $character = $item->character ?? null;
                                                    $image = $character->image_url ?? $item->character_image;
                                                    $name = $character->name ?? $item->character_name;

                                                    $animeAnilistId =
                                                        $character->anime->anilist_id ??
                                                        ($item->anime_anilist_id ?? null);
                                                    $characterAnilistId =
                                                        $character->anilist_id ?? ($item->character_anilist_id ?? null);

                                                    $hasNotes = !empty($item->notes);
                                                @endphp

                                                <a href="{{ $animeAnilistId && $characterAnilistId ? url('/animes/' . $animeAnilistId . '/personajes/' . $characterAnilistId) : '#' }}"
                                                    class="bg-gray-800 text-white p-4 rounded-2xl shadow-md hover:shadow-xl hover:scale-[1.03] transition block {{ $animeAnilistId && $characterAnilistId ? '' : 'cursor-not-allowed opacity-80' }}">

                                                    @if ($image)
                                                        <img src="{{ $image }}" alt="{{ $name }}"
                                                            class="w-full aspect-[3/4] rounded-lg object-cover mb-3" />
                                                    @else
                                                        <div
                                                            class="w-full aspect-[3/4] bg-gray-600 rounded-lg flex items-center justify-center text-gray-300">
                                                            Sin imagen
                                                        </div>
                                                    @endif

                                                    <h3 class="font-bold text-sm sm:text-base truncate">
                                                        {{ $name }}</h3>

                                                    @if ($item->score)
                                                        <p class="text-xs sm:text-sm text-yellow-400">
                                                            Puntuación: <strong>{{ $item->score }}/10</strong>
                                                        </p>
                                                    @endif

                                                    @if ($hasNotes)
                                                        <button
                                                            class="mt-2 text-xs text-blue-400 underline open-note-modal-character"
                                                            data-note="{{ $item->notes }}">
                                                            Ver nota 📝
                                                        </button>
                                                    @endif
                                                </a>
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
                <div
                    class="bg-white rounded-xl shadow-xl w-11/12 max-w-md p-6 relative transform transition-all duration-200">
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
</x-app-layout>
