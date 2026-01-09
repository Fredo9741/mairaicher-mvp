// Gestion de l'ajout au panier en AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Intercepter tous les formulaires d'ajout au panier
    const cartForms = document.querySelectorAll('[data-cart-form]');

    cartForms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const url = this.action;
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;

            // Désactiver le bouton pendant la requête
            submitButton.disabled = true;
            submitButton.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Mettre à jour le compteur du panier
                    updateCartCount(data.cartCount);

                    // Afficher la notification de succès
                    showNotification(data.message, 'success');

                    // Réinitialiser le formulaire
                    this.reset();
                    const quantityInput = this.querySelector('input[name="quantity"]');
                    if (quantityInput) {
                        quantityInput.value = quantityInput.step && quantityInput.step === '0.1' ? '0.1' : '1';
                    }
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue. Veuillez réessayer.', 'error');
            } finally {
                // Réactiver le bouton
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
    });
});

// Fonction pour mettre à jour le compteur du panier
function updateCartCount(count) {
    const cartBadges = document.querySelectorAll('[data-cart-count]');
    cartBadges.forEach(badge => {
        if (count > 0) {
            // Mise à jour du contenu
            badge.textContent = count;

            // Retirer hidden et forcer les styles
            badge.classList.remove('hidden');

            // Forcer les styles inline pour contourner tout problème CSS
            badge.style.backgroundColor = 'rgb(5, 150, 105)'; // emerald-600
            badge.style.color = 'rgb(255, 255, 255)'; // white
            badge.style.display = 'inline-flex';
        } else {
            badge.textContent = '';
            badge.classList.add('hidden');
            badge.style.display = 'none';
        }
    });
}

// Fonction pour afficher une notification
function showNotification(message, type = 'success') {
    // Supprimer les notifications existantes
    const existing = document.querySelectorAll('.toast-notification');
    existing.forEach(el => el.remove());

    // Créer la notification
    const notification = document.createElement('div');
    notification.className = `toast-notification rounded-lg shadow-lg border-l-4 transform transition-all duration-300 ease-in-out ${
        type === 'success' ? 'border-emerald-500' : 'border-red-500'
    }`;
    notification.style.position = 'fixed';
    notification.style.top = '80px';
    notification.style.right = '16px';
    notification.style.maxWidth = '24rem';
    notification.style.width = '100%';
    notification.style.zIndex = '9999';

    // Background avec dégradé jaune léger pour succès
    if (type === 'success') {
        notification.style.background = 'linear-gradient(to left, rgba(238, 245, 39, 1), rgba(238, 245, 39, 0.7))';
    } else {
        notification.style.background = 'white';
    }

    notification.innerHTML = `
        <div class="p-4 flex items-center">
            <div class="flex-shrink-0">
                ${type === 'success' ? `
                    <svg class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                ` : `
                    <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                `}
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-gray-900">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 flex-shrink-0 inline-flex text-gray-400 hover:text-gray-500">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;

    // Position initiale hors écran à droite
    notification.style.transform = 'translateX(400px)';

    document.body.appendChild(notification);

    // Animer l'entrée
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 10);

    // Supprimer automatiquement après 4 secondes
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}
