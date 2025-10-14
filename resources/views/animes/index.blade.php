<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buscar Animes
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Buscador -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ url('/animes') }}">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        Busca un anime
                    </label>
                    <div class="flex gap-2">
                        <input type="text" name="search" id="search" placeholder="Nombre del anime..." class="border border-gray-300 rounded-md px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Buscar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Resultados (por ahora vacíos) -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Resultados:</h3>
                <p class="text-gray-500">Aquí aparecerán los resultados de la búsqueda.</p>
            </div>

        </div>
    </div>
</x-app-layout>