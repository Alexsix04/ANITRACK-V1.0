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

    <!-- ========================================= -->
    <!-- 🌟 FAVORITOS -->
    <!-- ========================================= -->
    <div class="max-w-5xl mx-auto px-6 py-12 space-y-10">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Favoritos</h2>

        <!-- 🎬 ANIMES FAVORITOS + 👤 PERSONAJES FAVORITOS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- 🎬 ANIMES FAVORITOS -->
            <div class="relative group cursor-pointer open-favorites-modal" id="openAnimesModal">
                <div class="aspect-[16/9] rounded-2xl overflow-hidden shadow-lg relative">
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
                                <img src="{{ $img }}" alt="Anime favorito" class="object-cover w-full h-full">
                            @endforeach
                            @for ($i = $animeImages->count(); $i < 4; $i++)
                                <div class="bg-gray-300"></div>
                            @endfor
                        </div>
                    @endif

                    <div
                        class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition">
                        <h3 class="text-white text-2xl font-bold mb-1">Animes Favoritos</h3>
                        @if (!$animeFavorites->isEmpty())
                            <span class="text-white text-sm">Ver colección completa</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 👤 PERSONAJES FAVORITOS -->
            <div class="relative group cursor-pointer open-favorites-modal" id="openCharsModal">
                <div class="aspect-[16/9] rounded-2xl overflow-hidden shadow-lg relative">
                    @php
                        $charImages = $characterFavorites->take(4)->pluck('character_image');
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
                        <h3 class="text-white text-2xl font-bold mb-1">Personajes Favoritos</h3>
                        @if (!$characterFavorites->isEmpty())
                            <span class="text-white text-sm">Ver colección completa</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ========================================= -->
    <!-- 📋 LISTAS ANIMES -->
    <!-- ========================================= -->
    <div class="max-w-5xl mx-auto px-6 py-12 space-y-10">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Mis Listas</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            @foreach ($defaultLists as $list)
                <div class="relative group cursor-pointer open-list-modal" data-list-id="{{ $list->id }}">
                    <div class="aspect-[16/9] rounded-2xl overflow-hidden shadow-lg relative">
                        @php
                            // 👇 igual que en tu ejemplo de favoritos
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

                        <div
                            class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <h3 class="text-white text-2xl font-bold mb-1">{{ $list->name }}</h3>
                            @if (!$list->items->isEmpty())
                                <span class="text-white text-sm">Ver colección completa</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <!-- ========================================= -->
    <!-- 📋 LISTAS DE PERSONAJES -->
    <!-- ========================================= -->
<div class="max-w-5xl mx-auto px-6 py-12 space-y-10">
    <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Mis Listas de Personajes</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        @foreach ($characterLists as $list)
            <div class="relative group cursor-pointer open-character-list-modal" data-list-id="{{ $list->id }}">
                <div class="aspect-[16/9] rounded-2xl overflow-hidden shadow-lg relative">
                    @php
                        // Tomar hasta 4 personajes para previsualizar
                        $characterImages = $list->items
                            ->take(4)
                            ->map(fn($item) => $item->character->image_url ?? $item->character_image)
                            ->filter();
                    @endphp

                    @if ($characterImages->isEmpty())
                        <div class="flex items-center justify-center h-full bg-gray-200 text-gray-500">
                            <span>Sin personajes en {{ $list->name }}</span>
                        </div>
                    @else
                        <div class="grid grid-cols-2 grid-rows-2 h-full w-full">
                            @foreach ($characterImages as $img)
                                <img src="{{ $img }}" alt="Personaje en {{ $list->name }}"
                                    class="object-cover w-full h-full">
                            @endforeach
                            @for ($i = $characterImages->count(); $i < 4; $i++)
                                <div class="bg-gray-300"></div>
                            @endfor
                        </div>
                    @endif

                    <div
                        class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition">
                        <h3 class="text-white text-2xl font-bold mb-1">{{ $list->name }}</h3>
                        @if (!$list->items->isEmpty())
                            <span class="text-white text-sm">Ver colección completa</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
    <!-- ========================================= -->
    <!-- 🪄 MODALES -->
    <!-- ========================================= -->
    <!-- MODALES DE FAVORITOS -->
    <div id="animesModal"
        class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 opacity-0 scale-95">
        <div class="bg-white p-6 rounded-xl shadow-xl w-11/12 max-w-5xl max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Todos los Animes Favoritos</h2>
                <button id="closeAnimesModal" class="text-gray-600 hover:text-gray-800">✕</button>
            </div>

            @if ($animeFavorites->isEmpty())
                <p class="text-gray-500">Aún no tienes animes en tus favoritos.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($animeFavorites as $fav)
                        @php
                            $anime = $fav->anime; // relación con la tabla animes
                        @endphp

                        @if ($anime)
                            {{-- ✅ Cambiamos el enlace para que use el anilist_id --}}
                            <a href="{{ url('/animes/' . $anime->anilist_id) }}"
                                class="bg-gray-800 text-white p-4 rounded-2xl shadow-md hover:shadow-lg transition block hover:scale-[1.03]">
                                <img src="{{ $anime->cover_image ?? $fav->anime_image }}"
                                    alt="{{ $anime->title ?? $fav->anime_title }}"
                                    class="w-full h-64 object-cover rounded-lg mb-4">
                                <h3 class="text-lg font-bold mb-2 truncate">
                                    {{ $anime->title ?? $fav->anime_title }}
                                </h3>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div id="charsModal"
        class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 opacity-0 scale-95">
        <div class="bg-white p-6 rounded-xl shadow-xl w-11/12 max-w-5xl max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Todos los Personajes Favoritos</h2>
                <button id="closeCharsModal" class="text-gray-600 hover:text-gray-800">✕</button>
            </div>

            @if ($characterFavorites->isEmpty())
                <p class="text-gray-500">Aún no tienes personajes en tus favoritos.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($characterFavorites as $fav)
                        <a href="{{ route('animes.characters.show', ['anime' => $fav->anime_anilist_id, 'character' => $fav->anilist_id]) }}"
                            class="bg-gray-800 text-white p-4 rounded-2xl shadow-md hover:shadow-lg transition block hover:scale-[1.03]">
                            <img src="{{ $fav->character_image }}" alt="{{ $fav->character_name }}"
                                class="w-full h-64 object-cover rounded-lg mb-4">
                            <h3 class="text-lg font-bold mb-2 truncate">{{ $fav->character_name }}</h3>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>


    <!-- MODALES DE LISTAS -->
    @foreach ($defaultLists as $list)
        <div id="listModal-{{ $list->id }}"
            class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 opacity-0 scale-95">
            <div class="bg-white p-6 rounded-xl shadow-xl w-11/12 max-w-5xl max-h-[80vh] overflow-y-auto relative">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $list->name }}</h2>
                    <button class="text-gray-600 hover:text-gray-800 close-list-modal">✕</button>
                </div>

                @if ($list->items->isEmpty())
                    <p class="text-gray-500">Esta lista está vacía.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($list->items as $item)
                            @php
                                // 🔹 Obtener imagen (cover del modelo Anime o la guardada en el item)
                                $image = $item->anime->cover_image ?? $item->anime_image;
                            @endphp

                            <div class="bg-gray-800 text-white p-4 rounded-2xl shadow-md hover:shadow-lg transition block hover:scale-[1.03] cursor-pointer open-submodal"
                                data-item-id="{{ $item->id }}" data-anime-id="{{ $item->anime_id }}"
                                data-anime-title="{{ $item->anime_title }}" data-anime-image="{{ $image }}"
                                data-anime-anilist_id="{{ $item->anime->anilist_id ?? '' }}"
                                data-anime-episodes="{{ $item->anime->episodes ?? 0 }}"
                                data-anime-status="{{ $item->status }}" data-anime-score="{{ $item->score }}"
                                data-anime-episode_progress="{{ $item->episode_progress }}"
                                data-anime-is_rewatch="{{ $item->is_rewatch }}"
                                data-anime-rewatch_count="{{ $item->rewatch_count }}"
                                data-anime-notes="{{ $item->notes }}">

                                {{-- 🔸 Imagen del anime o fallback --}}
                                @if ($image)
                                    <img src="{{ $image }}" alt="{{ $item->anime_title }}"
                                        class="w-full h-64 object-cover rounded-lg mb-4">
                                @else
                                    <div
                                        class="w-full h-64 bg-gray-600 flex items-center justify-center rounded-lg mb-4">
                                        <span class="text-gray-300 text-sm">Sin imagen</span>
                                    </div>
                                @endif

                                {{-- 🔸 Título del anime --}}
                                <h3 class="text-lg font-bold mb-1 truncate">
                                    {{ $item->anime->title ?? $item->anime_title }}</h3>

                                {{-- 🔸 Información adicional (opcional, puedes quitar si no quieres) --}}
                                <div class="text-sm text-gray-300 space-y-1">
                                    @if ($item->score)
                                        <p>Puntuación: <span class="font-semibold">{{ $item->score }}/10</span></p>
                                    @endif
                                    @if ($item->episode_progress)
                                        <p>Episodios vistos: <span
                                                class="font-semibold">{{ $item->episode_progress }}</span></p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    <!-- SUBMODAL DE ANIME EN LISTA -->
    <div id="animeSubmodal"
        class="hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[60] opacity-0 scale-95">
        <div
            class="bg-gray-100 p-8 rounded-2xl shadow-2xl w-11/12 max-w-4xl relative flex flex-col md:flex-row gap-6 transition-all duration-200">
            <button id="closeSubmodal"
                class="absolute top-4 right-5 text-gray-600 hover:text-gray-800 text-3xl font-bold">✕</button>

            <!-- Imagen -->
            <div class="relative flex-shrink-0 w-full md:w-1/3 group cursor-pointer" id="submodalImageContainer">
                <img id="submodalImage" src="" alt=""
                    class="w-full h-80 object-cover rounded-xl shadow-md transition-transform duration-300 group-hover:scale-105">
                <div
                    class="absolute inset-0 bg-black bg-opacity-40 rounded-xl opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                    <span class="text-white text-lg font-semibold">Ver anime completo</span>
                </div>
            </div>

            <!-- Información -->
            <div class="flex-1 text-gray-800 space-y-3" id="submodalInfo">
                <h3 id="submodalTitle" class="text-3xl font-extrabold text-gray-900 mb-3"></h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2">
                    <p><span class="font-semibold">Estado:</span> <span id="submodalStatus">—</span></p>
                    <p><span class="font-semibold">Puntuación:</span> <span id="submodalScore">—</span></p>
                    <p><span class="font-semibold">Progreso:</span> <span id="submodalProgress">—</span></p>
                    <p><span class="font-semibold">Reviendo:</span> <span id="submodalRewatch">—</span></p>
                    <p><span class="font-semibold">Veces visto:</span> <span id="submodalRewatchCount">—</span></p>
                </div>

                <div class="mt-4">
                    <h4 class="font-semibold text-lg mb-1">Notas:</h4>
                    <p id="submodalNotes"
                        class="text-sm text-gray-700 bg-white p-3 rounded-lg border border-gray-200 shadow-inner">
                        Sin notas
                    </p>
                </div>

                <!-- Botones -->
                <div class="mt-6 flex gap-3">
                    <button id="editInfoBtn"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-5 py-2 rounded-lg shadow transition">
                        Editar info
                    </button>
                    <button id="deleteFromListBtn"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded-lg shadow transition">
                        Eliminar de la lista
                    </button>

                </div>
            </div>
        </div>
    </div>

    <!-- ========================================= -->
    <!-- 🧩 JS DE MODALES (favoritos + listas) -->
    <!-- ========================================= -->
    <script>
        function setupModal(openBtn, modal, closeBtn) {
            if (!modal) return;
            if (openBtn) openBtn.addEventListener('click', () => {
                modal.classList.remove('hidden', 'opacity-0', 'scale-95');
                modal.classList.add('opacity-100', 'scale-100');
            });
            if (closeBtn) closeBtn.addEventListener('click', () => closeModal(modal));
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal(modal);
            });
            const modalContent = modal.querySelector('div');
            if (modalContent) modalContent.addEventListener('click', e => e.stopPropagation());
        }

        function closeModal(modal) {
            modal.classList.add('opacity-0', 'scale-95');
            setTimeout(() => modal.classList.add('hidden'), 200);
        }

        // === FAVORITOS ===
        setupModal(
            document.getElementById('openAnimesModal'),
            document.getElementById('animesModal'),
            document.getElementById('closeAnimesModal')
        );
        setupModal(
            document.getElementById('openCharsModal'),
            document.getElementById('charsModal'),
            document.getElementById('closeCharsModal')
        );

        // === LISTAS ===
        document.querySelectorAll('.open-list-modal').forEach(button => {
            button.addEventListener('click', () => {
                const listId = button.dataset.listId;
                const modal = document.getElementById(`listModal-${listId}`);
                if (!modal) return;
                modal.classList.remove('hidden', 'opacity-0', 'scale-95');
                modal.classList.add('opacity-100', 'scale-100');

                // cerrar al clicar fuera del modal
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) closeModal(modal);
                });
            });
        });

        document.querySelectorAll('.close-list-modal').forEach(button => {
            button.addEventListener('click', () => {
                const modal = button.closest('.fixed');
                closeModal(modal);
            });
        });
    </script>

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
    <!-- ===================================================== -->
    <!-- ⚙️ SCRIPT SUBMODAL DE LISTAS ANIME -->
    <!-- ===================================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const submodal = document.getElementById('animeSubmodal');
            const closeBtn = document.getElementById('closeSubmodal');
            const editBtn = document.getElementById('editInfoBtn');
            const img = document.getElementById('submodalImage');
            const title = document.getElementById('submodalTitle');
            const imgContainer = document.getElementById('submodalImageContainer');

            const statusMap = {
                'watching': 'Viendo',
                'completed': 'Completado',
                'on_hold': 'En pausa',
                'dropped': 'Abandonado',
                'plan_to_watch': 'Pendiente'
            };

            let currentAnime = null;
            let editing = false;

            // === Abrir submodal ===
            document.querySelectorAll('.open-submodal').forEach(card => {
                card.addEventListener('click', () => {
                    currentAnime = {
                        itemId: card.dataset.itemId,
                        animeId: card.dataset.animeId,
                        anilist_id: card.dataset.animeAnilist_id,
                        episodes: card.dataset.animeEpisodes,
                        title: card.dataset.animeTitle,
                        image: card.dataset.animeImage,
                        status: card.dataset.animeStatus,
                        score: card.dataset.animeScore,
                        progress: card.dataset.animeEpisode_progress,
                        rewatch: card.dataset.animeIs_rewatch,
                        rewatchCount: card.dataset.animeRewatch_count,
                        notes: card.dataset.animeNotes
                    };

                    if (!currentAnime.itemId) {
                        console.error("❌ FALTA data-item-id en el elemento .open-submodal");
                        alert("Error interno: falta el ID del registro en la lista.");
                        return;
                    }

                    // Mostrar datos
                    img.src = currentAnime.image;
                    title.textContent = currentAnime.title;
                    document.getElementById('submodalStatus').textContent = statusMap[currentAnime
                        .status?.toLowerCase()] || '—';
                    document.getElementById('submodalScore').textContent = currentAnime.score ||
                        '—';
                    document.getElementById('submodalProgress').textContent = currentAnime
                        .progress || '—';
                    document.getElementById('submodalRewatch').textContent = currentAnime.rewatch ==
                        '1' ? 'Sí' : 'No';
                    document.getElementById('submodalRewatchCount').textContent = currentAnime
                        .rewatchCount || '—';
                    document.getElementById('submodalNotes').textContent = currentAnime.notes ||
                        'Sin notas';

                    // Click en imagen = ir al anime
                    imgContainer.onclick = () => {
                        window.location.href = `/animes/${currentAnime.anilist_id}`;
                    };




                    // Mostrar modal
                    submodal.classList.remove('hidden', 'opacity-0', 'scale-95');
                    submodal.classList.add('opacity-100', 'scale-100');
                });
            });

            // === Cerrar modal ===
            closeBtn.addEventListener('click', closeModal);
            submodal.addEventListener('click', e => {
                if (e.target === submodal) closeModal();
            });

            function closeModal() {
                submodal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => submodal.classList.add('hidden'), 200);
            }

            // === Modo edición ===
            editBtn.addEventListener('click', async () => {
                if (!currentAnime) return alert("No hay anime seleccionado.");

                let status = document.getElementById('submodalStatus');
                let score = document.getElementById('submodalScore');
                let progress = document.getElementById('submodalProgress');
                let rewatch = document.getElementById('submodalRewatch');
                let rewatchCount = document.getElementById('submodalRewatchCount');
                let notes = document.getElementById('submodalNotes');

                if (!editing) {
                    editing = true;
                    editBtn.textContent = 'Guardar cambios';

                    status.outerHTML = `
                <select id="submodalStatus" class="border rounded-lg px-2 py-1 text-sm">
                    <option value="watching">Viendo</option>
                    <option value="completed">Completado</option>
                    <option value="on_hold">En pausa</option>
                    <option value="dropped">Abandonado</option>
                    <option value="plan_to_watch">Pendiente</option>
                </select>`;
                    document.getElementById('submodalStatus').value = currentAnime.status;

                    score.outerHTML =
                        `<input id="submodalScore" type="number" min="0" max="10" value="${currentAnime.score || ''}" class="border rounded-lg px-2 py-1 w-16">`;
                    const maxEpisodes = currentAnime.episodes && currentAnime.episodes > 0 ?
                        currentAnime.episodes : 9999;
                    progress.outerHTML = `
                    <input id="submodalProgress" type="number" min="0" max="${maxEpisodes}"
                    value="${currentAnime.progress || ''}" 
                    class="border rounded-lg px-2 py-1 w-20"
                    title="Máximo: ${maxEpisodes} episodios">`;

                    rewatch.outerHTML = `
                <select id="submodalRewatch" class="border rounded-lg px-2 py-1 text-sm">
                    <option value="0" ${currentAnime.rewatch == '0' ? 'selected' : ''}>No</option>
                    <option value="1" ${currentAnime.rewatch == '1' ? 'selected' : ''}>Sí</option>
                </select>`;
                    rewatchCount.outerHTML =
                        `<input id="submodalRewatchCount" type="number" min="0" value="${currentAnime.rewatchCount || ''}" class="border rounded-lg px-2 py-1 w-20">`;
                    notes.outerHTML =
                        `<textarea id="submodalNotes" class="w-full border rounded-lg px-3 py-2 text-sm">${currentAnime.notes || ''}</textarea>`;

                } else {
                    // === Guardar cambios ===
                    editing = false;
                    editBtn.textContent = 'Editar info';

                    const statusValue = document.getElementById('submodalStatus').value;
                    const scoreValue = document.getElementById('submodalScore').value;
                    let progressValue = document.getElementById('submodalProgress').value;
                    const rewatchValue = document.getElementById('submodalRewatch').value;
                    const rewatchCountValue = document.getElementById('submodalRewatchCount').value;
                    const notesValue = document.getElementById('submodalNotes').value;

                    // ✅ Si el estado es "completed", forzar progreso al total de episodios
                    if (statusValue === 'completed') {
                        const totalEpisodes = currentAnime.episodes && currentAnime.episodes > 0 ?
                            currentAnime.episodes : null;
                        if (totalEpisodes) {
                            progressValue = totalEpisodes;
                        }
                    }

                    const newData = {
                        status: statusValue,
                        score: scoreValue,
                        episode_progress: progressValue,
                        is_rewatch: rewatchValue,
                        rewatch_count: rewatchCountValue,
                        notes: notesValue,
                    };

                    try {
                        const res = await fetch(`/anime-list/${currentAnime.itemId}/update`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(newData)
                        });

                        const result = await res.json();

                        if (!res.ok) {
                            console.error("Error al actualizar:", res.status, result);
                            alert(`Error ${res.status}: no se pudieron guardar los cambios.`);
                            return;
                        }

                        // Caso especial: se movió automáticamente de Pendientes a Vistos
                        if (result.action === 'moved_new' || result.action === 'moved_existing') {
                            alert(result.message);
                            closeModal();
                            location.reload();
                            return;
                        }

                        // Si no se movió, solo actualizamos visualmente los campos
                        status = document.getElementById('submodalStatus');
                        score = document.getElementById('submodalScore');
                        progress = document.getElementById('submodalProgress');
                        rewatch = document.getElementById('submodalRewatch');
                        rewatchCount = document.getElementById('submodalRewatchCount');
                        notes = document.getElementById('submodalNotes');

                        status.outerHTML =
                            `<span id="submodalStatus">${statusMap[newData.status] || '—'}</span>`;
                        score.outerHTML = `<span id="submodalScore">${newData.score || '—'}</span>`;
                        progress.outerHTML =
                            `<span id="submodalProgress">${newData.episode_progress || '—'}</span>`;
                        rewatch.outerHTML =
                            `<span id="submodalRewatch">${newData.is_rewatch == '1' ? 'Sí' : 'No'}</span>`;
                        rewatchCount.outerHTML =
                            `<span id="submodalRewatchCount">${newData.rewatch_count || '—'}</span>`;
                        notes.outerHTML =
                            `<p id="submodalNotes" class="text-sm text-gray-700 bg-white p-3 rounded-lg border border-gray-200 shadow-inner">${newData.notes || 'Sin notas'}</p>`;

                        alert(result.message || 'Cambios guardados correctamente.');

                    } catch (err) {
                        console.error("Error de conexión:", err);
                        alert('Error de conexión con el servidor.');
                    }
                }

            });
            // === Eliminar de la lista ===
            const deleteBtn = document.getElementById('deleteFromListBtn');

            deleteBtn.addEventListener('click', async () => {
                if (!currentAnime || !currentAnime.itemId) {
                    alert("❌ No hay anime seleccionado para eliminar.");
                    return;
                }

                if (!confirm(`¿Seguro que quieres eliminar "${currentAnime.title}" de tu lista?`))
                    return;

                try {
                    const res = await fetch(`/anime-list/${currentAnime.itemId}/delete`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        }
                    });

                    const result = await res.json();

                    if (!res.ok) {
                        console.error("Error al eliminar:", result);
                        alert(result.message || 'Error al eliminar el anime.');
                        return;
                    }

                    alert(result.message || '✅ Anime eliminado correctamente.');
                    closeModal(); // Cierra el submodal
                    location.reload(); // Recarga para actualizar la lista

                } catch (err) {
                    console.error("Error de conexión:", err);
                    alert('Error de conexión con el servidor.');
                }
            });


        });
    </script>

    <!-- ========================================= -->
    <!-- ✨ ESTILOS DE TRANSICIÓN -->
    <!-- ========================================= -->
    <style>
        #animesModal,
        #charsModal,
        [id^="listModal-"] {
            transition: opacity 0.2s ease, transform 0.2s ease;
            transform-origin: center;
        }
    </style>

</x-app-layout>