<x-app-layout>
    <div class="max-w-7xl mx-auto p-6">

        <!-- Header del staff -->
        <div class="flex flex-col lg:flex-row items-center lg:items-start mb-10 gap-6">
            <div class="flex-shrink-0">
                <img src="{{ $staff['image']['large'] ?? $staff['image']['medium'] }}"
                     alt="{{ $staff['name']['full'] ?? $staff['name'] }}"
                     class="w-56 h-72 object-cover rounded-xl shadow-lg transition-transform transform hover:scale-105">
            </div>
            <div class="flex-1 lg:ml-8 text-center lg:text-left">
                <h1 class="text-4xl lg:text-5xl font-extrabold mb-3 text-gray-900">
                    {{ $staff['name']['full'] ?? $staff['name'] }}
                </h1>

                <p class="text-lg text-gray-700 font-medium mb-2">
                    Roles en <span class="font-semibold">"{{ $anime['title']['romaji'] ?? 'Anime' }}"</span>:
                </p>

                @if (!empty($staff['roles_in_anime']))
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        @foreach ($staff['roles_in_anime'] as $role)
                            <li>{{ $role }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-400 italic">No hay roles registrados.</p>
                @endif
            </div>
        </div>

        <!-- Otros animes asociados -->
        <div class="bg-gray-50 p-6 rounded-2xl shadow-lg">
            <h2 class="text-2xl lg:text-3xl font-bold mb-6 border-b border-gray-300 pb-2 text-gray-800">
                Otros animes en los que trabajó
            </h2>

            @if (!empty($staff['other_animes']))
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
                    @foreach ($staff['other_animes'] as $media)
                        <a href="{{ route('animes.show', $media['id']) }}"
                           class="block bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition transform hover:-translate-y-1 hover:scale-105">
                            <img src="{{ $media['coverImage'] }}" alt="{{ $media['title'] }}"
                                 class="w-full h-52 object-cover rounded-t-xl">
                            <div class="p-3">
                                <h3 class="text-sm lg:text-base font-semibold text-gray-900 truncate text-center">
                                    {{ $media['title'] }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 italic">No hay otros animes asociados disponibles.</p>
            @endif
        </div>

    </div>
</x-app-layout>