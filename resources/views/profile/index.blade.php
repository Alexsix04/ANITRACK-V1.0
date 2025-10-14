<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Perfil de {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6 flex flex-col md:flex-row items-center md:items-start gap-6 relative">

                <!-- Foto de perfil -->
                <div class="flex-shrink-0 relative group cursor-pointer" id="avatar-container">
                    <img class="h-32 w-32 rounded-full object-cover border border-gray-300"
                         src="{{ $user->avatar }}"
                         alt="Avatar de {{ $user->name }}">
                    <div class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
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

    <!-- Modal oculto -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4">Editar Avatar y Descripción</h2>

            <form method="POST" action="{{ route('profile.updateBioAvatar') }}" enctype="multipart/form-data">
                @csrf

                <!-- Avatar -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo Avatar</label>
                    <input type="file" name="avatar" class="block w-full text-sm border border-gray-300 rounded p-2">
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