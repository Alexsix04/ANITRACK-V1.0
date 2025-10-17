<x-app-layout>

    <!-- Header con altura fija -->
    <header class="relative w-full h-128 overflow-hidden">
        <!-- Fondo -->
        <div class="absolute inset-0">
            @if (!empty($anime['bannerImage']))
                <img src="{{ $anime['bannerImage'] }}" alt="{{ $anime['title']['romaji'] }}"
                    class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-white"></div>
            @endif
            <div class="absolute inset-0 bg-black opacity-20"></div>
        </div>

        <!-- Contenido principal: imagen y detalles -->
        <div class="relative flex flex-col lg:flex-row p-6 lg:p-12 h-full items-start">
            <!-- Portada y botones -->
            <div class="flex-shrink-0 mb-4 lg:mb-0 lg:mr-8 flex flex-col items-start">
                <img src="{{ $anime['coverImage']['large'] }}" alt="{{ $anime['title']['romaji'] }}"
                    class="w-64 h-96 object-cover rounded-lg shadow-lg mb-4">

                <div class="flex space-x-4">
                    <button
                        class="flex items-center justify-center w-12 h-12 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow-md transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.782 1.4 8.172L12 18.896l-7.334 3.868 1.4-8.172-5.934-5.782 8.2-1.192z" />
                        </svg>
                    </button>

                    <button class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-md transition">
                        Añadir a mi lista
                    </button>
                </div>
            </div>

            <!-- Información con scroll en la descripción al hover -->
            <div class="text-white lg:flex-1 max-w-3xl flex flex-col h-96">
                <h2 class="text-4xl lg:text-5xl font-bold mb-2">{{ $anime['title']['romaji'] }}</h2>
                <h3 class="text-2xl mb-2 text-gray-300">{{ $anime['title']['english'] ?? '' }}</h3>
                <p class="mb-2 text-lg"><strong>Episodios:</strong> {{ $anime['episodes'] ?? 'N/A' }}</p>

                <!-- Contenedor de descripción alineado con la portada -->
                <div
                    class="mt-4 p-6 bg-gray-800 bg-opacity-70 rounded-lg text-gray-100 text-lg leading-relaxed
                        flex-1 overflow-y-scroll scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800">
                    {!! $anime['description'] !!}
                </div>
            </div>
        </div>
    </header>

    <!-- Sección adicional ocupando todo el ancho -->
    <section class="w-full p-6 mt-8 flex gap-8">
        <!-- Columna izquierda: info del anime disponible -->
        <div class="w-64 flex-shrink-0 bg-gray-100 p-6 rounded-lg shadow-md sticky top-28 space-y-4">
            <h2 class="text-xl font-bold mb-4">Detalles del Anime</h2>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">FORMAT</h3>
                <p class="text-gray-700 text-sm">{{ $anime['format'] ?? 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">EPISODES</h3>
                <p class="text-gray-700 text-sm">{{ $anime['episodes'] ?? 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">DURATION</h3>
                <p class="text-gray-700 text-sm">{{ $anime['duration'] ? $anime['duration'] . ' mins' : 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">STATUS</h3>
                <p class="text-gray-700 text-sm">{{ $anime['status'] ?? 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">SEASON</h3>
                <p class="text-gray-700 text-sm">{{ $anime['season'] ?? 'N/A' }} {{ $anime['seasonYear'] ?? '' }}</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">START DATE</h3>
                <p class="text-gray-700 text-sm">
                    {{ $anime['startDate']['year'] ?? 'N/A' }}-{{ $anime['startDate']['month'] ?? 'N/A' }}-{{ $anime['startDate']['day'] ?? 'N/A' }}
                </p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">END DATE</h3>
                <p class="text-gray-700 text-sm">
                    {{ $anime['endDate']['year'] ?? 'N/A' }}-{{ $anime['endDate']['month'] ?? 'N/A' }}-{{ $anime['endDate']['day'] ?? 'N/A' }}
                </p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">AVERAGE SCORE</h3>
                <p class="text-gray-700 text-sm">{{ $anime['averageScore'] ?? 'N/A' }}%</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">POPULARITY</h3>
                <p class="text-gray-700 text-sm">{{ $anime['popularity'] ?? 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">GENRES</h3>
                @if (!empty($anime['genres']))
                    <div class="text-gray-700 text-sm space-y-1">
                        @foreach ($anime['genres'] as $genre)
                            <p>{{ $genre }}</p>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-700 text-sm">N/A</p>
                @endif
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">SOURCE</h3>
                <p class="text-gray-700 text-sm">{{ $anime['source'] ?? 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">STUDIOS</h3>
                @if (!empty($anime['studios']['edges']))
                    <div class="text-gray-700 text-sm space-y-1">
                        @foreach ($anime['studios']['edges'] as $studio)
                            <p>{{ $studio['node']['name'] }}</p>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-700 text-sm">N/A</p>
                @endif
            </div>
        </div>

        <!-- Columna derecha: menú y contenido -->
        <div x-data="{ activeTab: '{{ $seccion }}' }" class="flex-1">

            <h2 class="text-2xl font-bold mb-4">Opciones</h2>

            <div class="flex space-x-4 mb-6 border-b border-gray-300 overflow-x-auto">
                @foreach (['opcion1', 'opcion2', 'opcion3', 'opcion4', 'opcion5'] as $op)
                    <button
                        @click="
                    activeTab = '{{ $op }}';
                    const base = '/animes/{{ $anime['id'] }}';
                    history.pushState({}, '', '{{ $op === 'opcion1' ? '' : '/' . $op }}' ? base + '{{ $op === 'opcion1' ? '' : '/' . $op }}' : base);
                "
                        :class="activeTab === '{{ $op }}' ? 'text-blue-500 border-blue-500' : 'text-gray-700'"
                        class="py-2 px-4 font-semibold border-b-2 border-transparent hover:text-blue-500 hover:border-blue-500 transition">
                        {{ ucfirst($op) }}
                    </button>
                @endforeach
            </div>

            <div class="text-gray-700">
                <template x-if="activeTab === 'opcion1'">
                    <p>Contenido de la opción 1.</p>
                </template>

                <template x-if="activeTab === 'opcion2'">
                    <p>Contenido de la opción 2.</p>
                </template>

                <template x-if="activeTab === 'opcion3'">
                    <p>Contenido de la opción 3.</p>
                </template>
            </div>
        </div>


    </section>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</x-app-layout>
