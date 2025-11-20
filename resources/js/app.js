import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Buscador dinamico de animes
import './anime/search.js';

// Ajuste de texto dinamico
import { initFitText } from './anime/FitText.js';

document.addEventListener('DOMContentLoaded', () => {
    initFitText({ min: 16, max: 60, step: 1 });
});
