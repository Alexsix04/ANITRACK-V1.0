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
            <div class="relative flex-shrink-0 mb-4 sm:mb-0 sm:mr-8 group">
                <img class="h-36 w-36 sm:h-36 sm:w-36 rounded-full object-cover border-4 border-white shadow-lg"
                    src="{{ $user->avatar_url }}" alt="Avatar de {{ $user->name }}">
                @if ($isOwner)
                    <div class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer"
                        id="openEditModal">
                        <span class="text-white text-sm font-medium">Editar</span>
                    </div>
                @endif
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
                        class="block w-full text-sm border border-gray-300 rounded p-2 mb-2">
                    <button type="button" id="openAvatarModal"
                        class="px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
                        Seleccionar avatar por defecto
                    </button>
                    <!-- Preview del avatar seleccionado -->
                    <img id="avatarPreview" src="{{ $user->avatar_url }}"
                        class="h-20 w-20 rounded-full mt-2 object-cover">
                </div>

                <!-- Bio -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="bio" rows="3" class="w-full border rounded-md p-2">{{ $user->bio }}</textarea>
                </div>

                <!-- Estado del perfil -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Visibilidad del perfil</label>
                    <select name="is_public" class="w-full border rounded-md p-2">
                        <option value="1" {{ $user->is_public ? 'selected' : '' }}>Público</option>
                        <option value="0" {{ $user->is_public ? '' : 'selected' }}>Privado</option>
                    </select>
                </div>

                <!-- Botón Opciones Avanzadas -->
                <div class="mb-4">
                    <a href="{{ route('profile.edit') }}"
                        class="w-full inline-block text-center px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
                        Opciones Avanzadas
                    </a>
                </div>

                <!-- Boton de Guardado -->
                <div class="flex justify-end gap-2">
                    <button type="button" id="closeEditModal"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Submodal de Avatares -->
    <div id="avatarModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 opacity-0 scale-95">
        <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-lg">
            <h2 class="text-lg font-semibold mb-4">Seleccionar Avatar</h2>
            <div class="grid grid-cols-4 gap-4 max-h-80 overflow-y-auto">
                @foreach (File::files(public_path('images/avatars')) as $avatar)
                    @php
                        $avatarUrl = asset('images/avatars/' . $avatar->getFilename());
                    @endphp
                    <img src="{{ $avatarUrl }}" alt="Avatar"
                        class="w-20 h-20 rounded-full cursor-pointer border-2 border-transparent hover:border-indigo-500"
                        onclick="selectAvatar('{{ $avatarUrl }}')">
                @endforeach
            </div>
            <div class="flex justify-end mt-4">
                <button type="button" id="closeAvatarModal"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
            </div>
        </div>
    </div>


    <div class="flex flex-col lg:flex-row max-w-6xl mx-auto px-6 py-12 gap-8">

        <!-- Sección estadísticas -->
        <div class="w-full lg:w-80 lg:flex-shrink-0">
            <div class="bg-white p-6 rounded-xl shadow mb-6 mt-8 lg:mt-0">
                <h2 class="text-xl font-semibold mb-4">Estadísticas del perfil</h2>
                <div class="grid grid-cols-1 gap-4">

                    <!-- Total de animes vistos -->
                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                        <h3 class="text-sm text-gray-600">Total de animes vistos</h3>
                        <p class="text-2xl font-bold">{{ $totalVistos }}</p>
                    </div>

                    <!-- Anime favorito -->
                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                        <h3 class="text-sm text-gray-600">Anime favorito</h3>
                        @if ($animeFavorito)
                            <p class="text-lg font-semibold">{{ $animeFavorito->title }}</p>
                        @else
                            <p class="text-gray-400">Sin datos</p>
                        @endif
                    </div>

                    <!-- Nota media -->
                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                        <h3 class="text-sm text-gray-600">Nota media</h3>
                        <p class="text-2xl font-bold">{{ round($notaMedia, 2) ?? 0 }}</p>
                        <div class="w-full bg-gray-200 h-2 rounded-full mt-2">
                            <div class="bg-indigo-600 h-2 rounded-full"
                                style="width: {{ $notaMedia ? ($notaMedia / 10) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Personaje favorito -->
                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                        <h3 class="text-sm text-gray-600">Personaje favorito</h3>
                        @if ($characterFavorito)
                            <p class="text-lg font-semibold">{{ $characterFavorito->name }}</p>
                            @if (isset($characterFavorito->pivot->score))
                                <p class="text-sm text-gray-500">Score: {{ $characterFavorito->pivot->score }}</p>
                            @endif
                        @else
                            <p class="text-gray-400">Sin datos</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        <!-- Contenedor centrado del resto del contenido -->
        <div class="flex-1 space-y-12">

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

                    <!-- Grid responsive: 1 columna en móvil, 2 columnas en sm y mayores -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach ($visibleAnimeLists as $list)
                            <div class="relative group cursor-pointer open-list-modal"
                                data-list-id="{{ $list->id }}">
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

                                    <!-- Overlay -->
                                    <div
                                        class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                        <h3 class="text-white text-2xl font-bold mb-1">{{ $list->name }}</h3>
                                        @if ($list->items->isNotEmpty())
                                            <span class="text-white text-sm">Ver colección completa</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Modal -->
                                <div id="listModal-{{ $list->id }}"
                                    class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
                                    <div
                                        class="bg-white w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-xl p-6 shadow-xl flex flex-col gap-6 animate-scale-in relative">

                                        <!-- Encabezado adaptable -->
                                        <div class="relative flex flex-col gap-3">

                                            <!-- Título + descripción -->
                                            <div class="pr-14"> <!-- Deja espacio para los botones -->
                                                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">
                                                    {{ $list->name }}
                                                </h2>

                                                @if ($list->description)
                                                    <p class="text-gray-600 text-sm sm:text-base mt-1 break-words">
                                                        {{ $list->description }}
                                                    </p>
                                                @endif

                                                <p class="text-gray-500 text-sm mt-2">
                                                    Estado:
                                                    <span
                                                        class="{{ $list->is_public ? 'text-green-600' : 'text-gray-400' }}">
                                                        {{ $list->is_public ? 'Pública' : 'Privada' }}
                                                    </span>
                                                </p>
                                            </div>

                                            <!-- Botones fijos arriba a la derecha -->
                                            <div class="absolute right-0 top-0 flex items-center gap-3">

                                                <!-- Guardar -->
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
                                                    }" x-cloak>

                                                        <button @click.stop="toggleSave()"
                                                            class="group p-1 bg-white rounded-full shadow hover:shadow-md transition">
                                                            <svg x-show="!saved" xmlns="http://www.w3.org/2000/svg"
                                                                fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor"
                                                                class="w-7 h-7 text-gray-400 group-hover:text-blue-600 transition-all duration-200">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M5 5v14l7-5 7 5V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2z" />
                                                            </svg>

                                                            <svg x-show="saved" xmlns="http://www.w3.org/2000/svg"
                                                                fill="currentColor" viewBox="0 0 24 24"
                                                                class="w-7 h-7 text-blue-600 transition-all duration-200">
                                                                <path
                                                                    d="M5 3a2 2 0 0 0-2 2v16l9-6 9 6V5a2 2 0 0 0-2-2H5z" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @endif

                                                <!-- Cerrar -->
                                                <button
                                                    class="close-list-modal text-gray-600 hover:text-gray-800 text-2xl font-bold rounded-full w-8 h-8 flex items-center justify-center bg-white shadow hover:shadow-md transition">
                                                    ✕
                                                </button>

                                            </div>

                                        </div>

                                        <!-- Contenido de la lista -->
                                        @if ($list->items->isEmpty())
                                            <p class="text-gray-500 text-center py-10">Esta lista está vacía.</p>
                                        @else
                                            <div
                                                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                                                @foreach ($list->items as $item)
                                                    @php $image = $item->anime->cover_image ?? $item->anime_image; @endphp

                                                    <a href="{{ url('/animes/' . ($item->anime->anilist_id ?? '')) }}"
                                                        class="block bg-gray-800 text-white p-3 sm:p-4 rounded-2xl shadow-md hover:shadow-lg transition hover:scale-[1.03]">

                                                        @if ($image)
                                                            <img src="{{ $image }}"
                                                                alt="{{ $item->anime_title }}"
                                                                class="w-full aspect-[3/4] object-cover rounded-lg mb-3 sm:mb-4">
                                                        @else
                                                            <div
                                                                class="w-full aspect-[3/4] bg-gray-600 flex items-center justify-center rounded-lg mb-3 sm:mb-4">
                                                                <span class="text-gray-300 text-sm">Sin imagen</span>
                                                            </div>
                                                        @endif

                                                        <h3 class="text-sm sm:text-lg font-bold mb-1 truncate">
                                                            {{ $item->anime->title ?? $item->anime_title }}
                                                        </h3>

                                                        @if ($item->score)
                                                            <p class="text-xs sm:text-sm text-gray-300">
                                                                Puntuación:
                                                                <span
                                                                    class="font-semibold">{{ $item->score }}/10</span>
                                                            </p>
                                                        @endif

                                                        @if ($item->episode_progress)
                                                            <p class="text-xs sm:text-sm text-gray-300">
                                                                Episodios vistos:
                                                                <span
                                                                    class="font-semibold">{{ $item->episode_progress }}</span>
                                                            </p>
                                                        @endif
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                    </div>
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

                    <!-- Grid responsive: 1 columna en móvil, 2 columnas en sm y mayores -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
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
                                                <img src="{{ $img }}"
                                                    alt="Personaje en {{ $list->name }}"
                                                    class="object-cover w-full h-full">
                                            @endforeach
                                            @for ($i = $charImages->count(); $i < 4; $i++)
                                                <div class="bg-gray-300"></div>
                                            @endfor
                                        </div>
                                    @endif

                                    <!-- Overlay -->
                                    <div
                                        class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                        <h3 class="text-white text-2xl font-bold mb-1">{{ $list->name }}</h3>
                                        @if ($list->items->isNotEmpty())
                                            <span class="text-white text-sm">Ver colección completa</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Modal -->
                                <div id="characterListModal-{{ $list->id }}"
                                    class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
                                    <div
                                        class="bg-white w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-xl p-5 shadow-xl flex flex-col gap-6 animate-scale-in">

                                        <!-- Encabezado -->
                                        <div class="relative flex flex-col gap-3">

                                            <!-- Botones fijos arriba a la derecha -->
                                            <div class="absolute top-3 right-3 flex items-center gap-3 z-10">

                                                <!-- Botón guardar para visitantes -->
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
                                                    }">

                                                        <button @click.stop="toggleSave()"
                                                            class="group p-1 bg-white rounded-full shadow hover:shadow-md transition focus:outline-none">

                                                            <!-- Icono no guardado -->
                                                            <svg x-show="!saved" xmlns="http://www.w3.org/2000/svg"
                                                                fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor"
                                                                class="w-8 h-8 text-gray-400 group-hover:text-blue-600 transition-all duration-200">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M5 5v14l7-5 7 5V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2z" />
                                                            </svg>

                                                            <!-- Icono guardado -->
                                                            <svg x-show="saved" xmlns="http://www.w3.org/2000/svg"
                                                                fill="currentColor" viewBox="0 0 24 24"
                                                                class="w-8 h-8 text-blue-600 transition-all duration-200">
                                                                <path
                                                                    d="M5 3a2 2 0 0 0-2 2v16l9-6 9 6V5a2 2 0 0 0-2-2H5z" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @endif

                                                <!-- Botón de cierre -->
                                                <button
                                                    class="close-list-modal text-gray-600 hover:text-gray-800 text-2xl font-bold rounded-full w-8 h-8 flex items-center justify-center bg-white shadow hover:shadow-md transition">
                                                    ✕
                                                </button>

                                            </div>

                                            <!-- Título + descripción -->
                                            <div class="pr-16"> <!-- Espacio para los botones en la derecha -->
                                                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">
                                                    {{ $list->name }}
                                                </h2>

                                                @if ($list->description)
                                                    <p class="text-gray-600 text-sm sm:text-base mt-1">
                                                        {{ $list->description }}
                                                    </p>
                                                @endif

                                                <p class="text-gray-500 text-sm mt-2">
                                                    Estado:
                                                    <span
                                                        class="{{ $list->is_public ? 'text-green-600' : 'text-gray-400' }}">
                                                        {{ $list->is_public ? 'Pública' : 'Privada' }}
                                                    </span>
                                                </p>
                                            </div>

                                        </div>

                                        <!-- Contenido de la lista -->
                                        @if ($list->items->isEmpty())
                                            <p class="text-gray-500 text-center py-10">Esta lista está vacía.</p>
                                        @else
                                            <div
                                                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                                                @foreach ($list->items as $item)
                                                    @php $image = $item->character->image_url ?? $item->character_image; @endphp

                                                    <a href="{{ route('animes.characters.show', ['anime' => $item->anime_anilist_id ?? '', 'character' => $item->character->anilist_id ?? '']) }}"
                                                        class="block bg-gray-800 text-white p-3 sm:p-4 rounded-2xl shadow-md hover:shadow-lg transition hover:scale-[1.03]">

                                                        @if ($image)
                                                            <img src="{{ $image }}"
                                                                alt="{{ $item->character->name ?? $item->character_name }}"
                                                                class="w-full aspect-[3/4] object-cover rounded-lg mb-3 sm:mb-4">
                                                        @else
                                                            <div
                                                                class="w-full aspect-[3/4] bg-gray-600 flex items-center justify-center rounded-lg mb-3 sm:mb-4">
                                                                <span class="text-gray-300 text-sm">Sin imagen</span>
                                                            </div>
                                                        @endif

                                                        <h3 class="text-sm sm:text-lg font-bold mb-1 truncate">
                                                            {{ $item->character->name ?? $item->character_name }}
                                                        </h3>

                                                        @if (!empty($item->anime_title))
                                                            <p class="text-xs sm:text-sm text-gray-300">
                                                                {{ $item->anime_title }}
                                                            </p>
                                                        @endif

                                                        @if ($item->score)
                                                            <p class="text-xs sm:text-sm text-gray-300">
                                                                Puntuación: <span
                                                                    class="font-semibold">{{ $item->score }}/10</span>
                                                            </p>
                                                        @endif
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                    </div>
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
            <!-- ===================================================== -->
            <!-- ⚙️ SCRIPT MODAL DE EDICIÓN DE PERFIL -->
            <!-- ===================================================== -->
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const openEditBtn = document.getElementById('openEditModal');
                    const editModal = document.getElementById('editModal');
                    const closeEditBtn = document.getElementById('closeEditModal');

                    if (!openEditBtn || !editModal) return;

                    // Abrir modal
                    openEditBtn.addEventListener('click', () => {
                        editModal.classList.remove('hidden', 'opacity-0', 'scale-95');
                        editModal.classList.add('flex', 'opacity-100', 'scale-100');
                    });

                    // Cerrar modal (botón o clic fuera)
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
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const openAvatarBtn = document.getElementById('openAvatarModal');
                    const avatarModal = document.getElementById('avatarModal');
                    const closeAvatarBtn = document.getElementById('closeAvatarModal');
                    const avatarPreview = document.getElementById('avatarPreview');

                    // Abrir submodal
                    openAvatarBtn.addEventListener('click', () => {
                        avatarModal.classList.remove('hidden', 'opacity-0', 'scale-95');
                        avatarModal.classList.add('flex', 'opacity-100', 'scale-100');
                    });

                    // Cerrar submodal
                    closeAvatarBtn.addEventListener('click', () => {
                        avatarModal.classList.add('opacity-0', 'scale-95');
                        setTimeout(() => avatarModal.classList.add('hidden'), 200);
                    });

                    avatarModal.addEventListener('click', (e) => {
                        if (e.target === avatarModal) {
                            avatarModal.classList.add('opacity-0', 'scale-95');
                            setTimeout(() => avatarModal.classList.add('hidden'), 200);
                        }
                    });

                    // Seleccionar avatar por defecto
                    window.selectAvatar = function(url) {
                        // Actualizar preview
                        avatarPreview.src = url;

                        // Crear/actualizar input hidden para enviar al backend
                        let hiddenInput = document.querySelector('input[name="selected_default"]');
                        if (!hiddenInput) {
                            hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'selected_default';
                            avatarPreview.parentNode.appendChild(hiddenInput);
                        }

                        // Guardar solo el nombre del archivo
                        hiddenInput.value = url.split('/').pop();

                        // Cerrar submodal
                        avatarModal.classList.add('opacity-0', 'scale-95');
                        setTimeout(() => avatarModal.classList.add('hidden'), 200);
                    }
                });
            </script>

</x-app-layout>
