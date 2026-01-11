import './bootstrap';

// Livewire 3 inclut déjà Alpine.js
// Pas besoin de l'importer manuellement

// Flatpickr pour le date picker avec jours désactivés
import flatpickr from 'flatpickr';
import { French } from 'flatpickr/dist/l10n/fr.js';

// Rendre Flatpickr disponible globalement
window.flatpickr = flatpickr;
window.flatpickrFrench = French;
