<x-app-layout>
    <div class="max-w-6xl mx-auto p-6">
        <!-- Header del personaje -->
        <div class="flex flex-col md:flex-row items-start md:items-center mb-8">
            <div class="flex-shrink-0">
                <img src="{{ $character['image']['large'] ?? $character['image']['medium'] }}"
                     alt="{{ $character['name']['full'] }}"
                     class="w-48 h-64 object-cover rounded-lg shadow-md mb-4 md:mb-0">
            </div>
            <div class="md:ml-6 flex-1">
                <h1 class="text-4xl font-bold mb-2">{{ $character['name']['full'] }}</h1>
                <p class="text-gray-700 mb-2"><strong>Rol en el anime:</strong> {{ $character['role'] ?? 'N/A' }}</p>
                <p class="text-gray-700 mb-2"><strong>Edad:</strong> {{ $character['age'] ?? 'N/A' }}</p>
                <p class="text-gray-700 mb-2"><strong>Sexo:</strong> {{ $character['gender'] ?? 'N/A' }}</p>
                <p class="text-gray-700 mb-2"><strong>Seiyuu:</strong>
                    @if(!empty($character['voiceActors']))
                        @foreach($character['voiceActors'] as $actor)
                            {{ $actor['name']['full'] }} ({{ $actor['language'] }}),
                        @endforeach
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>

        <!-- Descripción del personaje -->
        <div class="bg-gray-100 p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-bold mb-2 border-b border-gray-300 pb-2">Descripción</h2>
            <div class="prose max-w-none">
                {!! $character['description'] ?? '<p>Descripción no disponible.</p>' !!}
            </div>
        </div>

        <!-- Apariciones en animes -->
        <div class="bg-gray-100 p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4 border-b border-gray-300 pb-2">Apariciones en Animes</h2>
            @if(!empty($character['media']['edges']))
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($character['media']['edges'] as $media)
                        <a href="{{ route('animes.show', $media['node']['id']) }}"
                           class="block bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                            <img src="{{ $media['node']['coverImage']['medium'] }}"
                                 alt="{{ $media['node']['title']['romaji'] }}"
                                 class="w-full h-40 object-cover">
                            <div class="p-2">
                                <h3 class="text-sm font-semibold text-gray-800 truncate">
                                    {{ $media['node']['title']['romaji'] ?? 'Sin título' }}
                                </h3>
                                <p class="text-xs text-gray-500">
                                    {{ $media['role'] ?? '' }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No hay apariciones disponibles.</p>
            @endif
        </div>
    </div>
</x-app-layout>