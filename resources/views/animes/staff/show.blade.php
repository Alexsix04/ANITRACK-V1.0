<x-app-layout>
    <div class="max-w-6xl mx-auto p-6">
        <!-- Header del staff -->
        <div class="flex flex-col md:flex-row items-start md:items-center mb-8">
            <div class="flex-shrink-0">
                <img src="{{ $staff['image']['large'] ?? $staff['image']['medium'] }}"
                     alt="{{ $staff['name']['full'] ?? $staff['name'] }}"
                     class="w-48 h-64 object-cover rounded-lg shadow-md mb-4 md:mb-0">
            </div>
            <div class="md:ml-6 flex-1">
                <h1 class="text-4xl font-bold mb-2">{{ $staff['name']['full'] ?? $staff['name'] }}</h1>
                <p class="text-gray-700 mb-2"><strong>Rol en el anime:</strong> {{ $staff['role'] ?? 'N/A' }}</p>
                <p class="text-gray-700 mb-2"><strong>Favourites:</strong> {{ $staff['favourites'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Descripción del staff -->
        <div class="bg-gray-100 p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-bold mb-2 border-b border-gray-300 pb-2">Descripción</h2>
            <div class="prose max-w-none">
                {!! $staff['description'] ?? '<p>Descripción no disponible.</p>' !!}
            </div>
        </div>

        <!-- Animes asociados -->
        <div class="bg-gray-100 p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4 border-b border-gray-300 pb-2">Animes asociados</h2>
            @if(!empty($staff['media']))
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($staff['media'] as $media)
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
                <p class="text-gray-500">No hay animes asociados disponibles.</p>
            @endif
        </div>
    </div>
</x-app-layout>