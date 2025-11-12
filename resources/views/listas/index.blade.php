<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Listas públicas</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <a href="{{ route('listas.anime.public') }}"
               class="block w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg shadow">
                Ver listas públicas de Anime
            </a>

            <a href="{{ route('listas.characters.public') }}"
               class="block w-full text-center bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg shadow">
                Ver listas públicas de Personajes
            </a>
        </div>
    </div>
</x-app-layout>
