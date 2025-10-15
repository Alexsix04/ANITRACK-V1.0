<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Inicio
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Buscador reutilizado -->
        <form method="GET" action="{{ route('animes.index') }}" class="mb-6 flex flex-nowrap gap-4 items-end overflow-x-auto">
            <!-- Puedes copiar aquí el mismo formulario del index -->
        </form>

        <!-- Secciones -->
        @foreach($sections as $title => $animes)
            <div class="mb-10">
                <h3 class="text-2xl font-bold mb-4">{{ $title }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-5 lg:grid-cols-6 gap-4">
                    @foreach($animes as $anime)
                        <a href="{{ route('animes.show', $anime['id']) }}" class="bg-gray-100 rounded-lg overflow-hidden shadow hover:shadow-lg transition">
                            <img src="{{ $anime['coverImage']['large'] }}" alt="{{ $anime['title']['romaji'] }}" class="w-full h-64 object-cover">
                            <div class="p-2">
                                <h4 class="text-lg font-semibold truncate">{{ $anime['title']['romaji'] }}</h4>
                                <p class="text-sm text-gray-600">⭐ {{ $anime['averageScore'] ?? 'N/A' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>