<x-app-layout>
    {{-- Carrusel centrado, mismo ancho que secciones, altura grande --}}
    @if (!empty($currentAnimes))
        <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                class="relative overflow-hidden rounded-2xl shadow-lg h-[300px] md:h-[400px]">

                <!-- Slides -->
                <template x-for="(anime, index) in {{ json_encode($currentAnimes) }}" :key="index">
                    <div x-show="active === index" x-transition:enter="transform transition ease-out duration-700"
                        x-transition:enter-start="translate-x-full opacity-0"
                        x-transition:enter-end="translate-x-0 opacity-100"
                        x-transition:leave="transform transition ease-in duration-700 absolute inset-0"
                        x-transition:leave-start="translate-x-0 opacity-100"
                        x-transition:leave-end="-translate-x-full opacity-0" class="absolute inset-0 w-full h-full">
                        <a :href="'/animes/' + anime.id" class="block w-full h-full">
                            <img :src="'https://res.cloudinary.com/dqhtqecue/image/fetch/f_auto,q_auto:best,c_fit,w_1920,h_560/' +
                            encodeURIComponent(anime.coverImage.extraLarge ?? anime.coverImage.large)"
                                :alt="anime.title.romaji"
                                class="w-full h-full object-cover object-[center_37%] transition-all duration-500"
                                loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent">
                            </div>

                            <div class="absolute bottom-4 left-4 text-white drop-shadow-md">
                                <h2 class="text-xl md:text-2xl font-bold mb-1" x-text="anime.title.romaji"></h2>
                                <p class="text-sm md:text-base">⭐ <span x-text="anime.averageScore ?? 'N/A'"></span></p>
                            </div>

                    </div>
                </template>

                <!-- Controles -->
                <button @click="prev()"
                    class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white text-gray-800 p-1 md:p-2 rounded-full shadow transition">◀</button>
                <button @click="next()"
                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white text-gray-800 p-1 md:p-2 rounded-full shadow transition">▶</button>

                <!-- Indicadores -->
                <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1 md:gap-2">
                    <template x-for="i in total" :key="i">
                        <div @click="previous = active; active = i - 1"
                            class="w-2 h-2 md:w-3 md:h-3 rounded-full cursor-pointer transition"
                            :class="active === i - 1 ? 'bg-white' : 'bg-white/50'"></div>
                    </template>
                </div>
            </div>
        </div>
    @endif

    {{-- Secciones --}}
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
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
