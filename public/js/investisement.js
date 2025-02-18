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
function openEditModal(id) {
    document.getElementById("editModal"+id).style.display = "flex";
}

// Fermer le modal de modification
function closeEditModal(id) {
    document.getElementById("editModal"+id).style.display = "none";
}

// Sauvegarder les modifications
function saveChanges(id) {
    alert("Modifications enregistrées !");
    closeEditModal(id);
}

// Ouvrir le modal de suppression
function openDeleteModal(id) {
    document.getElementById("deleteModal" + id).style.display="flex";
}

// Fermer le modal de suppression
function closeDeleteModal(id) {
    document.getElementById("deleteModal" + id).style.display = "none";
}

// Confirmer la suppression
function confirmDelete(id) {
    alert("Investissement supprimé !");
    closeDeleteModal(id);
}
 // Toggle selection on type options
  function selectTypes() {
    const selectedOptions = document.querySelectorAll('#type-list .type-option.selected');
    const form = document.getElementById('investmentForm');

    if (!form) {
        console.error('Investment form not found!');
        return;
    }

    // Remove existing hidden inputs for investmentTypes
    document.querySelectorAll('input[name="investmentTypes[]"]').forEach(input => input.remove());

    // Get the container for displaying selected types
    const container = document.getElementById('selectedTypesContainer');
    if (container) {
        container.innerHTML = ''; // Clear previous selections
    }

    // Array to hold the selected type values
    const selectedTypes = [];

    selectedOptions.forEach(option => {
        const typeValue = option.getAttribute('data-value');
        selectedTypes.push(typeValue);

        // Create a hidden input to store the selected type in the form
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'investmentTypes[]';
        hiddenInput.value = typeValue;
        form.appendChild(hiddenInput);

        // Display the selected type (optional)
        if (container) {
            const span = document.createElement('span');
            span.textContent = typeValue;
            container.appendChild(span);
        }
    });

    console.log("Selected Types:", selectedTypes);

    // Close modal after selection
    closeModal('typeModal');
}

// Toggle selection state when clicking on a type


// Function to close the modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

// Function to open the modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
    }
}


// Ensure the function is only called when clicking the "Validate" button
document.getElementById('validateSelection').addEventListener('click', function (event) {
    event.preventDefault(); // Prevent accidental form submission
    selectTypes();
    console.log('Selected Types:', selectedTypes);
    closeModal('typeModal'); // Close the modal only after selection is confirmed
});
function validateForm(event) {
    let isValid = true;

    // Récupérer les valeurs des champs
    const description = document.getElementById('description').value;
    const typeRetour = document.getElementById('typeRetour').value;
    const tauxRendement = document.getElementById('tauxRendement').value;
    const dateDeadline = document.getElementById('date_deadline').value;
    const status = document.getElementById('status').value;

    // Vérifier si les champs sont vides
    if (!description==="" || !typeRetour || !tauxRendement || !dateDeadline || !status) {
        isValid = false;
        alert('Add return on your investmenet');
        displayErrorMessage('Tous les champs doivent être remplis.');
    }

    // Vérifier le format du taux de rendement (utilisation du pattern)
    const tauxPattern = new RegExp('^\\d+(\\.\\d{1,2})?$'); // Format : nombre entier ou décimal avec deux décimales max
    if (tauxRendement && !tauxPattern.test(tauxRendement)) {
        isValid = false;
        displayErrorMessage('Le taux de rendement doit être un nombre valide avec jusqu\'à deux décimales.');
    }

    // La validation de la date est déjà gérée par l'input de type "date"
    // Aucune validation supplémentaire nécessaire pour la date.

    // Afficher le message d'erreur si le formulaire est invalide
    if (!isValid) {
        
        event.preventDefault();
        // Empêcher la soumission du formulaire
    }
    return isValid;
}

// Fonction pour afficher les messages d'erreur
function displayErrorMessage(message) {
    const errorMessage = document.getElementById('errorMessage');
    errorMessage.textContent = message;
    errorMessage.style.display = 'block'; // Afficher le message d'erreur
}

// Fonction pour masquer le message d'erreur
function hideErrorMessage() {
    const errorMessage = document.getElementById('errorMessage');
    errorMessage.style.display = 'none';
}

// Attacher la validation du formulaire à l'événement de soumission
document.getElementById('form').addEventListener('submit', function(event) {
    hideErrorMessage(); // Masquer les erreurs précédentes
    validateForm(event);
});

function toggleOptionsMenu1(event) {
    event.stopPropagation();
    const dropdown = event.target.nextElementSibling;
    document.querySelectorAll('.options-dropdown1').forEach(menu => {
        if (menu !== dropdown) menu.classList.remove('show');
    });
    dropdown.classList.toggle('show');
}
function openDeleteReturn(id) {
    document.getElementById("deleteReturn" + id).style.display="flex";
}
