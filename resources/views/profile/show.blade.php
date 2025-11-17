<x-app-layout>

    <!-- ===================================================== -->
    <!-- 🏞️ BANNER + AVATAR + BIO -->
    <!-- ===================================================== -->
    <div class="relative w-full h-64 bg-gray-900">
        <img src="{{ $user->banner ? asset('storage/' . $user->banner) : asset('images/default-banner.jpg') }}"
            class="absolute inset-0 w-full h-full object-cover opacity-80">

        <div class="absolute inset-0 bg-black bg-opacity-40"></div>

        <div class="relative max-w-5xl mx-auto px-6 flex items-end h-full pb-6">

            <!-- Avatar -->
            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/avatars/default-avatar.png') }}"
                class="w-28 h-28 rounded-full border-4 border-white shadow-lg">

            <div class="ml-6 text-white">
                <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
                <p class="text-gray-200 mt-1">{{ $user->bio ?? 'Este usuario no ha agregado una descripción.' }}</p>
            </div>

            @if ($isOwner)
                <a href="{{ route('profile.edit') }}"
                    class="ml-auto bg-white text-gray-800 font-semibold px-4 py-2 rounded-lg shadow hover:bg-gray-100">
                    Editar Perfil
                </a>
            @endif
        </div>
    </div>

    <!-- ========================================= -->
    <!-- 🌟 FAVORITOS -->
    <!-- ========================================= -->
    @if (!$animeFavorites->isEmpty() || !$characterFavorites->isEmpty())
        <div class="max-w-6xl mx-auto px-6 py-12 space-y-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Favoritos</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- 🎬 ANIMES FAVORITOS -->
                @if (!$animeFavorites->isEmpty())
                    <div class="relative group cursor-pointer" id="openAnimesModal">
                        <div class="aspect-[16/9] rounded-xl overflow-hidden shadow-md relative">
                            @php
                                $animeImages = $animeFavorites
                                    ->take(4)
                                    ->map(fn($fav) => $fav->anime->cover_image ?? $fav->anime_image)
                                    ->filter();
                            @endphp

                            @if ($animeImages->isEmpty())
                                <div class="flex items-center justify-center h-full bg-gray-200 text-gray-500">
                                    <span>Sin animes favoritos</span>
                                </div>
                            @else
                                <div class="grid grid-cols-2 grid-rows-2 h-full w-full">
                                    @foreach ($animeImages as $img)
                                        <img src="{{ $img }}" alt="Anime favorito"
                                            class="object-cover w-full h-full">
                                    @endforeach
                                    @for ($i = $animeImages->count(); $i < 4; $i++)
                                        <div class="bg-gray-300"></div>
                                    @endfor
                                </div>
                            @endif

                            <div
                                class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <h3 class="text-white text-xl font-bold mb-1">Animes Favoritos</h3>
                                @if (!$animeFavorites->isEmpty())
                                    <span class="text-white text-sm">Ver colección completa</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 👤 PERSONAJES FAVORITOS -->
                @if (!$characterFavorites->isEmpty())
                    <div class="relative group cursor-pointer" id="openCharsModal">
                        <div class="aspect-[16/9] rounded-xl overflow-hidden shadow-md relative">
                            @php
                                $charImages = $characterFavorites
                                    ->take(4)
                                    ->map(fn($fav) => $fav->character->image_url ?? $fav->character_image)
                                    ->filter();
                            @endphp

                            @if ($charImages->isEmpty())
                                <div class="flex items-center justify-center h-full bg-gray-200 text-gray-500">
                                    <span>Sin personajes favoritos</span>
                                </div>
                            @else
                                <div class="grid grid-cols-2 grid-rows-2 h-full w-full">
                                    @foreach ($charImages as $img)
                                        <img src="{{ $img }}" alt="Personaje favorito"
                                            class="object-cover w-full h-full">
                                    @endforeach
                                    @for ($i = $charImages->count(); $i < 4; $i++)
                                        <div class="bg-gray-300"></div>
                                    @endfor
                                </div>
                            @endif

                            <div
                                class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <h3 class="text-white text-xl font-bold mb-1">Personajes Favoritos</h3>
                                @if (!$characterFavorites->isEmpty())
                                    <span class="text-white text-sm">Ver colección completa</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            <!-- 🔗 INCLUIR MODALES -->
            @include('partials.favorites-modals')
        </div>
    @endif

    <!-- ========================================= -->
    <!-- 📋 LISTAS DE ANIMES -->
    <!-- ========================================= -->

    @php
        // Listas visibles según sea propietario o visitante
        $visibleAnimeLists = $isOwner ? $animeLists : $animeLists->where('is_public', 1);
    @endphp

    @if ($visibleAnimeLists->isNotEmpty())
        <div class="max-w-6xl mx-auto px-6 py-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Listas de Animes</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                @foreach ($visibleAnimeLists as $list)
                    <div class="relative group cursor-pointer open-list-modal" data-list-id="{{ $list->id }}">
                        <div class="aspect-[16/9] rounded-2xl overflow-hidden shadow-lg relative">
                            @php
                                $animeImages = $list->items
                                    ->take(4)
                                    ->map(fn($item) => $item->anime->cover_image ?? $item->anime_image)
                                    ->filter();
                            @endphp

                            @if ($animeImages->isEmpty())
                                <div class="flex items-center justify-center h-full bg-gray-200 text-gray-500">
                                    <span>Sin animes en {{ $list->name }}</span>
                                </div>
                            @else
                                <div class="grid grid-cols-2 grid-rows-2 h-full w-full">
                                    @foreach ($animeImages as $img)
                                        <img src="{{ $img }}" alt="Anime en {{ $list->name }}"
                                            class="object-cover w-full h-full">
                                    @endforeach
                                    @for ($i = $animeImages->count(); $i < 4; $i++)
                                        <div class="bg-gray-300"></div>
                                    @endfor
                                </div>
                            @endif

                            <!-- ⭐ Botón guardar para visitantes -->
                            @if (!$isOwner && isset($viewer))
                                <div x-data="{
                                    saved: {{ $list->savedByUsers->contains($viewer->id) ? 'true' : 'false' }},
                                    toggleSave() {
                                        fetch('{{ route('listas.anime.toggle-save', $list->id) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                },
                                            })
                                            .then(res => res.json())
                                            .then(data => { this.saved = data.saved })
                                            .catch(err => console.error(err));
                                    }
                                }" class="absolute top-3 right-3 z-10" x-cloak>
                                    <button @click.stop="toggleSave()" class="group p-1 focus:outline-none transition">

                                        <!-- Ícono vacío -->
                                        <svg x-show="!saved" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            class="w-7 h-7 text-gray-400 group-hover:text-blue-600 transition-all duration-200">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M5 5v14l7-5 7 5V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2z" />
                                        </svg>

                                        <!-- Ícono lleno -->
                                        <svg x-show="saved" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 24 24"
                                            class="w-7 h-7 text-blue-600 transition-all duration-200">
                                            <path d="M5 3a2 2 0 0 0-2 2v16l9-6 9 6V5a2 2 0 0 0-2-2H5z" />
                                        </svg>

                                    </button>
                                </div>
                            @endif

                            <div
                                class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <h3 class="text-white text-2xl font-bold mb-1">{{ $list->name }}</h3>
                                @if ($list->items->isNotEmpty())
                                    <span class="text-white text-sm">Ver colección completa</span>
                                @endif
                            </div>

                        </div>
                    </div>

                    <!-- Modal -->
                    <div id="listModal-{{ $list->id }}"
                        class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 opacity-0 scale-95">
                        <div
                            class="bg-white p-6 rounded-xl shadow-xl w-11/12 max-w-5xl max-h-[80vh] overflow-y-auto relative flex flex-col gap-6">

                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-800">{{ $list->name }}</h2>

                                    @if ($list->description)
                                        <p class="text-gray-600 text-sm">{{ $list->description }}</p>
                                    @endif

                                    <p class="text-gray-500 text-sm">
                                        Estado:
                                        <span class="{{ $list->is_public ? 'text-green-600' : 'text-gray-400' }}">
                                            {{ $list->is_public ? 'Pública' : 'Privada' }}
                                        </span>
                                    </p>
                                </div>

                                <button
                                    class="close-list-modal text-gray-600 hover:text-gray-800 text-xl font-bold">✕</button>
                            </div>

                            @if ($list->items->isEmpty())
                                <p class="text-gray-500 text-center py-10">Esta lista está vacía.</p>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                    @foreach ($list->items as $item)
                                        @php
                                            $image = $item->anime->cover_image ?? $item->anime_image;
                                        @endphp

                                        <a href="{{ url('/animes/' . ($item->anime->anilist_id ?? '')) }}"
                                            class="block bg-gray-800 text-white p-4 rounded-2xl shadow-md hover:shadow-lg transition hover:scale-[1.03]">

                                            @if ($image)
                                                <img src="{{ $image }}" alt="{{ $item->anime_title }}"
                                                    class="w-full h-64 object-cover rounded-lg mb-4">
                                            @else
                                                <div
                                                    class="w-full h-64 bg-gray-600 flex items-center justify-center rounded-lg mb-4">
                                                    <span class="text-gray-300 text-sm">Sin imagen</span>
                                                </div>
                                            @endif

                                            <h3 class="text-lg font-bold mb-1 truncate">
                                                {{ $item->anime->title ?? $item->anime_title }}
                                            </h3>

                                            @if ($item->score)
                                                <p class="text-sm text-gray-300">
                                                    Puntuación: <span
                                                        class="font-semibold">{{ $item->score }}/10</span>
                                                </p>
                                            @endif

                                            @if ($item->episode_progress)
                                                <p class="text-sm text-gray-300">
                                                    Episodios vistos: <span
                                                        class="font-semibold">{{ $item->episode_progress }}</span>
                                                </p>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        @if (!$isOwner)
            <p class="text-gray-500 text-center mt-6"></p>
        @endif
    @endif

    <!-- ========================================= -->
    <!-- 📋 LISTAS DE PERSONAJES -->
    <!-- ========================================= -->
    @php
        // Listas visibles según sea propietario o visitante
        $visibleCharacterLists = $isOwner ? $characterLists : $characterLists->where('is_public', 1);
    @endphp

    @if ($visibleCharacterLists->isNotEmpty())
        <div class="max-w-6xl mx-auto px-6 py-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Listas de Personajes</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                @foreach ($visibleCharacterLists as $list)
                    <div class="relative group cursor-pointer open-character-list-modal"
                        data-list-id="{{ $list->id }}">
                        <div class="aspect-[16/9] rounded-2xl overflow-hidden shadow-lg relative">

                            @php
                                $charImages = $list->items
                                    ->take(4)
                                    ->map(fn($item) => $item->character->image_url ?? $item->character_image)
                                    ->filter();
                            @endphp

                            @if ($charImages->isEmpty())
                                <div class="flex items-center justify-center h-full bg-gray-200 text-gray-500">
                                    <span>Sin personajes en {{ $list->name }}</span>
                                </div>
                            @else
                                <div class="grid grid-cols-2 grid-rows-2 h-full w-full">
                                    @foreach ($charImages as $img)
                                        <img src="{{ $img }}" alt="Personaje en {{ $list->name }}"
                                            class="object-cover w-full h-full">
                                    @endforeach
                                    @for ($i = $charImages->count(); $i < 4; $i++)
                                        <div class="bg-gray-300"></div>
                                    @endfor
                                </div>
                            @endif

                            <!-- ⭐ Botón guardar para visitantes -->
                            @if (!$isOwner && isset($viewer))
                                <div x-data="{
                                    saved: {{ $list->savedByUsers->contains($viewer->id) ? 'true' : 'false' }},
                                    toggleSave() {
                                        fetch('{{ route('listas.characters.save', $list->id) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                },
                                            })
                                            .then(res => res.json())
                                            .then(data => { this.saved = data.saved })
                                            .catch(err => console.error(err));
                                    }
                                }" class="absolute top-3 right-3 z-10" x-cloak>
                                    <button @click.stop="toggleSave()"
                                        class="group p-1 focus:outline-none transition">
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
                            @endif

                            <div
                                class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <h3 class="text-white text-2xl font-bold mb-1">{{ $list->name }}</h3>
                                @if ($list->items->isNotEmpty())
                                    <span class="text-white text-sm">Ver colección completa</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div id="characterListModal-{{ $list->id }}"
                        class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 opacity-0 scale-95">
                        <div
                            class="bg-white p-6 rounded-xl shadow-xl w-11/12 max-w-5xl max-h-[80vh] overflow-y-auto relative flex flex-col gap-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-800">{{ $list->name }}</h2>
                                    @if ($list->description)
                                        <p class="text-gray-600 text-sm">{{ $list->description }}</p>
                                    @endif
                                    <p class="text-gray-500 text-sm">
                                        Estado:
                                        <span class="{{ $list->is_public ? 'text-green-600' : 'text-gray-400' }}">
                                            {{ $list->is_public ? 'Pública' : 'Privada' }}
                                        </span>
                                    </p>
                                </div>

                                <button
                                    class="close-list-modal text-gray-600 hover:text-gray-800 text-xl font-bold">✕</button>
                            </div>

                            @if ($list->items->isEmpty())
                                <p class="text-gray-500 text-center py-10">Esta lista está vacía.</p>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                    @foreach ($list->items as $item)
                                        @php $image = $item->character->image_url ?? $item->character_image; @endphp
                                        <a href="{{ route('animes.characters.show', ['anime' => $item->anime_anilist_id ?? '', 'character' => $item->character->anilist_id ?? '']) }}"
                                            class="block bg-gray-800 text-white p-4 rounded-2xl shadow-md hover:shadow-lg transition hover:scale-[1.03]">
                                            @if ($image)
                                                <img src="{{ $image }}"
                                                    alt="{{ $item->character->name ?? $item->character_name }}"
                                                    class="w-full h-64 object-cover rounded-lg mb-4">
                                            @else
                                                <div
                                                    class="w-full h-64 bg-gray-600 flex items-center justify-center rounded-lg mb-4">
                                                    <span class="text-gray-300 text-sm">Sin imagen</span>
                                                </div>
                                            @endif
                                            <h3 class="text-lg font-bold mb-1 truncate">
                                                {{ $item->character->name ?? $item->character_name }}</h3>
                                            @if (!empty($item->anime_title))
                                                <p class="text-sm text-gray-300">{{ $item->anime_title }}</p>
                                            @endif
                                            @if ($item->score)
                                                <p class="text-sm text-gray-300">Puntuación: <span
                                                        class="font-semibold">{{ $item->score }}/10</span></p>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        @if (!$isOwner)
            <p class="text-gray-500 text-center mt-6"></p>
        @endif
    @endif
    {{-- =============================== --}}
    {{-- 🚫 PERFIL NO PÚBLICO --}}
    {{-- =============================== --}}
    @if (
        !$isOwner &&
            $animeFavorites->isEmpty() &&
            $characterFavorites->isEmpty() &&
            $visibleAnimeLists->isEmpty() &&
            $visibleCharacterLists->isEmpty())
        <p class="text-gray-500 text-center mt-10 text-lg">
            Este perfil no es público.
        </p>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Abrir modal de animes
            document.querySelectorAll('.open-list-modal').forEach(btn => {
                btn.addEventListener('click', () => {
                    const listId = btn.dataset.listId;
                    const modal = document.getElementById(`listModal-${listId}`);
                    if (modal) {
                        modal.classList.remove('hidden', 'opacity-0', 'scale-95');
                        modal.classList.add('flex', 'opacity-100', 'scale-100');
                    }
                });
            });

            // Abrir modal de personajes
            document.querySelectorAll('.open-character-list-modal').forEach(btn => {
                btn.addEventListener('click', () => {
                    const listId = btn.dataset.listId;
                    const modal = document.getElementById(`characterListModal-${listId}`);
                    if (modal) {
                        modal.classList.remove('hidden', 'opacity-0', 'scale-95');
                        modal.classList.add('flex', 'opacity-100', 'scale-100');
                    }
                });
            });

            // Cerrar modales
            document.querySelectorAll('.close-list-modal').forEach(btn => {
                btn.addEventListener('click', () => {
                    const modal = btn.closest('.fixed.inset-0');
                    if (modal) {
                        modal.classList.add('opacity-0', 'scale-95');
                        setTimeout(() => modal.classList.add('hidden'), 200);
                    }
                });
            });

            // Cerrar al click fuera del contenido
            document.querySelectorAll('.fixed.inset-0').forEach(modal => {
                modal.addEventListener('click', e => {
                    if (e.target === modal) {
                        modal.classList.add('opacity-0', 'scale-95');
                        setTimeout(() => modal.classList.add('hidden'), 200);
                    }
                });

                const content = modal.querySelector('div');
                if (content) content.addEventListener('click', e => e.stopPropagation());
            });
        });
    </script>

</x-app-layout>
