@props(['anime'])

<div class="w-full md:w-64 flex-shrink-0 bg-gray-100 p-4 md:p-6 rounded-lg shadow-md space-y-4">

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