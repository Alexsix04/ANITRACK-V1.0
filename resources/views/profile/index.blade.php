<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Perfil de {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white shadow sm:rounded-lg p-6 flex flex-col md:flex-row items-center md:items-start gap-6 relative">

                <!-- Foto de perfil -->
                <div class="flex-shrink-0 relative group cursor-pointer" id="avatar-container">
                    <img class="h-32 w-32 rounded-full object-cover border border-gray-300 shadow-sm"
                        src="{{ $user->avatar_url }}" alt="Avatar de {{ $user->name }}">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                        <span class="text-white text-sm">Editar</span>
                    </div>
                </div>

                <!-- Información del usuario -->
                <div class="flex-1">
                    <h3 class="text-2xl font-bold mb-2">{{ $user->name }}</h3>
                    <p class="text-gray-600 mb-4">{{ $user->bio ?? 'Este usuario no ha agregado una descripción.' }}</p>

                    <!-- Botón para editar perfil completo -->
                    <a href="{{ route('profile.edit') }}"
                        class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Editar Perfil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================== -->
    <!-- ANIMES FAVORITOS DEL USUARIO -->
    <!-- ======================== -->
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">🎴 Animes Favoritos</h2>

                @if ($favorites->isEmpty())
                    <p class="text-gray-500">Aún no tienes animes marcados como favoritos.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($favorites as $fav)
                            <div class="bg-gray-100 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                                <img src="{{ $fav->anime_image }}" 
                                     alt="{{ $fav->anime_title }}"
                                     class="w-full h-64 object-cover">

                                <div class="p-4 flex flex-col justify-between h-full">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                        {{ $fav->anime_title }}
                                    </h3>

                                    <div class="flex justify-between items-center mt-auto">
                                        <a href="{{ route('animes.show', $fav->anime_id) }}"
                                           class="text-indigo-600 hover:underline">
                                           Ver detalles
                                        </a>

                                        <form action="{{ route('favorites.anime.destroy', $fav->anime_id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-yellow-500 hover:text-yellow-600 transition"
                                                    title="Quitar de favoritos">
                                                <svg xmlns="http://www.w3.org/2000/svg" 
                                                     class="h-6 w-6" fill="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.782 
                                                             1.4 8.172L12 18.896l-7.334 3.868 
                                                             1.4-8.172-5.934-5.782 8.2-1.192z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal oculto -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4">Editar Avatar y Descripción</h2>

            <form method="POST" action="{{ route('profile.updateBioAvatar') }}" enctype="multipart/form-data">
                @csrf

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
                    <button type="button" id="closeModal"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const avatarContainer = document.getElementById('avatar-container');
        const modal = document.getElementById('editModal');
        const closeModal = document.getElementById('closeModal');

        avatarContainer.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });

        closeModal.addEventListener('click', () => {
            modal.classList.add('hidden');
        });
    </script>
</x-app-layout>