document.addEventListener("DOMContentLoaded", function () {
    // Fonction pour ouvrir un modal spécifique
    function openModal(modalId) {
        console.log("Ouverture du modal:", modalId);
        let modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "flex";
        } else {
            console.error("Le modal avec l'ID '" + modalId + "' n'existe pas.");
        }
    }

    // Fonction pour fermer un modal spécifique
    function closeModal(modalId) {
        let modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "none";
        }
    }

    // Ajout de l'événement au bouton pour ouvrir le modal
    document.querySelector('.investment-type').addEventListener('click', function() {
        openModal('typeModal');
    });

    // Sélection des types et fermeture du modal après validation
    let validateButton = document.querySelector("#typeModal .submit-btn");
    validateButton.addEventListener('click', function () {
        let selectedTypes = Array.from(document.querySelectorAll('.type-option.selected'))
            .map(option => option.getAttribute('data-value'))
            .join(', ');

        if (selectedTypes.length > 0) {
            document.querySelector('.investment-type').textContent = selectedTypes;
        }

        closeModal('typeModal');
    });

    // Clic sur les options pour ajouter à la sélection
    document.querySelectorAll('.type-option').forEach(option => {
        option.addEventListener('click', function() {
            this.classList.toggle('selected');
        });
    });




 
});

