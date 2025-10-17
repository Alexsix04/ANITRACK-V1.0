<x-app-layout>
    <x-slot name="header">
        <h2 id="anime-page-title" class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ $title ?? 'Buscar Animes' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-[95rem] mx-auto sm:px-4 lg:px-6">
            <div class="bg-white p-6 shadow-lg rounded-xl">

                <!-- FORMULARIO -->
                <form id="anime-search-form" method="GET" action="{{ route('animes.index') }}"
                      class="mb-6 flex flex-wrap gap-5 items-end">
                    @if ($filter)
                        <input type="hidden" name="filter" value="{{ $filter }}">
                    @endif

                    <!-- Nombre -->
                    <div class="flex flex-col flex-1 min-w-[200px]">
                        <label for="query" class="text-base font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" id="query" name="query" value="{{ request('query') }}"
                               placeholder="Buscar anime..."
                               class="border rounded-lg p-3 w-full text-base focus:ring-2 focus:ring-blue-400">
                    </div>

                    <!-- Género -->
                    <div class="flex flex-col flex-1 min-w-[160px]">
                        <label for="genre" class="text-base font-medium text-gray-700 mb-1">Género</label>
                        <select id="genre" name="genre"
                                class="border rounded-lg p-3 w-full text-base focus:ring-2 focus:ring-blue-400">
                            <option value="">Cualquiera</option>
                            @foreach ($genres as $g)
                                <option value="{{ $g }}" {{ request('genre') == $g ? 'selected' : '' }}>
                                    {{ $g }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Temporada -->
                    <div class="flex flex-col flex-1 min-w-[140px]">
                        <label for="season" class="text-base font-medium text-gray-700 mb-1">Temporada</label>
                        <select id="season" name="season"
                                class="border rounded-lg p-3 w-full text-base focus:ring-2 focus:ring-blue-400">
                            <option value="">Cualquiera</option>
                            <option value="WINTER" {{ request('season') == 'WINTER' ? 'selected' : '' }}>Invierno</option>
                            <option value="SPRING" {{ request('season') == 'SPRING' ? 'selected' : '' }}>Primavera</option>
                            <option value="SUMMER" {{ request('season') == 'SUMMER' ? 'selected' : '' }}>Verano</option>
                            <option value="FALL" {{ request('season') == 'FALL' ? 'selected' : '' }}>Otoño</option>
                        </select>
                    </div>

                    <!-- Año -->
                    <div class="flex flex-col flex-1 min-w-[130px]">
                        <label for="seasonYear" class="text-base font-medium text-gray-700 mb-1">Año</label>
                        <select id="seasonYear" name="seasonYear"
                                class="border rounded-lg p-3 w-full text-base focus:ring-2 focus:ring-blue-400">
                            <option value="">Cualquiera</option>
                            @for ($year = date('Y') + 1; $year >= 1985; $year--)
                                <option value="{{ $year }}" {{ request('seasonYear') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Formato -->
                    <div class="flex flex-col flex-1 min-w-[140px]">
                        <label for="format" class="text-base font-medium text-gray-700 mb-1">Formato</label>
                        <select id="format" name="format"
                                class="border rounded-lg p-3 w-full text-base focus:ring-2 focus:ring-blue-400">
                            <option value="">Cualquiera</option>
                            @foreach (['TV', 'MOVIE', 'OVA', 'ONA', 'SPECIAL', 'MUSIC'] as $f)
                                <option value="{{ $f }}" {{ request('format') == $f ? 'selected' : '' }}>
                                    {{ $f }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estado -->
                    <div class="flex flex-col flex-1 min-w-[140px]">
                        <label for="status" class="text-base font-medium text-gray-700 mb-1">Estado</label>
                        <select id="status" name="status"
                                class="border rounded-lg p-3 w-full text-base focus:ring-2 focus:ring-blue-400">
                            <option value="">Cualquiera</option>
                            <option value="FINISHED" {{ request('status') == 'FINISHED' ? 'selected' : '' }}>Finalizado</option>
                            <option value="RELEASING" {{ request('status') == 'RELEASING' ? 'selected' : '' }}>En emisión</option>
                            <option value="NOT_YET_RELEASED" {{ request('status') == 'NOT_YET_RELEASED' ? 'selected' : '' }}>No estrenado</option>
                            <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                </form>

                <!-- GRID DE RESULTADOS -->
                <div id="anime-grid"
                     class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-7">
                    @foreach ($animes as $anime)
                        <a href="{{ route('animes.show', $anime['id']) }}">
                            <div class="bg-gray-100 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition">
                                <img src="{{ $anime['coverImage']['large'] }}" alt="{{ $anime['title']['romaji'] }}"
                                     class="w-full h-64 object-cover">
                                <div class="p-2.5">
                                    <h3 class="text-base font-semibold truncate">{{ $anime['title']['romaji'] }}</h3>
                                    <p class="text-sm text-gray-600">
                                        ⭐ {{ $anime['averageScore'] ?? 'N/A' }} | {{ $anime['format'] ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    <!-- LOADER -->
    <div id="loading" class="hidden text-center py-6">
        <div class="flex justify-center items-center space-x-2">
            <div class="w-5 h-5 border-2 border-t-transparent border-indigo-600 rounded-full animate-spin"></div>
            <span class="text-gray-600">Cargando animes...</span>
        </div>
    </div>

    <!-- SIN RESULTADOS -->
    <p id="no-results" class="hidden text-gray-500 mt-4">No se encontraron resultados.</p>

    <!-- JS -->
    <script src="{{ asset('/js/search.js') }}"></script>
</x-app-layout>