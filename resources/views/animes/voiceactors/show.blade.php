<x-app-layout>
    <div class="relative w-full min-h-screen p-6 max-w-7xl mx-auto space-y-10">

        <!-- Actor de voz -->
        <div
            class="flex flex-col md:flex-row items-center md:items-start bg-gradient-to-r from-gray-800 via-gray-900 to-gray-800 p-6 rounded-2xl shadow-xl text-white gap-6">
            <div class="flex-shrink-0 w-full md:w-56">
                <img src="{{ $actor['image']['large'] ?? $actor['image']['medium'] }}"
                    alt="{{ $actor['name']['full'] ?? 'Sin nombre' }}"
                    class="w-full h-72 object-cover rounded-2xl shadow-lg">
            </div>
            <div class="flex-1 flex flex-col justify-center space-y-4">
                <h1 class="text-4xl sm:text-5xl font-extrabold">{{ $actor['name']['full'] ?? 'Sin nombre' }}</h1>
                <p class="text-yellow-400 font-medium text-lg">⭐ Favoritos: {{ $actor['favourites'] }}</p>

                <!-- Descripción -->
                <div class="prose prose-invert text-gray-200 max-w-full mt-2">
                    {!! $actor['description'] !!}
                </div>

                <!-- Enlaces oficiales -->
                @if ($actor['links']->isNotEmpty())
                    <div class="mt-4 bg-gray-700 bg-opacity-50 p-4 rounded-xl">
                        <h3 class="text-xl font-semibold flex items-center mb-3">
                            🌐 <span class="ml-2">Enlaces Oficiales</span>
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($actor['links'] as $link)
                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer"
                                    class="text-blue-400 hover:text-blue-300 font-medium text-sm underline transition-colors">
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Animes -->
        <div class="bg-gray-100 bg-opacity-90 p-6 rounded-2xl shadow-md">
            <h2 class="text-3xl font-bold mb-6 border-b border-gray-300 pb-3">🎬 Animes en los que participó</h2>

            @if ($animeList->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
    @foreach($animeList as $anime)
        <a href="{{ route('animes.show', $anime['id']) }}"
           class="block group bg-white rounded-xl shadow-md hover:shadow-xl transition transform hover:-translate-y-0.5 duration-200 overflow-hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-3 p-3">
                <img src="{{ $anime['coverImage'] ?? '' }}"
                     alt="{{ $anime['title'] ?? 'Sin título' }}"
                     class="w-20 h-32 object-cover rounded-lg flex-shrink-0">
                <div class="flex-1 min-w-0 w-full">
                    <h4 class="text-sm font-semibold text-gray-800 truncate whitespace-nowrap overflow-hidden text-left">
                        {{ $anime['title'] }}
                    </h4>
                </div>
            </div>
        </a>
    @endforeach
</div>
            @else
                <p class="text-gray-500 text-center">No se encontraron animes.</p>
            @endif
        </div>

        <!-- Personajes -->
        <div class="bg-gray-100 bg-opacity-90 p-6 rounded-2xl shadow-md">
            <h2 class="text-3xl font-bold mb-6 border-b border-gray-300 pb-3">🎭 Personajes doblados</h2>

            @if ($characters->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach ($characters as $char)
                        <a href="{{ route('animes.characters.show', ['anime' => $char['anime']['id'], 'character' => $char['id']]) }}"
                            class="block group">
                            <div
                                class="flex flex-col bg-white rounded-xl shadow-md hover:shadow-lg transition-transform duration-300 hover:-translate-y-1 overflow-hidden">
                                <div class="w-full h-64 relative">
                                    <img src="{{ $char['image'] ?? '' }}" alt="{{ $char['name'] ?? 'Sin nombre' }}"
                                        class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                                </div>
                                <div class="p-4 flex flex-col gap-1">
                                    <p class="font-semibold text-gray-800 truncate">{{ $char['name'] }}</p>
                                    <p class="text-gray-500 text-sm truncate">Anime: {{ $char['anime']['title'] }}</p>
                                    <p class="text-gray-500 text-sm">Favoritos: {{ $char['favourites'] }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center">No se encontraron personajes.</p>
            @endif
        </div>

    </div>
</x-app-layout>
