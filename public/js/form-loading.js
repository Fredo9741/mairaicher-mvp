/**
 * Gestion automatique des états de chargement pour tous les formulaires
 * Ajoute automatiquement un spinner et désactive les boutons pendant la soumission
 */

document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les formulaires qui ne sont pas Livewire
    const forms = document.querySelectorAll('form:not([wire\\:submit])');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');

            if (!submitButton) return;

            // Éviter les soumissions multiples
            if (submitButton.disabled) {
                e.preventDefault();
                return;
            }

            // Désactiver le bouton
            submitButton.disabled = true;
            submitButton.classList.add('opacity-75', 'cursor-not-allowed');

            // Sauvegarder le contenu original du bouton
            const originalContent = submitButton.innerHTML;
            submitButton.setAttribute('data-original-content', originalContent);

            // Créer le spinner
            const spinner = `
                <svg class="animate-spin w-5 h-5 inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Chargement...</span>
            `;

            // Remplacer le contenu du bouton
            submitButton.innerHTML = spinner;

            // Restaurer le bouton après un timeout (sécurité en cas d'erreur)
            setTimeout(() => {
                if (submitButton.disabled) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-75', 'cursor-not-allowed');
                    submitButton.innerHTML = originalContent;
                }
            }, 10000); // 10 secondes max
        });
    });

    // Restaurer les boutons si la page revient en arrière
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            forms.forEach(form => {
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton && submitButton.hasAttribute('data-original-content')) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-75', 'cursor-not-allowed');
                    submitButton.innerHTML = submitButton.getAttribute('data-original-content');
                }
            });
        }
    });
});

/**
 * Alpine.js directive pour les formulaires avec Alpine
 * Usage: <form x-data="formLoading()" @submit="handleSubmit">
 */
function formLoading() {
    return {
        isLoading: false,
        originalButtonText: '',

        handleSubmit(event) {
            const submitButton = event.target.querySelector('button[type="submit"]');

            if (!submitButton || this.isLoading) {
                if (this.isLoading) event.preventDefault();
                return;
            }

            this.isLoading = true;
            this.originalButtonText = submitButton.innerHTML;

            // Le formulaire se soumettra naturellement après cette fonction
        },

        resetLoading() {
            this.isLoading = false;
        }
    };
}
