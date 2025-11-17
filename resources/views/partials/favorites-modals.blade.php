<!-- ===================================================== -->
<!-- 🪄 MODALES DE FAVORITOS -->
<!-- ===================================================== -->

<!-- MODAL ANIMES FAVORITOS -->
<div id="animesModal"
    class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 opacity-0 scale-95">
    <div class="bg-white p-6 rounded-xl shadow-xl w-11/12 max-w-5xl max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Todos los Animes Favoritos</h2>
            <button id="closeAnimesModal" class="text-gray-600 hover:text-gray-800">✕</button>
        </div>

        @if ($animeFavorites->isEmpty())
            <p class="text-gray-500">Aún no hay animes en favoritos.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($animeFavorites as $fav)
                    @php
                        $anime = $fav->anime;
                    @endphp
                    @if ($anime)
                        <a href="{{ url('/animes/' . $anime->anilist_id) }}"
                           class="bg-gray-800 text-white p-4 rounded-2xl shadow-md hover:shadow-lg transition block hover:scale-[1.03]">
                            <img src="{{ $anime->cover_image ?? $fav->anime_image }}"
                                 alt="{{ $anime->title ?? $fav->anime_title }}"
                                 class="w-full h-64 object-cover rounded-lg mb-4">
                            <h3 class="text-lg font-bold mb-2 truncate">
                                {{ $anime->title ?? $fav->anime_title }}
                            </h3>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- MODAL PERSONAJES FAVORITOS -->
<div id="charsModal"
    class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 opacity-0 scale-95">
    <div class="bg-white p-6 rounded-xl shadow-xl w-11/12 max-w-5xl max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Todos los Personajes Favoritos</h2>
            <button id="closeCharsModal" class="text-gray-600 hover:text-gray-800">✕</button>
        </div>

        @if ($characterFavorites->isEmpty())
            <p class="text-gray-500">Aún no hay personajes en favoritos.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($characterFavorites as $fav)
                    <a href="{{ route('animes.characters.show', ['anime' => $fav->anime_anilist_id, 'character' => $fav->anilist_id]) }}"
                       class="bg-gray-800 text-white p-4 rounded-2xl shadow-md hover:shadow-lg transition block hover:scale-[1.03]">
                        <img src="{{ $fav->character_image }}" alt="{{ $fav->character_name }}"
                             class="w-full h-64 object-cover rounded-lg mb-4">
                        <h3 class="text-lg font-bold mb-2 truncate">{{ $fav->character_name }}</h3>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- ===================================================== -->
<!-- 🧩 JS MODALES FAVORITOS -->
<!-- ===================================================== -->
<script>
    function setupModal(openBtn, modal, closeBtn) {
        if (!modal) return;
        if (openBtn) openBtn.addEventListener('click', () => {
            modal.classList.remove('hidden', 'opacity-0', 'scale-95');
            modal.classList.add('opacity-100', 'scale-100');
        });
        if (closeBtn) closeBtn.addEventListener('click', () => closeModal(modal));
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(modal);
        });
        const modalContent = modal.querySelector('div');
        if (modalContent) modalContent.addEventListener('click', e => e.stopPropagation());
    }

    function closeModal(modal) {
        modal.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    // === FAVORITOS ===
    setupModal(
        document.getElementById('openAnimesModal'),
        document.getElementById('animesModal'),
        document.getElementById('closeAnimesModal')
    );
    setupModal(
        document.getElementById('openCharsModal'),
        document.getElementById('charsModal'),
        document.getElementById('closeCharsModal')
    );
</script>