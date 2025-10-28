<x-app-layout>
    <div class="relative w-full min-h-screen">

        <!-- Banner de fondo -->
        @if (!empty($anime['bannerImage']))
            <div class="absolute inset-0">
                <img src="{{ $anime['bannerImage'] }}" alt="{{ $anime['title']['romaji'] }}"
                    class="w-full h-full object-cover filter brightness-50">
            </div>
        @endif

        <!-- Contenedor de contenido -->
        <div class="relative max-w-6xl mx-auto p-6">

            <!-- Datos del personaje -->
            <div
                class="flex flex-col md:flex-row items-start md:items-center mb-8 bg-gray-900 bg-opacity-70 p-6 rounded-lg shadow-lg text-white">
                <div class="flex-shrink-0">
                    <img src="{{ $character['image']['large'] ?? $character['image']['medium'] }}"
                        alt="{{ $character['name']['full'] }}"
                        class="w-48 h-64 object-cover rounded-lg shadow-md mb-4 md:mb-0">
                </div>
                <div class="md:ml-6 flex-1 space-y-2">
                    <h1 class="text-4xl font-bold">{{ $character['name']['full'] }}</h1>

                    <!-- Extra attributes (Rol, Edad, Sexo, Altura...) -->
                    @foreach ($character['extra_attributes'] as $key => $value)
                        <p><strong>{{ $key }}:</strong> {!! $value !!}</p>
                    @endforeach

                    <!-- Seiyuu -->
                    @if (!empty($character['voiceActors']))
                        <p>
                            <strong>Seiyuu:</strong>
                            @foreach ($character['voiceActors'] as $actor)
                                {{ $actor['name']['full'] }} ({{ $actor['language'] }}),
                            @endforeach
                        </p>
                    @endif
                </div>
            </div>

            <!-- Descripción -->
            <div class="bg-gray-100 bg-opacity-80 p-6 rounded-lg shadow-md mb-8 max-h-96 overflow-y-auto">
                <h2 class="text-2xl font-bold mb-2 border-b border-gray-300 pb-2">Descripción</h2>
                <div class="prose max-w-none text-gray-800">
                    {!! $character['description'] ?? '<p>Descripción no disponible.</p>' !!}
                </div>
            </div>

            <!-- Apariciones en animes (incluye prequel/sequel) -->
            <div class="bg-gray-100 bg-opacity-80 p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4 border-b border-gray-300 pb-2">Apariciones en Animes</h2>

                @php
                    $allAppearances = collect($character['media'])->merge($relatedMedia)->unique('node.id');
                @endphp

                @if ($allAppearances->isNotEmpty())
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($allAppearances as $media)
                            @if (!empty($media['node']))
                                <a href="{{ route('animes.show', $media['node']['id']) }}"
                                    class="block bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                                    <img src="{{ $media['node']['coverImage']['medium'] ?? '' }}"
                                        alt="{{ $media['node']['title']['romaji'] ?? 'Sin título' }}"
                                        class="w-full h-40 object-cover">
                                    <div class="p-2">
                                        <h3 class="text-sm font-semibold text-gray-800 truncate">
                                            {{ $media['node']['title']['romaji'] ?? 'Sin título' }}
                                        </h3>
                                        <p class="text-xs text-gray-500">{{ $media['role'] ?? '' }}</p>
                                        @if (isset($media['relationType']))
                                            <p class="text-xs text-blue-600 font-semibold">
                                                {{ ucfirst(strtolower($media['relationType'])) }}</p>
                                        @endif
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No hay apariciones disponibles.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>