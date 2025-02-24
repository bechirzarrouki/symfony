document.addEventListener('DOMContentLoaded', function () {
    // Modal Elements for Certif
    const addModal = document.getElementById("addCertifModal");
    const editModal = document.getElementById("editCertifModal");
    const addBtn = document.querySelector(".btn-more"); // Button to open add modal
    const editBtns = document.querySelectorAll('.btn-modify'); // Buttons for edit modal
    const closeAddModalBtn = document.querySelector(".close-btn-add");
    const closeEditModalBtn = document.querySelector(".close-btn-edit");

    // Form Elements
    const addForm = document.querySelector("#addCertifModal form");
    const editForm = document.querySelector("#editCertifForm");

    // Attach live validation for each input in the given form
    function attachLiveValidation(form) {
        form.querySelectorAll("input").forEach(input => {
            input.addEventListener('input', function () {
                validateInput(input);
            });
        });
    }

    if (addForm) {
        attachLiveValidation(addForm);
    }
    if (editForm) {
        attachLiveValidation(editForm);
    }

    // Add Certif Modal Logic
    if (addBtn && addModal && closeAddModalBtn && addForm) {
        addBtn.addEventListener("click", function () {
            addModal.style.display = "flex";
        });

        closeAddModalBtn.addEventListener("click", function () {
            addModal.style.display = "none";
        });

        window.addEventListener("click", function (event) {
            if (event.target === addModal) {
                addModal.style.display = "none";
            }
        });

        addForm.addEventListener("submit", function (event) {
            let isValid = validateForm(addForm);
            if (!isValid) {
                event.preventDefault();
            }
        });
    }

    // Edit Certif Modal Logic
    if (editModal && closeEditModalBtn && editForm) {
        editBtns.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault(); // Prevent default link behavior

                const certifId = this.getAttribute('data-id');
                const titre = this.getAttribute('data-titre');
                const date = this.getAttribute('data-date');
                const duree = this.getAttribute('data-duree');
                const NomUser = this.getAttribute('data-NomUser');
                const IdCours = this.getAttribute('data-IdCours');

                // Update form action and populate fields
                editForm.action = editForm.action.replace('PLACEHOLDER_ID', certifId);
                editForm.querySelector('[name="titre"]').value = titre;
                editForm.querySelector('[name="date"]').value = date;
                editForm.querySelector('[name="duree"]').value = duree;
                editForm.querySelector('[name="NomUser"]').value = NomUser;
                editForm.querySelector('[name="IdCours"]').value = IdCours;

                editModal.style.display = "flex";
            });
        });

        closeEditModalBtn.addEventListener("click", function () {
            editModal.style.display = "none";
        });

        window.addEventListener("click", function (event) {
            if (event.target === editModal) {
                editModal.style.display = "none";
            }
        });

        editForm.addEventListener("submit", function (event) {
            let isValid = validateForm(editForm);
            if (!isValid) {
                event.preventDefault();
            }
        });
    }

    // Validate entire form by validating each input field.
    function validateForm(form) {
        let isValid = true;
        form.querySelectorAll("input").forEach(input => {
            if (!validateInput(input)) {
                isValid = false;
            }
        });
        return isValid;
    }

    // Validate individual input based on its name and value.
    function validateInput(input) {
        let value = input.value.trim();
        // If empty, show required message.
        if (value === "") {
            showError(input, "This field is required.");
            return false;
        }

        let errorMessage = "";

        if (input.name === "titre" || input.name === "NomUser") {
            // Allow only letters and spaces.
            let regex = /^[A-Za-z\s]+$/;
            if (!regex.test(value)) {
                errorMessage = "This field must contain only letters.";
            }
        } else if (input.name === "date") {
            // Check that the date is not in the future.
            let inputDate = new Date(value);
            inputDate.setHours(0, 0, 0, 0); // Reset input date time to midnight
            let today = new Date();
            today.setHours(0, 0, 0, 0); // Reset today's time for comparison
            if (inputDate > today) {
                errorMessage = "The date cannot be in the future.";
            }
        } else if (input.name === "duree") {
            // Must be a number followed by month(s) or year(s)
            let regex = /^\d+\s*(months|month|years|year)$/i;
            if (!regex.test(value)) {
                errorMessage = "Duree must be a number followed by 'month(s)' or 'year(s)'.";
            }
        }

        showError(input, errorMessage);
        return errorMessage === "";
    }

    // Display or clear an error message next to an input field.
    function showError(input, message) {
        let errorSpan = input.nextElementSibling;
        if (!errorSpan || !errorSpan.classList.contains("error-message")) {
            errorSpan = document.createElement("span");
            errorSpan.classList.add("error-message");
            input.parentNode.insertBefore(errorSpan, input.nextSibling);
        }
        errorSpan.textContent = message;
        errorSpan.style.color = "red";
        errorSpan.style.fontSize = "12px";
    }
});
