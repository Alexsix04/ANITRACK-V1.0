document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.favoriteButton');
    const toast = document.getElementById('toast');

    function showToast(message, color = 'bg-green-600') {
        toast.textContent = message;
        toast.className =
            `fixed top-5 right-5 ${color} text-white px-4 py-2 rounded-lg shadow-lg opacity-0 transition-opacity duration-500 pointer-events-none z-50`;
        setTimeout(() => toast.classList.add('opacity-100'), 10);
        setTimeout(() => toast.classList.remove('opacity-100'), 3000);
    }

    buttons.forEach(button => {
        // Inicializar estado
        if (button.dataset.isFavorite === 'true') {
            button.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
            button.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
        }

        button.addEventListener('click', async () => {
            const animeAnilistId = button.dataset.anilistId;
            const animeTitle = button.dataset.animeTitle;
            const animeImage = button.dataset.animeImage;
            const isFavorite = button.dataset.isFavorite === 'true';

            try {
                const response = await fetch(window.toggleFavoriteUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        anilist_id: animeAnilistId,
                        anime_title: animeTitle,
                        anime_image: animeImage
                    })
                });


                const data = await response.json();

                if (data.status === 'added') {
                    button.dataset.isFavorite = 'true';
                    button.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                    button.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
                    button.title = 'Quitar de favoritos';
                    button.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.782
                                1.4 8.172L12 18.896l-7.334 3.868
                                1.4-8.172-5.934-5.782 8.2-1.192z"/>
                        </svg>`;
                    showToast('✅ Anime añadido a favoritos', 'bg-green-600');
                } else if (data.status === 'removed') {
                    button.dataset.isFavorite = 'false';
                    button.classList.remove('bg-yellow-600', 'hover:bg-yellow-700');
                    button.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
                    button.title = 'Agregar a favoritos';
                    button.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 
                                   9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                        </svg>`;
                    showToast('❌ Anime eliminado de favoritos', 'bg-red-600');
                }

            } catch (error) {
                console.error('Error:', error);
                showToast('⚠️ Error al procesar la solicitud', 'bg-yellow-600');
            }
        });
    });
});