import './bootstrap';

import Alpine from 'alpinejs';

// Exponer Alpine globalmente para que Livewire 3 lo detecte y lo inicie él mismo.
// NO llamar Alpine.start() aquí — Livewire lo hace internamente.
window.Alpine = Alpine;
