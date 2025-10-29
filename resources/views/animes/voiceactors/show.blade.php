<x-app-layout>
    <div class="relative w-full min-h-screen p-6 max-w-6xl mx-auto">

        <!-- Actor de voz -->
        <div class="flex flex-col md:flex-row items-start md:items-center mb-10 bg-gray-900 bg-opacity-70 p-6 rounded-xl shadow-lg text-white">
            <div class="flex-shrink-0">
                <img src="{{ $actor['image']['large'] ?? $actor['image']['medium'] }}"
                     alt="{{ $actor['name']['full'] ?? 'Sin nombre' }}"
                     class="w-56 h-72 object-cover rounded-xl shadow-md mb-4 md:mb-0">
            </div>

            <div class="md:ml-8 flex-1 space-y-3 mt-2 md:mt-0">
                <h1 class="text-5xl font-extrabold">{{ $actor['name']['full'] ?? 'Sin nombre' }}</h1>
                <p class="text-gray-300 text-lg">⭐ Favoritos: {{ $actor['favourites'] }}</p>

                <!-- Descripción -->
                <div class="mt-3 text-gray-100 prose max-w-none">
                    {!! $actor['description'] !!}
                </div>

                <!-- 🌐 Enlaces Oficiales -->
                @if($actor['links']->isNotEmpty())
                    <div class="mt-5 bg-gray-800 bg-opacity-60 p-4 rounded-lg">
                        <h3 class="text-xl font-semibold text-white mb-3 flex items-center">
                            🌐 <span class="ml-2">Enlaces Oficiales</span>
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach($actor['links'] as $link)
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

        <!-- Animes en los que participó -->
        <div class="bg-gray-100 bg-opacity-80 p-6 rounded-xl shadow-md mb-10">
            <h2 class="text-3xl font-bold mb-5 border-b border-gray-300 pb-3">🎬 Animes en los que participó</h2>

            @if($animeList->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
                    @foreach($animeList as $anime)
                        <a href="{{ route('animes.show', $anime['id']) }}"
                           class="block bg-white rounded-xl overflow-hidden shadow hover:shadow-lg transition transform hover:-translate-y-1">
                            <div class="flex items-center space-x-4 p-4">
                                <img src="{{ $anime['coverImage'] ?? '' }}"
                                     alt="{{ $anime['title'] ?? 'Sin título' }}"
                                     class="w-24 h-36 object-cover rounded-lg flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base font-semibold text-gray-800 truncate">
                                        {{ $anime['title'] }}
                                    </h4>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No se encontraron animes.</p>
            @endif
        </div>

        <!-- Personajes doblados -->
        <div class="bg-gray-100 bg-opacity-80 p-6 rounded-xl shadow-md">
            <h2 class="text-3xl font-bold mb-5 border-b border-gray-300 pb-3">🎭 Personajes doblados</h2>

            @if($characters->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-5">
                    @foreach($characters as $char)
                        <a href="{{ route('animes.characters.show', ['anime' => $char['anime']['id'], 'character' => $char['id']]) }}"
                           class="block">
                            <div class="flex items-center bg-white rounded-xl shadow hover:shadow-lg transition-transform duration-300 hover:-translate-y-1 p-3.5">
                                <img src="{{ $char['image'] ?? '' }}"
                                     alt="{{ $char['name'] ?? 'Sin nombre' }}"
                                     class="w-24 h-24 object-contain rounded-lg flex-shrink-0">
                                <div class="ml-4 flex flex-col justify-center">
                                    <p class="text-base font-semibold">{{ $char['name'] }}</p>
                                    <p class="text-sm text-gray-500 truncate">Anime: {{ $char['anime']['title'] }}</p>
                                    <p class="text-sm text-gray-500">Favoritos: {{ $char['favourites'] }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No se encontraron personajes.</p>
            @endif
        </div>

    </div>
</x-app-layout>
