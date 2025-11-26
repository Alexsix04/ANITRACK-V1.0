<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🎲 Randomizer de Anime
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg sm:rounded-xl p-8 border border-gray-100">

                <!-- Selector de listas -->
                <div class="mb-6">
                    <label for="animeListSelect" class="block mb-3 font-semibold text-lg text-gray-700">
                        Selecciona una lista:
                    </label>

                    <select id="animeListSelect"
                        class="border-gray-300 rounded-lg shadow-sm w-full p-3 text-gray-700 focus:ring focus:ring-blue-200 focus:border-blue-400 transition">
                        <option value="">-- Selecciona una lista --</option>

                        <optgroup label="Tus listas">
                            @foreach ($userLists as $list)
                                <option value="{{ $list->id }}" data-type="user">{{ $list->name }}</option>
                            @endforeach
                        </optgroup>

                        <optgroup label="Listas guardadas">
                            @foreach ($savedLists as $saved)
                                @if ($saved->list)
                                    <option value="{{ $saved->id }}" data-type="saved">
                                        ⭐ {{ $saved->list->name }}
                                    </option>
                                @endif
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <!-- Botón principal -->
                <button id="randomButton"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg text-lg font-semibold shadow 
                        hover:bg-blue-700 hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                    Sacar anime aleatorio
                </button>

                <!-- Resultado -->
                <div id="randomAnime" class="mt-10 text-center"></div>

            </div>
        </div>
    </div>

    <style>
        .fade-in {
            animation: fadeIn 0.7s ease forwards;
        }

        .bounce {
            animation: bounceIn 0.6s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }

            60% {
                transform: scale(1.05);
                opacity: 1;
            }

            80% {
                transform: scale(0.95);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>

    <script>
        document.getElementById('randomButton').addEventListener('click', async () => {
            const select = document.getElementById('animeListSelect');
            const option = select.options[select.selectedIndex];

            if (!option.value) return alert("Selecciona una lista");

            const listId = option.value;
            const listType = option.dataset.type;
            const container = document.getElementById('randomAnime');

            container.innerHTML = `<p class="text-gray-500 text-lg">Cargando animes...</p>`;

            // 1️⃣ Obtener todos los animes de la lista
            const listResponse = await fetch('{{ route('randomizer.getListAnimes') }}', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    list_id: listId,
                    list_type: listType
                })
            });

            const listData = await listResponse.json();

            // Manejar listas que no existen o no pertenecen al usuario
            if (listResponse.status === 404) {
                container.innerHTML = `<p class="text-red-500">❌ No tienes permiso para ver esta lista</p>`;
                return;
            }

            if (!listData.animes || listData.animes.length === 0) {
                container.innerHTML = `<p class="text-red-500">La lista no tiene animes</p>`;
                return;
            }

            const fakeAnimes = listData.animes; // animes reales de la lista

            // 2️⃣ Mostrar el shuffle con ANIMES REALES
            let iterations = 12;
            let index = 0;

            const interval = setInterval(() => {
                const anime = fakeAnimes[Math.floor(Math.random() * fakeAnimes.length)];

                container.innerHTML = `
            <div class="flex flex-col items-center">
                <img src="${anime.image}"
                     class="h-72 w-52 object-cover rounded-xl shadow-lg transition-all duration-150">
                <p class="mt-2 text-gray-600">Selecionando Anime...</p>
            </div>
        `;

                index++;

                if (index >= iterations) {
                    clearInterval(interval);
                    showFinalAnime(); // mostrar anime definitivo
                }
            }, 120);

            // 3️⃣ Petición del anime definitivo
            async function showFinalAnime() {
                const response = await fetch('{{ route('randomizer.getRandomAnime') }}', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        list_id: listId,
                        list_type: listType
                    })
                });

                const final = await response.json();

                container.innerHTML = `
            <div class="flex flex-col items-center animate-[fadeIn_0.4s_ease]">
                <img src="${final.image}" class="h-80 w-56 object-cover rounded-xl shadow-xl mb-4">
                <h3 class="text-2xl font-bold mb-4">${final.title}</h3>

                <div class="flex gap-4">
                    <button id="closeBtn" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg shadow">
                        OK
                    </button>

                    <button id="retryBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow">
                        Volver a tirar
                    </button>
                </div>
            </div>
        `;

                document.getElementById("closeBtn").onclick = () =>
                    container.innerHTML = "";

                document.getElementById("retryBtn").onclick = () =>
                    document.getElementById("randomButton").click();
            }
        });
    </script>
</x-app-layout>
