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

    // Attach live validation
    function attachLiveValidation(form) {
        form.querySelectorAll("input, textarea").forEach(input => {
            input.addEventListener('input', function () {
                validateInput(input);
            });
        });
    }
    if (addForm) attachLiveValidation(addForm);
    if (editForm) attachLiveValidation(editForm);

    // Open & Close Add Course Modal
    if (addBtn && addModal && closeAddModalBtn && addForm) {
        addBtn.addEventListener("click", () => addModal.style.display = "flex");
        closeAddModalBtn.addEventListener("click", () => addModal.style.display = "none");

        window.addEventListener("click", event => {
            if (event.target === addModal) addModal.style.display = "none";
        });

        addForm.addEventListener("submit", function (event) {
            // Prevent form submission if validation fails
            if (!validateForm(addForm)) {
                event.preventDefault();
            }
        });
    }

    // File Input Element
    const fileInput = document.getElementById("pdfUpload");
    const currentFileDisplay = document.getElementById('pdf-file-name');

    // Handle file selection and display selected file
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const file = fileInput.files[0];
            if (file) {
                currentFileDisplay.textContent = `Fichier sélectionné: ${file.name}`;
            } else {
                currentFileDisplay.textContent = "Aucun fichier sélectionné";
            }
        });
    }

    // Open & Close Edit Course Modal
if (editModal && closeEditModalBtn && editForm) {
    editBtns.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();

            const courseId = this.getAttribute('data-id');
            const nomcours = this.getAttribute('data-nomcours');
            const description = this.getAttribute('data-description');
            const duree = this.getAttribute('data-duree');
            const type = this.getAttribute('data-type');
            const date = this.getAttribute('data-date');
            const objectifs = this.getAttribute('data-objectifs');
            const Filename = this.getAttribute('data-Filename');
            const image = this.getAttribute('data-image');

            // Update form action and form fields
            editForm.action = editForm.action.replace('PLACEHOLDER_ID', courseId);
            editForm.querySelector('[name="NomCours"]').value = nomcours;
            editForm.querySelector('[name="description"]').value = description;
            editForm.querySelector('[name="duree"]').value = duree;
            editForm.querySelector('[name="type"]').value = type;
            editForm.querySelector('[name="date"]').value = date;
            editForm.querySelector('[name="objectifs"]').value = objectifs;

            // Reset the file input fields when opening the modal
            const fileInput = document.getElementById('pdfUpload');
            const imageInput = document.getElementById('image');
            
            if (fileInput) {
                // If no new file is selected, retain the current file name
                currentFileDisplay.textContent = Filename ? `Fichier actuel: ${Filename}` : "Aucun fichier sélectionné";
            }

            if (imageInput) {
                document.getElementById('image-file-name').textContent = image ? `Image actuelle: ${image}` : "Aucune image sélectionnée";
            }

            // Reset the actual file inputs
            if (fileInput) {
                fileInput.value = '';  // Reset file input field
            }

            if (imageInput) {
                imageInput.value = '';  // Reset image input field
            }

            // Show modal
            editModal.style.display = "flex";
        });
    });

    closeEditModalBtn.addEventListener('click', () => editModal.style.display = "none");

    window.addEventListener('click', event => {
        if (event.target === editModal) editModal.style.display = "none";
    });

    editForm.addEventListener("submit", function (event) {
        // Prevent form submission if validation fails
        if (!validateForm(editForm)) {
            event.preventDefault();
        }
    });

    
        closeEditModalBtn.addEventListener('click', () => editModal.style.display = "none");

        window.addEventListener('click', event => {
            if (event.target === editModal) editModal.style.display = "none";
        });

        editForm.addEventListener("submit", function (event) {
            // Prevent form submission if validation fails
            if (!validateForm(editForm)) {
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
        const date = form.querySelector("input[name='date']");
        const objectifs = form.querySelector("textarea[name='objectifs']");
        const Filename = form.querySelector("input[name='Filename']");
        const image = form.querySelector("input[name='image']");

        let isValid = true;

        // Check all inputs for validation
        [nomCours, description, duree, type, date, objectifs, Filename, image].forEach(input => {
            if (!validateInput(input)) isValid = false;
        });

        return isValid;
    }

    // Validation for Each Input Field
    function validateInput(input) {
        let errorMessage = "";
        let value = input.value.trim();

        const stringRegex = /^[A-Za-z\s]+$/;
        const dureeRegex = /^[0-9]+h$/;
        const dateRegex = /^\d{4}-\d{2}-\d{2}$/;

        if (input.name === "NomCours") {
            if (!value || !stringRegex.test(value)) errorMessage = "Le nom du cours doit contenir uniquement des lettres.";
        } else if (input.name === "description") {
            if (!value || value.length < 5) errorMessage = "La description doit contenir au moins 5 caractères.";
        } else if (input.name === "duree") {
            if (!value || !dureeRegex.test(value)) errorMessage = "La durée doit être un nombre suivi de 'h' (ex: 2h, 10h).";
        } else if (input.name === "type") {
            if (!value || (value.toLowerCase() !== "premium" && value.toLowerCase() !== "free")) {
                errorMessage = "Le type doit être 'premium' ou 'free'.";
            }
        } else if (input.name === "date") {
            // Check if the date is in the future
            let inputDate = new Date(value);
            inputDate.setHours(0, 0, 0, 0); // Reset input date time to midnight
            let today = new Date();
            today.setHours(0, 0, 0, 0); // Reset today's time for comparison

            // If the date is in the future, show error message
            if (inputDate > today) {
                errorMessage = "La date ne peut pas être dans le futur.";
            }
        } else if (input.name === "objectifs") {
            const sentences = value.split('.').filter(sentence => sentence.trim().length > 0);
            if (sentences.length < 2) errorMessage = "Les objectifs doivent contenir au moins deux phrases.";
        }  else if (input.name === "image") {
            if (input.files.length > 0) {
                const file = input.files[0];
                if (!file.type.includes("jpeg") && !file.type.includes("jpg")) {
                    errorMessage = "L'image doit être au format JPG.";
                }
            }
            // Image is not required, so we only check if a file is selected
            else {
                // Do not show the error if no new image is selected (i.e., user keeps the old one)
                errorMessage = "";
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
        errorSpan.textContent = message ? message : "";
        errorSpan.style.color = message ? "black" : "";
        errorSpan.style.fontSize = "12px";
    }
});
