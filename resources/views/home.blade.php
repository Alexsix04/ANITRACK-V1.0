<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Inicio
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- 🎬 Carrusel tipo hero (un anime a la vez, slide lateral) -->
        @if (!empty($currentAnimes))
            <div x-data="{
                active: 0,
                total: {{ count($currentAnimes) }},
                previous: 0,
                autoplay() {
                    setInterval(() => {
                        this.next();
                    }, 6000);
                },
                next() {
                    this.previous = this.active;
                    this.active = (this.active + 1) % this.total;
                },
                prev() {
                    this.previous = this.active;
                    this.active = (this.active - 1 + this.total) % this.total;
                }
            }" x-init="autoplay()"
                class="relative mb-12 overflow-hidden rounded-2xl shadow-lg h-[460px] md:h-[560px]">

                <!-- Slides con animación lateral -->
                <template x-for="(anime, index) in {{ json_encode($currentAnimes) }}" :key="index">
                    <div x-show="active === index" x-transition:enter="transform transition ease-out duration-700"
                        x-transition:enter-start="translate-x-full opacity-0"
                        x-transition:enter-end="translate-x-0 opacity-100"
                        x-transition:leave="transform transition ease-in duration-700 absolute inset-0"
                        x-transition:leave-start="translate-x-0 opacity-100"
                        x-transition:leave-end="-translate-x-full opacity-0" class="absolute inset-0 w-full h-full">
                        <a :href="'/animes/' + anime.id" class="block w-full h-full">
                            <!-- Imagen de alta resolución con mejor enfoque -->
                            <img :src="anime.coverImage.extraLarge ?? anime.coverImage.large" :alt="anime.title.romaji"
                                class="w-full max-h-[560px] object-cover object-[50%_25%] mx-auto transition-all duration-500"
                                loading="lazy" />


                            <!-- Overlay degradado -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent">
                            </div>

                            <!-- Texto informativo -->
                            <div class="absolute bottom-10 left-8 text-white max-w-lg drop-shadow-lg">
                                <h2 class="text-3xl md:text-5xl font-bold mb-3" x-text="anime.title.romaji"></h2>
                                <p class="text-lg">⭐ <span x-text="anime.averageScore ?? 'N/A'"></span></p>
                                <p class="text-sm mt-2 opacity-80" x-text="anime.season + ' ' + anime.seasonYear"></p>
                                <a :href="'/animes/' + anime.id"
                                    class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                                    Ver detalles →
                                </a>
                            </div>
                        </a>
                    </div>
                </template>


                <!-- Controles -->
                <button @click="prev()"
                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 rounded-full shadow transition">◀</button>
                <button @click="next()"
                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 rounded-full shadow transition">▶</button>

                <!-- Indicadores -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                    <template x-for="i in total" :key="i">
                        <div @click="previous = active; active = i - 1"
                            class="w-3 h-3 rounded-full cursor-pointer transition"
                            :class="active === i - 1 ? 'bg-white' : 'bg-white/50'"></div>
                    </template>
                </div>
            </div>
        @endif


        <!-- 📚 Secciones normales -->
        @foreach ($sections as $title => $animes)
            <div class="mb-10">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-2xl font-bold">{{ $title }}</h3>
                    <a href="{{ route('animes.index', ['filter' => Str::slug($title)]) }}"
                        class="text-blue-600 hover:text-blue-800 font-medium text-sm transition">
                        Ver más →
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-5 lg:grid-cols-6 gap-4">
                    @foreach ($animes as $anime)
                        <a href="{{ route('animes.show', $anime['id']) }}"
                            class="bg-gray-100 rounded-lg overflow-hidden shadow hover:shadow-lg transition">
                            <img src="{{ $anime['coverImage']['large'] }}" alt="{{ $anime['title']['romaji'] }}"
                                class="w-full h-64 object-cover object-center">
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
