document.addEventListener('DOMContentLoaded', function () {
    // Modal Elements
    const addModal = document.getElementById("addCourseModal");
    const editModal = document.getElementById("editCourseModal");
    const addBtn = document.querySelector(".btn-more");
    const editBtns = document.querySelectorAll('.btn-modify');
    const closeAddModalBtn = document.querySelector(".close-btn-add");
    const closeEditModalBtn = document.querySelector(".close-btn-edit");

    // Form Elements
    const addForm = document.querySelector("#addCourseModal form");
    const editForm = document.querySelector("#editCourseForm");

    // Attach live validation to all inputs in the forms
    function attachLiveValidation(form) {
        form.querySelectorAll("input").forEach(input => {
            input.addEventListener('input', function() {
                validateInput(input);
            });
        });
    }
    if(addForm) attachLiveValidation(addForm);
    if(editForm) attachLiveValidation(editForm);

    // Add Course Modal Logic
    if (addBtn && addModal && closeAddModalBtn && addForm) {
        // Open Add Course Modal
        addBtn.addEventListener("click", function () {
            addModal.style.display = "flex";
        });

        // Close Add Course Modal
        closeAddModalBtn.addEventListener("click", function () {
            addModal.style.display = "none";
        });

        // Close Add Course Modal when clicking outside
        window.addEventListener("click", function (event) {
            if (event.target === addModal) {
                addModal.style.display = "none";
            }
        });

        // Add Course Form Submit Logic
        addForm.addEventListener("submit", function (event) {
            let isValid = validateForm(addForm);
            if (!isValid) {
                event.preventDefault();
            }
        });
    }

    // Edit Course Modal Logic
    if (editModal && closeEditModalBtn && editForm) {
        // Open Edit Course Modal and populate data
        editBtns.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault(); // Prevent navigation

                const courseId = this.getAttribute('data-id');
                const nomcours = this.getAttribute('data-nomcours');
                const description = this.getAttribute('data-description');
                const duree = this.getAttribute('data-duree');
                const type = this.getAttribute('data-type');

                // Update form action and fill form fields with data
                editForm.action = editForm.action.replace('PLACEHOLDER_ID', courseId);
                editForm.querySelector('[name="NomCours"]').value = nomcours;
                editForm.querySelector('[name="description"]').value = description;
                editForm.querySelector('[name="duree"]').value = duree;
                editForm.querySelector('[name="type"]').value = type;

                // Show Edit Course Modal
                editModal.style.display = "flex";
            });
        });

        // Close Edit Course Modal
        closeEditModalBtn.addEventListener('click', function () {
            editModal.style.display = "none";
        });

        // Close Edit Course Modal when clicking outside
        window.addEventListener('click', function (event) {
            if (event.target === editModal) {
                editModal.style.display = "none";
            }
        });

        // Edit Course Form Submit Logic
        editForm.addEventListener("submit", function (event) {
            let isValid = validateForm(editForm);
            if (!isValid) {
                event.preventDefault();
            }
        });
    }

    // Form Validation Function
    function validateForm(form) {
        const nomCours = form.querySelector("input[name='NomCours']");
        const description = form.querySelector("input[name='description']");
        const duree = form.querySelector("input[name='duree']");
        const type = form.querySelector("input[name='type']");
        
        let isValid = true;

        // Check input validity
        [nomCours, description, duree, type].forEach(input => {
            if (!validateInput(input)) {
                isValid = false;
            }
        });
        return isValid;
    }

    // Validation for Each Input Field
    function validateInput(input) {
        let errorMessage = "";
        let value = input.value.trim();

        const stringRegex = /^[A-Za-z\s]+$/;
        const dureeRegex = /^[0-9]+h$/;

        if (input.name === "NomCours") {
            // Note: Adjust the regex if you want to allow numbers or other characters.
            if (!value || !stringRegex.test(value)) {
                errorMessage = "Le nom du cours doit contenir uniquement des lettres.";
            }
        } else if (input.name === "description") {
            if (!value || value.length < 5) {
                errorMessage = "La description doit contenir au moins 5 caractères.";
            }
        } else if (input.name === "duree") {
            if (!value || !dureeRegex.test(value)) {
                errorMessage = "La durée doit être un nombre suivi de 'h' (ex: 2h, 10h).";
            }
        } else if (input.name === "type") {
            if (!value || (value.toLowerCase() !== "premium" && value.toLowerCase() !== "free")) {
                errorMessage = "Le type doit être 'premium' ou 'free'.";
            }
        }

        showError(input, errorMessage);
        return errorMessage === "";
    }

    // Show or Clear Error Message Under Input Field
    function showError(input, message) {
        let errorSpan = input.nextElementSibling;
        if (!errorSpan || !errorSpan.classList.contains("error-message")) {
            errorSpan = document.createElement("span");
            errorSpan.classList.add("error-message");
            input.parentNode.insertBefore(errorSpan, input.nextSibling);
        }
        // If there's an error message, display it; otherwise, clear the span.
        if (message) {
            errorSpan.textContent = message;
            errorSpan.style.color = "black";
            errorSpan.style.fontSize = "12px";
        } else {
            errorSpan.textContent = "";
        }
    }
});
