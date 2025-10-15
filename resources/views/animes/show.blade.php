<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $anime['title']['romaji'] }}
        </h2>
    </x-slot>

    <div class="relative w-full h-128 overflow-hidden rounded-lg shadow-lg">
        <!-- Fondo del anime: banner image -->
        @if(!empty($anime['bannerImage']))
            <div class="absolute inset-0">
                <img src="{{ $anime['bannerImage'] }}" alt="{{ $anime['title']['romaji'] }}" class="w-full h-full object-cover">
                <!-- Overlay menos oscuro -->
                <div class="absolute inset-0 bg-black opacity-20"></div>
            </div>
        @endif

        <!-- Contenido principal -->
        <div class="relative flex flex-col lg:flex-row p-6 lg:p-12 h-full items-start">
            <!-- Portada y botones -->
            <div class="flex-shrink-0 mb-4 lg:mb-0 lg:mr-8 flex flex-col items-start">
                <img src="{{ $anime['coverImage']['large'] }}" alt="{{ $anime['title']['romaji'] }}" class="w-64 h-96 object-cover rounded-lg shadow-lg mb-4">

                <!-- Botones alineados con la portada -->
                <div class="flex space-x-4">
                    <!-- Botón favoritos (solo estrella) -->
                    <button class="flex items-center justify-center w-12 h-12 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow-md transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.782 1.4 8.172L12 18.896l-7.334 3.868 1.4-8.172-5.934-5.782 8.2-1.192z"/>
                        </svg>
                    </button>

                    <!-- Botón añadir a lista -->
                    <button class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-md transition">
                        Añadir a mi lista
                    </button>
                </div>
            </div>

            <!-- Información -->
            <div class="text-white lg:flex-1 max-w-3xl">
                <h3 class="text-4xl lg:text-5xl font-bold mb-4">{{ $anime['title']['english'] ?? $anime['title']['romaji'] }}</h3>
                <p class="mb-2 text-lg"><strong>Episodios:</strong> {{ $anime['episodes'] ?? 'N/A' }}</p>
                <div class="mt-4 text-gray-100 text-lg leading-relaxed">
                    {!! $anime['description'] !!}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>