// Vite entry for generated Breeze pages. Loads bootstrap.js, exposes Alpine, and starts Alpine
// for dropdowns/modals in Dashboard/Profile. Custom auth/product scripts are linked separately
// from public/js.

import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
