document.addEventListener('DOMContentLoaded', () => {
    const isLoggedIn = Boolean(window.isLoggedIn); // true si está logueado
    const openAddBtn = document.getElementById('openAddToListModal');

    // ⛔ Si el botón NO existe en esta vista, detenemos el script
    if (!openAddBtn) return;

    const addModal = document.getElementById('addToListModal');
    const closeAddBtn = document.getElementById('closeAddToListModal');
    const cancelAddBtn = document.getElementById('cancelAddToList');

    const createModal = document.getElementById('createListModal');
    const closeCreateBtn = document.getElementById('closeCreateListModal');
    const cancelCreateBtn = document.getElementById('cancelCreateList');
    const createForm = document.getElementById('createListForm');

    const listSelect = document.getElementById('list_name');
    const statusSelect = document.getElementById('status');
    const episodeInput = document.getElementById('episode_progress');
    const maxEpisodes = parseInt(episodeInput?.getAttribute('max')) || 0;

    // ---------------------------------------------------------
    // 1) ABRIR MODAL → REDIRIGIR AL LOGIN SI NO ESTÁ LOGUEADO
    // ---------------------------------------------------------
    openAddBtn.addEventListener('click', () => {
        if (!isLoggedIn) {
            window.location.href = window.loginUrl;
            return;
        }

        addModal.classList.remove('hidden', 'opacity-0', 'scale-95');
        addModal.classList.add('flex', 'opacity-100', 'scale-100');

        const defaultOption = Array.from(listSelect.options).find(opt => opt.value === 'Pendientes');
        listSelect.value = defaultOption ? 'Pendientes' : listSelect.selectedIndex = 0;
    });

    // ---------------------------------------------------------
    // 2) CERRAR MODAL PRINCIPAL
    // ---------------------------------------------------------
    [closeAddBtn, cancelAddBtn].forEach(btn =>
        btn.addEventListener('click', () => {
            addModal.classList.add('opacity-0', 'scale-95');
            setTimeout(() => addModal.classList.add('hidden'), 200);
        })
    );

    // ---------------------------------------------------------
    // 3) ABRIR SUB-MODAL (Crear nueva lista)
    // ---------------------------------------------------------
    listSelect.addEventListener('change', e => {
        if (e.target.value === '__new__') {
            createModal.classList.remove('hidden', 'opacity-0', 'scale-95');
            createModal.classList.add('flex', 'opacity-100', 'scale-100');
            addModal.classList.add('pointer-events-none');
        }
    });

    // ---------------------------------------------------------
    // 4) CERRAR SUB-MODAL
    // ---------------------------------------------------------
    [closeCreateBtn, cancelCreateBtn].forEach(btn =>
        btn.addEventListener('click', () => {
            createModal.classList.add('opacity-0', 'scale-95');
            setTimeout(() => createModal.classList.add('hidden'), 200);
            addModal.classList.remove('pointer-events-none');
            listSelect.value = '';
        })
    );

    // ---------------------------------------------------------
    // 5) CREAR NUEVA LISTA (AJAX)
    // ---------------------------------------------------------
    createForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(createForm);

        const response = await fetch(window.createListUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.csrfToken },
            body: formData
        });

        const result = await response.json();

        if (result.success && result.list) {
            const option = new Option(result.list.name, result.list.name, true, true);
            const createOption = listSelect.querySelector('option[value="__new__"]');
            listSelect.insertBefore(option, createOption);

            createModal.classList.add('opacity-0', 'scale-95');
            setTimeout(() => createModal.classList.add('hidden'), 200);
            addModal.classList.remove('pointer-events-none');
        } else {
            alert(result.message || 'Error al crear la lista.');
        }
    });

    // ---------------------------------------------------------
    // 6) AUTOAJUSTES SI SE SELECCIONA "Vistos"
    // ---------------------------------------------------------
    listSelect.addEventListener('change', e => {
        if (e.target.value === 'Vistos') {
            if (maxEpisodes > 0) episodeInput.value = maxEpisodes;

            statusSelect.value = 'completed';
            episodeInput.readOnly = true;
            episodeInput.classList.add('opacity-70', 'cursor-not-allowed');

            statusSelect.style.pointerEvents = 'none';
            statusSelect.classList.add('opacity-70', 'cursor-not-allowed');
        } else if (e.target.value && e.target.value !== '__new__') {
            episodeInput.readOnly = false;
            episodeInput.classList.remove('opacity-70', 'cursor-not-allowed');

            statusSelect.style.pointerEvents = '';
            statusSelect.classList.remove('opacity-70', 'cursor-not-allowed');
        }
    });
});