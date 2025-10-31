<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Perfil de {{ $user->name }}
        </h2>
    </x-slot>

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
                    src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/avatar-default.png') }}"
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
    <!-- 🌟 FAVORITOS (vista de colección con collage) -->
    <!-- ========================================= -->
    <div class="max-w-5xl mx-auto px-6 py-12 space-y-10">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Favoritos</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- 🎬 ANIMES FAVORITOS -->
            <div class="relative group cursor-pointer" id="openAnimesModal">
                <div class="aspect-[16/9] rounded-2xl overflow-hidden shadow-lg relative">
                    @php $animeImages = $animeFavorites->take(4)->pluck('anime_image'); @endphp
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
            <div class="relative group cursor-pointer" id="openCharsModal">
                <div class="aspect-[16/9] rounded-2xl overflow-hidden shadow-lg relative">
                    @php $charImages = $characterFavorites->take(4)->pluck('character_image'); @endphp
                    @if ($charImages->isEmpty())
                        <div class="flex items-center justify-center h-full bg-gray-200 text-gray-500">
                            <span>Sin personajes favoritos</span>
                        </div>
                    @else
                        <div class="grid grid-cols-2 grid-rows-2 h-full w-full">
                            @foreach ($charImages as $img)
                                <img src="{{ $img }}" alt="Personaje favorito" class="object-cover w-full h-full">
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
    <!-- 🪄 MODALES DE COLECCIONES -->
    <!-- ========================================= -->
    <!-- Modal de Animes -->
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
                        <a href="{{ route('animes.show', $fav->anime_id) }}"
                            class="bg-gray-800 text-white p-4 rounded-2xl shadow-md hover:shadow-lg transition block hover:scale-[1.03]">
                            <img src="{{ $fav->anime_image }}" alt="{{ $fav->anime_title }}"
                                class="w-full h-64 object-cover rounded-lg mb-4">
                            <h3 class="text-lg font-bold mb-2 truncate">{{ $fav->anime_title }}</h3>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Personajes -->
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
                        <a href="{{ route('animes.characters.show', ['anime' => $fav->anime_id, 'character' => $fav->character_id]) }}"
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

    <!-- ========================================= -->
    <!-- 🧩 JS DE MODALES -->
    <!-- ========================================= -->
    <script>
        function setupModal(openBtn, modal, closeBtn) {
            if (!modal) return;

            // abrir modal
            if (openBtn) openBtn.addEventListener('click', () => {
                modal.classList.remove('hidden', 'opacity-0', 'scale-95');
                modal.classList.add('opacity-100', 'scale-100');
            });

            // cerrar modal con botón
            if (closeBtn) closeBtn.addEventListener('click', () => {
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            });

            // cerrar modal al hacer clic fuera
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => modal.classList.add('hidden'), 200);
                }
            });

            // evitar cerrar al hacer clic dentro del contenido
            const modalContent = modal.querySelector('div');
            if (modalContent) modalContent.addEventListener('click', e => e.stopPropagation());
        }

        setupModal(
            document.getElementById('openEditModal'),
            document.getElementById('editModal'),
            document.getElementById('closeEditModal')
        );
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
    </script>

    <!-- ========================================= -->
    <!-- ✨ ESTILOS DE TRANSICIÓN PARA MODALES -->
    <!-- ========================================= -->
    <style>
        #editModal,
        #animesModal,
        #charsModal {
            transition: opacity 0.2s ease, transform 0.2s ease;
            transform-origin: center;
        }
    </style>
</x-app-layout>