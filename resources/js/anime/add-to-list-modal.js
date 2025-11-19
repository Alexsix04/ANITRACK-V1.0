document.addEventListener('DOMContentLoaded', () => {
    const openAddBtn = document.getElementById('openAddToListModal');
    const addModal = document.getElementById('addToListModal');
    const closeAddBtn = document.getElementById('closeAddToListModal');
    const cancelAddBtn = document.getElementById('cancelAddToList');

    const createModal = document.getElementById('createListModal');
    const closeCreateBtn = document.getElementById('closeCreateListModal');
    const cancelCreateBtn = document.getElementById('cancelCreateList');
    const createForm = document.getElementById('createListForm');

    const listSelect = document.getElementById('list_name');

    // Mostrar modal principal
    openAddBtn.addEventListener('click', () => {
        addModal.classList.remove('hidden', 'opacity-0', 'scale-95');
        addModal.classList.add('flex', 'opacity-100', 'scale-100');

        // Por defecto seleccionar "Pendientes" (si existe)
        const defaultOption = Array.from(listSelect.options).find(opt => opt.value ===
            'Pendientes');
        if (defaultOption) {
            listSelect.value = 'Pendientes';
        } else {
            listSelect.selectedIndex = 0; // fallback
        }
    });

    // Cerrar modal principal
    [closeAddBtn, cancelAddBtn].forEach(btn => btn.addEventListener('click', () => {
        addModal.classList.add('opacity-0', 'scale-95');
        setTimeout(() => addModal.classList.add('hidden'), 200);
    }));

    // Abrir submodal
    listSelect.addEventListener('change', e => {
        if (e.target.value === '__new__') {
            createModal.classList.remove('hidden', 'opacity-0', 'scale-95');
            createModal.classList.add('flex', 'opacity-100', 'scale-100');
            addModal.classList.add('pointer-events-none'); // Bloquea interacción con fondo
        }
    });

    // Cerrar submodal
    [closeCreateBtn, cancelCreateBtn].forEach(btn => btn.addEventListener('click', () => {
        createModal.classList.add('opacity-0', 'scale-95');
        setTimeout(() => createModal.classList.add('hidden'), 200);
        addModal.classList.remove('pointer-events-none'); // Desbloquea modal principal
        listSelect.value = '';
    }));

    // Crear nueva lista por AJAX
    createForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(createForm);

        const response = await fetch(window.createListUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: formData
        });


        const result = await response.json();
        if (result.success && result.list) {
            const option = new Option(result.list.name, result.list.name, true, true);

            // Insertar justo antes de la opción "Crear nueva lista..."
            const createOption = listSelect.querySelector('option[value="__new__"]');
            listSelect.insertBefore(option, createOption);

            // Cerrar submodal y desbloquear el modal principal
            createModal.classList.add('opacity-0', 'scale-95');
            setTimeout(() => createModal.classList.add('hidden'), 200);
            addModal.classList.remove('pointer-events-none');

        } else {
            alert(result.message || 'Error al crear la lista.');
        }
    });

    // Autoajustar datos si se elige la lista "Vistos"
    const episodeInput = document.getElementById('episode_progress');
    const statusSelect = document.getElementById('status');
    const maxEpisodes = parseInt(episodeInput.getAttribute('max')) || 0;

    listSelect.addEventListener('change', e => {
        const selected = e.target.value;

        if (selected === 'Vistos') {
            // Rellenar datos automáticamente
            if (maxEpisodes > 0) episodeInput.value = maxEpisodes;
            statusSelect.value = 'completed';

            // Bloquear campos visualmente sin impedir envío
            episodeInput.readOnly = true;
            episodeInput.classList.add('opacity-70', 'cursor-not-allowed');

            statusSelect.style.pointerEvents = 'none';
            statusSelect.classList.add('opacity-70', 'cursor-not-allowed');
        } else if (selected && selected !== '__new__') {
            // Restaurar campos si cambia a otra lista
            episodeInput.readOnly = false;
            episodeInput.classList.remove('opacity-70', 'cursor-not-allowed');

            statusSelect.style.pointerEvents = '';
            statusSelect.classList.remove('opacity-70', 'cursor-not-allowed');
        }
    });
});