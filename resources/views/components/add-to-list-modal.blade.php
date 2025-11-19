<!-- ===================================================== -->
<!-- 🧩 MODAL PRINCIPAL: AÑADIR A MI LISTA (versión ampliada adaptada) -->
<!-- ===================================================== -->
<div id="addToListModal"
    class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-all opacity-0 scale-95">
    <div
        class="bg-gray-900 text-white p-8 rounded-2xl shadow-2xl w-full max-w-4xl relative flex flex-col md:flex-row gap-8">

        <!-- ✖ Botón cerrar -->
        <button id="closeAddToListModal"
            class="absolute top-4 right-4 text-gray-400 hover:text-white text-3xl font-bold">&times;</button>

        <!-- Columna izquierda: imagen + título -->
        <div
            class="flex flex-col items-center justify-start w-full md:w-2/5 border-b md:border-b-0 md:border-r border-gray-700 pb-6 md:pb-0 md:pr-6">
            <img src="{{ $anime['coverImage']['large'] }}" alt="{{ $anime['title']['romaji'] }}"
                class="w-56 h-80 object-cover rounded-xl shadow-lg mb-4">
            <h3 class="text-xl font-semibold text-center leading-tight">{{ $anime['title']['romaji'] }}</h3>
        </div>

        <!-- Columna derecha: formulario -->
        <div class="flex-1">
            <h2 class="text-3xl font-semibold mb-6">Añadir a mi lista</h2>

            <form action="{{ route('anime.addToList') }}" method="POST" id="addToListForm">
                @csrf
                <!-- Enviar opcionalmente anime_id si ya existe en base local -->
                <input type="hidden" name="anime_id" value="{{ $anime['id'] ?? '' }}">
                <!-- Siempre enviar AniList ID -->
                <input type="hidden" name="anilist_id" value="{{ $anime['anilist_id'] ?? $anime['id'] }}">
                <input type="hidden" name="anime_title" value="{{ $anime['title']['romaji'] }}">
                <input type="hidden" name="anime_image" value="{{ $anime['coverImage']['large'] }}">

                <!-- Selector de lista -->
                <label for="list_name" class="block mb-2 text-sm text-gray-300">Selecciona una lista</label>
                <select id="list_name" name="list_name"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 focus:ring-2 focus:ring-blue-500 text-base">
                    @foreach (auth()->user()->animeLists as $list)
                        <option value="{{ $list->name }}">{{ $list->name }}</option>
                    @endforeach
                    <option value="__new__">+ Crear nueva lista...</option>
                </select>

                <!-- Estado -->
                <label for="status" class="block mb-2 text-sm text-gray-300">Estado</label>
                <select id="status" name="status"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 focus:ring-2 focus:ring-blue-500 text-base">
                    <option value="watching">Viendo</option>
                    <option value="completed">Completado</option>
                    <option value="on_hold">En pausa</option>
                    <option value="dropped">Dropeado</option>
                    <option value="plan_to_watch" selected>Pendiente</option>
                </select>

                <!-- Progreso -->
                <label for="episode_progress" class="block mb-2 text-sm text-gray-300">Progreso de
                    episodios</label>
                <input type="number" id="episode_progress" name="episode_progress" min="0"
                    max="{{ $anime['episodes'] ?? 0 }}"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 text-base" placeholder="Ej: 5">

                <!-- Puntuación -->
                <label for="score" class="block mb-2 text-sm text-gray-300">Puntuación (0-10)</label>
                <input type="number" id="score" name="score" min="0" max="10"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 text-base">

                <!-- Notas -->
                <label for="notes" class="block mb-2 text-sm text-gray-300">Notas / observaciones</label>
                <textarea id="notes" name="notes" rows="2"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 text-base" placeholder="Anota algo aquí..."></textarea>

                <!-- Rewatch (con campo oculto para asegurar envío de 0 si no se marca) -->
                <input type="hidden" name="is_rewatch" value="0">
                <div class="flex items-center mb-4">
                    <input type="checkbox" id="is_rewatch" name="is_rewatch" value="1"
                        class="w-5 h-5 text-blue-600 border-gray-300 rounded">
                    <label for="is_rewatch" class="ml-2 text-sm text-gray-300">Rewatch</label>
                </div>

                <label for="rewatch_count" class="block mb-2 text-sm text-gray-300">Veces rewatch</label>
                <input type="number" id="rewatch_count" name="rewatch_count" min="0"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 mb-4 text-base" placeholder="Ej: 1">

                <!-- Botones -->
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" id="cancelAddToList"
                        class="px-5 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-base">Cancelar</button>
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-white text-base font-semibold">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ===================================================== -->
<!-- 🟢 SUB-MODAL: CREAR NUEVA LISTA -->
<!-- ===================================================== -->
<div id="createListModal"
    class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-[100] transition-all opacity-0 scale-95">
    <div class="bg-gray-900 text-white p-6 rounded-2xl shadow-xl w-full max-w-sm relative">
        <button id="closeCreateListModal"
            class="absolute top-3 right-3 text-gray-400 hover:text-white text-2xl">&times;</button>
        <h2 class="text-xl font-semibold mb-4">Crear nueva lista</h2>

        <form id="createListForm">
            @csrf
            <label for="list_name_new" class="block mb-2 text-sm text-gray-300">Nombre de la lista</label>
            <input type="text" id="list_name_new" name="name"
                class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2 mb-3"
                placeholder="Ej: En curso, Mis favoritos..." required>

            <label for="is_public" class="block mb-2 text-sm text-gray-300">Visibilidad</label>
            <select id="is_public" name="is_public"
                class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2 mb-4">
                <option value="1">Pública</option>
                <option value="0">Privada</option>
            </select>

            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelCreateList"
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg">Cancelar</button>
                <button type="submit"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white">Crear</button>
            </div>
        </form>
    </div>
</div>
