document.addEventListener("DOMContentLoaded", function () {
    // Fonction pour ouvrir un modal spécifique
    window.openModal = function (modalId) {
        console.log("Ouverture du modal:", modalId);
        let modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "flex";
        } else {
            console.error("Le modal avec l'ID '" + modalId + "' n'existe pas.");
        }
    };

    // Fonction pour fermer un modal spécifique
    window.closeModal = function (modalId) {
        let modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "none";
        }
    };

    // Sélection du bouton pour choisir le type d'investissement
    let investmentTypeBtn = document.querySelector('.investment-type');
    if (investmentTypeBtn) {
        investmentTypeBtn.addEventListener('click', function () {
            openModal('typeModal');
        });
    }

    // Sélection des types d'investissement et mise à jour du bouton
    let validateButton = document.querySelector("#typeModal .submit-btn");
    if (validateButton) {
        validateButton.addEventListener('click', function () {
            let selectedTypes = Array.from(document.querySelectorAll('.type-option.selected'))
                .map(option => option.getAttribute('data-value'))
                .join(', ');

            if (selectedTypes.length > 0) {
                investmentTypeBtn.textContent = selectedTypes;
            }

            closeModal('typeModal');
        });
    }

    // Ajout du toggle de sélection aux options
    document.querySelectorAll('.type-option').forEach(option => {
        option.addEventListener('click', function () {
            this.classList.toggle('selected');
        });
    });

    // Toggle des détails de l'investissement
    document.querySelectorAll('.investment-box').forEach(box => {
        box.addEventListener('click', function () {
            let details = this.querySelector('.investment-details');
            if (details) {
                details.style.display = details.style.display === 'block' ? 'none' : 'block';
            }
        });
    });

    // Ajout du toggle des détails d'investissement spécifiques
    document.querySelectorAll('.investment-content').forEach(investmentContent => {
        investmentContent.addEventListener('click', function () {
            let investmentDetails = investmentContent.nextElementSibling;
            if (investmentDetails && investmentDetails.classList.contains('investment-details')) {
                investmentDetails.style.display = (investmentDetails.style.display === 'none' || investmentDetails.style.display === '') ? 'block' : 'none';
            }
        });
    });

    // Validation du formulaire d'ajout de retour
    window.validateForm = function () {
        const description = document.getElementById("description").value.trim();
        const typeRetour = document.getElementById("typeRetour").value.trim();
        const tauxRendement = document.getElementById("tauxRendement").value.trim();
        const dateDeadline = document.getElementById("date_deadline").value;
        const status = document.getElementById("status").value.trim();

        if (!description || !typeRetour || !tauxRendement || !dateDeadline || !status) {
            alert("Tous les champs doivent être remplis.");
            return false;
        }

        if (!/^\d+(\.\d{1,2})?$/.test(tauxRendement)) {
            alert("Le taux de rendement doit être un nombre valide avec au maximum deux décimales.");
            return false;
        }

        const today = new Date().toISOString().split("T")[0];
        if (dateDeadline < today) {
            alert("La date limite doit être égale ou supérieure à aujourd'hui.");
            return false;
        }

        return true;
    };

    // Gestion du menu des options (trois points)
    function toggleOptionsMenu(event) {
        event.stopPropagation();
        const dropdown = event.target.nextElementSibling;

        // Fermer tous les autres menus
        document.querySelectorAll('.options-dropdown').forEach(menu => {
            if (menu !== dropdown) {
                menu.classList.remove('show');
            }
        });

        // Afficher/masquer le menu actuel
        dropdown.classList.toggle('show');
    }

    // Écouteur d'événement sur les trois points
    document.querySelectorAll(".options-menu i").forEach(icon => {
        icon.addEventListener("click", toggleOptionsMenu);
    });

    // Cacher les menus lorsqu'on clique ailleurs
    document.addEventListener("click", function () {
        document.querySelectorAll(".options-dropdown").forEach(menu => {
            menu.classList.remove("show");
        });
    });
});
document.addEventListener("DOMContentLoaded", function () {
    // Gérer l'ouverture du menu d'options
    document.querySelectorAll(".fas.fa-ellipsis-v").forEach(icon => {
        icon.addEventListener("click", function (event) {
            event.stopPropagation();
            let dropdown = this.nextElementSibling;
            closeAllDropdowns();
            dropdown.classList.toggle("show");
        });
    });

    // Fermer les menus si on clique ailleurs
    document.addEventListener("click", function () {
        closeAllDropdowns();
    });

    function closeAllDropdowns() {
        document.querySelectorAll(".options-dropdown").forEach(menu => {
            menu.classList.remove("show");
        });
    }
});

// Ouvrir le modal de modification
function openEditModal() {
    document.getElementById("editModal").style.display = "flex";
}

// Fermer le modal de modification
function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
}

// Sauvegarder les modifications
function saveChanges() {
    alert("Modifications enregistrées !");
    closeEditModal();
}

// Ouvrir le modal de suppression
function openDeleteModal() {
    document.getElementById("deleteModal").style.display = "flex";
}

// Fermer le modal de suppression
function closeDeleteModal() {
    document.getElementById("deleteModal").style.display = "none";
}

// Confirmer la suppression
function confirmDelete() {
    alert("Investissement supprimé !");
    closeDeleteModal();
}
