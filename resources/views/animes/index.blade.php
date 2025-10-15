<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buscar Animes
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow rounded-lg">

                <!-- Formulario de búsqueda y filtros -->
                <form method="GET" action="{{ route('animes.index') }}" 
                      class="mb-6 flex flex-nowrap gap-4 items-end overflow-x-auto">
                    <!-- Nombre -->
                    <div class="flex flex-col min-w-[180px]">
                        <label for="query" class="text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" id="query" name="query" value="{{ request('query') }}"
                            placeholder="Buscar anime..." class="border rounded p-3 w-full">
                    </div>

                    <!-- Género -->
                    <div class="flex flex-col min-w-[150px]">
                        <label for="genre" class="text-sm font-medium text-gray-700">Género</label>
                        <select id="genre" name="genre" class="border rounded p-3 w-full">
                            <option value="">Cualquiera</option>
                            @foreach($genres as $g)
                                <option value="{{ $g }}" {{ request('genre') == $g ? 'selected' : '' }}>
                                    {{ $g }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Temporada -->
                    <div class="flex flex-col min-w-[120px]">
                        <label for="season" class="text-sm font-medium text-gray-700">Temporada</label>
                        <select id="season" name="season" class="border rounded p-3 w-full">
                            <option value="">Cualquiera</option>
                            <option value="WINTER" {{ request('season') == 'WINTER' ? 'selected' : '' }}>Invierno</option>
                            <option value="SPRING" {{ request('season') == 'SPRING' ? 'selected' : '' }}>Primavera</option>
                            <option value="SUMMER" {{ request('season') == 'SUMMER' ? 'selected' : '' }}>Verano</option>
                            <option value="FALL" {{ request('season') == 'FALL' ? 'selected' : '' }}>Otoño</option>
                        </select>
                    </div>

                    <!-- Año -->
                    <div class="flex flex-col min-w-[130px]">
                        <label for="seasonYear" class="text-sm font-medium text-gray-700">Año</label>
                        <select id="seasonYear" name="seasonYear" class="border rounded p-3 w-full">
                            <option value="">Cualquiera</option>
                            @for($year = date('Y'); $year >= 1985; $year--)
                                <option value="{{ $year }}" {{ request('seasonYear') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Formato -->
                    <div class="flex flex-col min-w-[120px]">
                        <label for="format" class="text-sm font-medium text-gray-700">Formato</label>
                        <select id="format" name="format" class="border rounded p-3 w-full">
                            <option value="">Cualquiera</option>
                            @foreach(['TV','MOVIE','OVA','ONA','SPECIAL','MUSIC'] as $f)
                                <option value="{{ $f }}" {{ request('format') == $f ? 'selected' : '' }}>
                                    {{ $f }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estado -->
                    <div class="flex flex-col min-w-[120px]">
                        <label for="status" class="text-sm font-medium text-gray-700">Estado</label>
                        <select id="status" name="status" class="border rounded p-3 w-full">
                            <option value="">Cualquiera</option>
                            <option value="FINISHED" {{ request('status') == 'FINISHED' ? 'selected' : '' }}>Finalizado</option>
                            <option value="RELEASING" {{ request('status') == 'RELEASING' ? 'selected' : '' }}>En emisión</option>
                            <option value="NOT_YET_RELEASED" {{ request('status') == 'NOT_YET_RELEASED' ? 'selected' : '' }}>No estrenado</option>
                            <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>

                    <!-- Botón Buscar -->
                    <div class="flex flex-col min-w-[120px]">
                        <label class="text-sm font-medium text-gray-700">&nbsp;</label>
                        <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-3 rounded hover:bg-indigo-700 w-full">
                            Buscar
                        </button>
                    </div>
                </form>

                <!-- Resultados -->
                @if(isset($animes) && count($animes) > 0)
                    <div class="grid grid-cols-2 md:grid-cols-5 lg:grid-cols-6 gap-4">
                        @foreach($animes as $anime)
                            <div class="bg-gray-100 rounded-lg overflow-hidden shadow hover:shadow-lg transition">
                                <img src="{{ $anime['coverImage']['large'] }}" alt="{{ $anime['title']['romaji'] }}"
                                     class="w-full h-64 object-cover">
                                <div class="p-2">
                                    <h3 class="text-lg font-bold truncate">{{ $anime['title']['romaji'] }}</h3>
                                    <p class="text-sm text-gray-600">
                                        ⭐ {{ $anime['averageScore'] ?? 'N/A' }} | {{ $anime['format'] ?? '' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginación -->
                    @if(!empty($pageInfo))
                        <div class="flex justify-center mt-6 space-x-2">
                            @if(($pageInfo['currentPage'] ?? 1) > 1)
                                <a href="{{ url()->current() }}?page={{ $pageInfo['currentPage'] - 1 }}&{{ $queryStringForPagination }}"
                                   class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Anterior</a>
                            @endif

                            <span class="px-4 py-2 bg-gray-100 rounded">
                                Página {{ $pageInfo['currentPage'] }} de {{ $pageInfo['lastPage'] }}
                            </span>

                            @if($pageInfo['hasNextPage'] ?? false)
                                <a href="{{ url()->current() }}?page={{ $pageInfo['currentPage'] + 1 }}&{{ $queryStringForPagination }}"
                                   class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Siguiente</a>
                            @endif
                        </div>
                    @endif

                @elseif(isset($animes))
                    <p class="text-gray-500 mt-4">No se encontraron resultados.</p>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>