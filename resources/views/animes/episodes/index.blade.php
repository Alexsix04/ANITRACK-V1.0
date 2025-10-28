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

        <!-- Contenido principal -->
        <div class="relative flex flex-col lg:flex-row p-6 lg:p-12 h-full items-start">
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

            <div class="text-white lg:flex-1 max-w-3xl flex flex-col h-96">
                <h2 class="text-4xl lg:text-5xl font-bold mb-2">{{ $anime['title']['romaji'] }}</h2>
                <h3 class="text-2xl mb-2 text-gray-300">{{ $anime['title']['english'] ?? '' }}</h3>
                <p class="mb-2 text-lg"><strong>Episodios:</strong> {{ $anime['episodes'] ?? 'N/A' }}</p>

                <div
                    class="mt-4 p-6 bg-gray-800 bg-opacity-70 rounded-lg text-gray-100 text-lg leading-relaxed flex-1 overflow-y-scroll scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800">
                    {!! $anime['description'] !!}
                </div>
            </div>
        </div>
    </header>

    <!-- Sección adicional ocupando todo el ancho -->
    <section class="w-full p-6 mt-8 flex gap-8">
        <!-- Columna izquierda: info del anime disponible -->
        <div class="w-64 flex-shrink-0 bg-gray-100 p-6 rounded-lg shadow-md space-y-4" style="align-self: flex-start;">
            <!-- Aquí eliminamos sticky y limitamos altura -->
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
                    {{ $anime['startDate']['year'] ?? 'N/A' }}-
                    {{ $anime['startDate']['month'] ?? 'N/A' }}-
                    {{ $anime['startDate']['day'] ?? 'N/A' }}
                </p>
            </div>

            <div>
                <h3 class="text-gray-800 font-semibold text-base">END DATE</h3>
                <p class="text-gray-700 text-sm">
                    {{ $anime['endDate']['year'] ?? 'N/A' }}-
                    {{ $anime['endDate']['month'] ?? 'N/A' }}-
                    {{ $anime['endDate']['day'] ?? 'N/A' }}
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

        <!-- Columna derecha: menú de secciones -->
        <div class="flex-1">
            <h2 class="text-2xl font-bold mb-4">Secciones</h2>

            <!-- Menú de botones tipo tabs -->
            <div class="flex space-x-2 mb-6 border-b border-gray-300 overflow-x-auto">
                @foreach (['General', 'Personajes', 'Staff', 'Episodios', 'Comentarios'] as $section)
                    @if ($section === 'General')
                        <!-- Botón funcional que lleva a la vista general del anime -->
                        <a href="{{ route('animes.show', ['id' => $anime['id']]) }}"
                            class="py-2 px-4 font-semibold border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors rounded-t">
                            {{ $section }}
                        </a>
                    @elseif ($section === 'Personajes')
                        <!-- Botón funcional que lleva a la vista de personajes -->
                        <a href="{{ route('animes.characters.index', ['anime' => $anime['id']]) }}"
                            class="py-2 px-4 font-semibold border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors rounded-t">
                            {{ $section }}
                        </a>
                    @elseif ($section === 'Staff')
                        <!-- Botón funcional que lleva a la vista de personajes -->
                        <a href="{{ route('animes.staff.index', ['anime' => $anime['id']]) }}"
                            class="py-2 px-4 font-semibold border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors rounded-t">
                            {{ $section }}
                        </a>
                    @elseif ($section === 'Episodios')
                        <!-- Botón funcional que lleva a la vista de personajes -->
                        <a href="{{ route('animes.episodes.index', ['anime' => $anime['id']]) }}"
                            class="py-2 px-4 font-semibold border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors rounded-t">
                            {{ $section }}
                        </a>
                    @else
                        <!-- Botones estáticos -->
                        <button
                            class="py-2 px-4 font-semibold border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors rounded-t">
                            {{ $section }}
                        </button>
                    @endif
                @endforeach
            </div>



            <!-- Contenido por defecto: Vista General -->
            <div class="mt-8">
                <h3 class="text-xl font-bold mb-3 border-b border-gray-300 pb-2">Episodios</h3>

                @if (!empty($anime['streamingEpisodes']))
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach (collect($anime['streamingEpisodes']) as $episode)
                            <a href="{{ $episode['url'] }}" target="_blank"
                                class="block bg-gray-100 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition">
                                <img src="{{ $episode['thumbnail'] }}" alt="{{ $episode['title'] }}"
                                    class="w-full object-cover h-40">
                                <div class="p-3">
                                    <h4 class="text-sm font-semibold text-gray-800 truncate">
                                        {{ $episode['title'] }}</h4>
                                    <p class="text-xs text-gray-500">{{ $episode['site'] }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Episodios no disponibles.</p>
                @endif
            </div>
        </div>

    </section>

</x-app-layout>
