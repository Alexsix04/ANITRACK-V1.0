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

                <!-- Roles en el anime específico -->
                <p class="text-gray-700 mb-1"><strong>Roles en "{{ $anime['title']['romaji'] ?? 'Anime' }}":</strong></p>
                @if (!empty($staff['roles_in_anime']))
                    <ul class="list-disc list-inside text-gray-700">
                        @foreach ($staff['roles_in_anime'] as $role)
                            <li>{{ $role }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500">N/A</p>
                @endif
            </div>
        </div>

        <!-- Otros animes asociados -->
        <div class="bg-gray-100 p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4 border-b border-gray-300 pb-2">Otros animes en los que trabajó</h2>
            @if (!empty($staff['other_animes']))
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach ($staff['other_animes'] as $media)
                        <a href="{{ route('animes.show', $media['id']) }}"
                            class="block bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition transform hover:-translate-y-1">
                            <img src="{{ $media['coverImage'] }}" alt="{{ $media['title'] }}"
                                class="w-full h-48 object-cover rounded-t-lg">
                            <div class="p-2">
                                <h3 class="text-sm font-semibold text-gray-800 truncate text-center">
                                    {{ $media['title'] }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No hay otros animes asociados disponibles.</p>
            @endif
        </div>


    </div>
</x-app-layout>
