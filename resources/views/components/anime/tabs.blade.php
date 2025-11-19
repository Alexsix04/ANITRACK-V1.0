@props(['animeId'])

@php
    $tabs = [
        'General' => route('animes.show', ['id' => $animeId]),
        'Personajes' => route('animes.characters.index', ['anime' => $animeId]),
        'Staff' => route('animes.staff.index', ['anime' => $animeId]),
        'Episodios' => route('animes.episodes.index', ['anime' => $animeId]),
    ];

    $currentUrl = url()->current();
@endphp

<div class="flex space-x-2 mb-6 border-b border-gray-300 overflow-x-auto pb-2">
    @foreach ($tabs as $name => $url)

        @if ($url)
            {{-- Link funcional --}}
            <a href="{{ $url }}"
                class="py-2 px-4 font-semibold border-b-2 rounded-t transition-colors
                       {{ $currentUrl === $url ? 'border-blue-600 text-blue-600' : 'border-transparent hover:text-blue-600 hover:border-blue-600' }}">
                {{ $name }}
            </a>
        @else
            {{-- Botón sin acción --}}
            <button class="py-2 px-4 font-semibold border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 rounded-t">
                {{ $name }}
            </button>
        @endif

    @endforeach
</div>