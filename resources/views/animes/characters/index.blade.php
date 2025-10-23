<x-app-layout>
    <div class="p-6">
        <h1 class="text-3xl font-bold mb-6">Personajes de {{ $anime['title']['romaji'] }}</h1>

        @if($characters->isEmpty())
            <p class="text-gray-500">No se encontraron personajes para este anime.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($characters as $char)
                    <a href="{{ route('animes.personajes.show', ['anime' => $anime['id'], 'character' => $char['node']['id']]) }}"
                       class="flex items-center bg-gray-100 p-3 rounded-lg shadow-sm hover:shadow-md transition">
                        <img src="{{ $char['node']['image']['medium'] }}" 
                             alt="{{ $char['node']['name']['full'] }}"
                             class="w-16 h-20 object-cover rounded-md mr-3 flex-shrink-0">

                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-gray-800 truncate">{{ $char['node']['name']['full'] }}</h3>
                            <p class="text-xs text-gray-500">{{ ucfirst(strtolower($char['role'])) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
