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

                <!-- Imagen del personaje -->
                <div class="flex-shrink-0">
                    <img src="{{ $character['image']['large'] ?? $character['image']['medium'] }}"
                        alt="{{ $character['name']['full'] }}"
                        class="w-48 h-64 object-cover rounded-lg shadow-md mb-4 md:mb-2">

                    <!-- Botones debajo de la imagen -->
                    <div class="flex space-x-2 justify-center mt-2">
                        <!-- Botón Favorito cuadrado -->
                        <button
                            class="flex items-center justify-center w-10 h-10 bg-yellow-400 text-white rounded-sm hover:bg-yellow-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.888 1.444 8.278L12 18.896l-7.38 3.976 1.444-8.278-6.064-5.888 8.332-1.151z" />
                            </svg>
                        </button>

                        <!-- Botón Añadir a Lista -->
                        <button class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            Añadir a Lista
                        </button>
                    </div>

                </div>

                <!-- Información del personaje -->
                <div class="md:ml-6 flex-1 space-y-2 mt-2 md:mt-0">
                    <h1 class="text-4xl font-bold">{{ $character['name']['full'] }}</h1>

                    <!-- Extra attributes (Rol, Edad, Sexo, Altura...) -->
                    @foreach ($character['extra_attributes'] as $key => $value)
                        <p><strong>{{ $key }}:</strong> {!! $value !!}</p>
                    @endforeach
                </div>
            </div>

            <!-- Descripción -->
            <div class="bg-gray-100 bg-opacity-80 p-6 rounded-lg shadow-md mb-8 max-h-96 overflow-y-auto">
                <h2 class="text-2xl font-bold mb-2 border-b border-gray-300 pb-2">Descripción</h2>
                <div class="prose max-w-none text-gray-800">
                    {!! $character['description'] ?? '<p>Descripción no disponible.</p>' !!}
                </div>
            </div>

            <!-- Apariciones en animes (más compacto) -->
            <div class="bg-gray-100 bg-opacity-80 p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-2xl font-bold mb-4 border-b border-gray-300 pb-2">Apariciones en Animes</h2>

                @php
                    $allAppearances = collect($character['media'])->merge($relatedMedia)->unique('node.id');
                @endphp

                @if ($allAppearances->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($allAppearances as $media)
                            @if (!empty($media['node']))
                                <a href="{{ route('animes.show', $media['node']['id']) }}"
                                    class="block bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition transform hover:-translate-y-1">
                                    <div class="flex items-center space-x-3 p-3">
                                        <img src="{{ $media['node']['coverImage']['medium'] ?? '' }}"
                                            alt="{{ $media['node']['title']['romaji'] ?? 'Sin título' }}"
                                            class="w-20 h-28 object-cover rounded-md">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-base font-semibold text-gray-800 truncate">
                                                {{ $media['node']['title']['romaji'] ?? 'Sin título' }}
                                            </h4>
                                            <p class="text-sm text-gray-500">
                                                {{ ucfirst(strtolower($media['role'] ?? '')) }}
                                            </p>
                                            @if (isset($media['relationType']))
                                                <p class="text-sm text-blue-600 font-semibold">
                                                    {{ ucfirst(strtolower($media['relationType'])) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No hay apariciones disponibles.</p>
                @endif
            </div>

            <!-- Actores de voz -->
            @if (!empty($character['voiceActors']))
                <div class="bg-gray-100 bg-opacity-80 p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold mb-4 border-b border-gray-300 pb-2">Actores de Voz</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($character['voiceActors'] as $actor)
                            <a href="{{ route('animes.voiceactors.show', ['id' => $actor['id']]) }}"
                                class="bg-white rounded-lg overflow-hidden shadow-sm p-2 flex flex-col items-center text-center hover:shadow-md hover:-translate-y-1 transform transition">
                                <img src="{{ $actor['image']['medium'] ?? ($actor['image']['large'] ?? '') }}"
                                    alt="{{ $actor['name']['full'] }}"
                                    class="w-24 h-24 object-cover rounded-full mb-2">
                                <p class="text-sm font-semibold text-gray-800">{{ $actor['name']['full'] }}</p>
                                @if (!empty($actor['languageV2']))
                                    <p class="text-xs text-gray-500">{{ $actor['languageV2'] }}</p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>