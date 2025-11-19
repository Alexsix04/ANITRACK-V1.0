<nav x-data="{ open: false, searchOpen: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">Inicio</x-nav-link>
                    <x-nav-link :href="route('animes.index')" :active="request()->routeIs('animes.index')">Animes</x-nav-link>
                    <x-nav-link :href="route('listas.index')" :active="request()->routeIs('listas.index')">Listas</x-nav-link>
                </div>
            </div>

            <!-- Lupa + Login / Avatar -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 gap-4">
                <!-- Botón de búsqueda (lupa) -->
                <button @click="searchOpen = true" class="p-2 rounded hover:bg-gray-100 focus:outline-none focus:ring">
                    <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                    </svg>
                </button>

                @guest
                    <x-nav-link :href="route('login')">Iniciar Sesión</x-nav-link>
                    <x-nav-link :href="route('register')">Registrarse</x-nav-link>
                @else
                    <!-- Avatar del usuario -->
                    <div class="ml-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->avatar_url }}" alt="Avatar">
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.show', Auth::id())">Mi Perfil</x-dropdown-link>
                                <x-dropdown-link :href="route('profile.index')">Listas y Favoritos</x-dropdown-link>
                                <x-dropdown-link :href="route('profile.saves')">Guardados</x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        Cerrar Sesión
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endguest
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">Inicio</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('animes.index')" :active="request()->routeIs('animes.index')">Animes</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('listas.index')" :active="request()->routeIs('listas.index')">Listas</x-responsive-nav-link>
        </div>

        <!-- Responsive Menu -->
        <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <!-- Enlaces principales -->
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">Inicio</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('animes.index')" :active="request()->routeIs('animes.index')">Animes</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('listas.index')" :active="request()->routeIs('listas.index')">Listas</x-responsive-nav-link>

                <!-- Botón de búsqueda -->
                <button @click="searchOpen = true"
                    class="w-full text-left px-4 py-2 mt-2 border rounded hover:bg-gray-100 flex items-center gap-2">
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                    </svg>
                    Buscar...
                </button>
            </div>

            <div class="pt-4 pb-1 border-t border-gray-200">
                @guest
                    <x-responsive-nav-link :href="route('login')">Iniciar Sesión</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">Registrarse</x-responsive-nav-link>
                @else
                    <!-- Información del usuario -->
                    <div class="px-4 flex items-center gap-3">
                        <img class="h-10 w-10 rounded-full" src="{{ Auth::user()->avatar_url }}" alt="Avatar">
                        <div>
                            <div class="font-medium text-base">{{ Auth::user()->name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <!-- Links de usuario -->
                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('profile.show', Auth::id())">Mi Perfil</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('profile.index')">Listas y Favoritos</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('profile.saves')">Guardados</x-responsive-nav-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Cerrar Sesión
                            </x-responsive-nav-link>
                        </form>
                    </div>
                @endguest
            </div>
        </div>
    </div>

    <!-- Modal de búsqueda -->
    <div x-data="searchComponent()" x-show="searchOpen" x-cloak
        class="fixed inset-0 z-50 flex items-start justify-center pt-24 px-4">
        <!-- Fondo oscuro -->
        <div @click="searchOpen = false" class="absolute inset-0 bg-black opacity-40"></div>

        <!-- Contenedor del modal -->
        <div class="relative bg-white rounded-lg shadow-lg w-full max-w-2xl p-4 z-50">
            <div class="flex justify-between items-center mb-2">
                <input type="text" x-model="searchQuery" @input.debounce.300ms="fetchResults()"
                    placeholder="Busca animes, personajes, usuarios o listas..."
                    class="w-full p-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
                <button @click="searchOpen = false" class="ml-2 px-3 py-1 rounded hover:bg-gray-100">Cerrar</button>
            </div>
            <div class="max-h-80 overflow-auto text-sm text-gray-700">
                <template x-if="results">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <!-- Animes -->
                        <div x-show="results.animes.length">
                            <h3 class="text-sm font-semibold mb-1">Animes</h3>
                            <ul class="space-y-1">
                                <template x-for="anime in results.animes" :key="anime.id">
                                    <li>
                                        <a :href="`/animes/${anime.id}`"
                                            class="flex items-center gap-2 p-1 hover:bg-gray-100 rounded">
                                            <img :src="anime.image" class="w-10 h-10 object-cover rounded"
                                                alt="">
                                            <span x-text="anime.title"></span>
                                        </a>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <!-- Personajes -->
                        <div x-show="results.characters.length">
                            <h3 class="text-sm font-semibold mb-1">Personajes</h3>
                            <ul class="space-y-1">
                                <template x-for="char in results.characters" :key="char.id">
                                    <li>
                                        <a :href="`/animes/${char.anime_id}/personajes/${char.id}`"
                                            class="flex items-center gap-2 p-1 hover:bg-gray-100 rounded">
                                            <img :src="char.image" class="w-10 h-10 object-cover rounded"
                                                alt="">
                                            <span x-text="char.name"></span>
                                        </a>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <!-- Usuarios -->
                        <div x-show="results.users.length">
                            <h3 class="text-sm font-semibold mb-1">Usuarios</h3>
                            <ul class="space-y-1">
                                <template x-for="user in results.users" :key="user.id">
                                    <li>
                                        <a :href="`/profile/${user.id}`"
                                            class="flex items-center gap-2 p-1 hover:bg-gray-100 rounded">
                                            <img :src="user.avatar" class="w-10 h-10 object-cover rounded"
                                                alt="">
                                            <span x-text="user.name"></span>
                                        </a>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <!-- Listas de Animes -->
                        <div x-show="results.anime_lists.length">
                            <h3 class="text-sm font-semibold mb-1">Listas de Animes</h3>
                            <ul class="space-y-1">
                                <template x-for="list in results.anime_lists" :key="list.id">
                                    <li>
                                        <a :href="`/listas/anime/public`"
                                            class="flex items-center gap-2 p-1 hover:bg-gray-100 rounded">
                                            <span x-text="list.name"></span>
                                        </a>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <!-- Listas de Personajes -->
                        <div x-show="results.char_lists.length">
                            <h3 class="text-sm font-semibold mb-1">Listas de Personajes</h3>
                            <ul class="space-y-1">
                                <template x-for="list in results.char_lists" :key="list.id">
                                    <li>
                                        <a :href="`/listas/characters/public`"
                                            class="flex items-center gap-2 p-1 hover:bg-gray-100 rounded">
                                            <span x-text="list.name"></span>
                                        </a>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <!-- Mensaje si no hay resultados -->
                        <template
                            x-if="!results.animes.length && !results.characters.length && !results.users.length && !results.anime_lists.length && !results.char_lists.length">
                            <p class="text-gray-500 px-1 py-2 col-span-full">No se encontraron resultados</p>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function searchComponent() {
            return {
                searchQuery: '',
                results: {
                    animes: [],
                    characters: [],
                    users: [],
                    anime_lists: [],
                    char_lists: []
                },
                fetchResults() {
                    if (this.searchQuery.length < 2) {
                        this.results = {
                            animes: [],
                            characters: [],
                            users: [],
                            anime_lists: [],
                            char_lists: []
                        };
                        return;
                    }
                    fetch(`/search?q=${encodeURIComponent(this.searchQuery)}`)
                        .then(r => r.json())
                        .then(data => {
                            this.results = data
                        })
                        .catch(e => console.error(e));
                }
            }
        }
    </script>
</nav>
