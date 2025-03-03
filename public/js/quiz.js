/*document.addEventListener('DOMContentLoaded', function () {
    let questionCount = 1;

    // Modal Elements for Quiz
    const quizModal = document.getElementById("quizModal");
    const openQuizPopup = document.getElementById("openQuizPopup");
    const closeQuizModalBtn = document.querySelector(".close-btn-quiz");

    // Form Elements
    const quizForm = document.getElementById("quizForm");

    // Attach live validation for each input in the given form
    function attachLiveValidation(form) {
        form.querySelectorAll("input").forEach(input => {
            input.addEventListener('input', function () {
                validateInput(input);
            });
        });
    }

    if (quizForm) {
        attachLiveValidation(quizForm);
    }

    // Open the Quiz Modal
    if (openQuizPopup && quizModal) {
        openQuizPopup.addEventListener("click", function () {
            quizModal.style.display = "flex";
        });
    }

    // Close the Quiz Modal
    if (closeQuizModalBtn) {
        closeQuizModalBtn.addEventListener("click", function () {
            quizModal.style.display = "none";
        });
    }

    // Close the modal if clicked outside of the modal content
    window.addEventListener("click", function (event) {
        if (event.target === quizModal) {
            quizModal.style.display = "none";
        }
    });

    // Add Question Dynamically
    document.getElementById("addQuestionButton").addEventListener("click", function () {
        questionCount++;

        // Create new question HTML
        let newQuestionHTML = `
        <div class="question" id="question${questionCount}">
            <label for="question${questionCount}Text">Question ${questionCount}:</label>
            <input type="text" name="questions[${questionCount - 1}][question]" id="question${questionCount}Text" required><br><br>

            <label for="answers${questionCount}">Answers (comma-separated):</label>
            <input type="text" name="questions[${questionCount - 1}][answers]" id="answers${questionCount}" placeholder="Answer 1, Answer 2, ..." required><br><br>

            <label for="correct${questionCount}">Correct Answer:</label>
            <input type="text" name="questions[${questionCount - 1}][correct]" id="correct${questionCount}" required><br><br>
            <button type="button" class="remove-question-btn" data-question-id="question${questionCount}">-</button>
        </div>
        `;

        // Append the new question to the container
        const questionsContainer = document.getElementById('questionsContainer');
        questionsContainer.insertAdjacentHTML('beforeend', newQuestionHTML);
    });

    // Use event delegation to handle remove button clicks
    document.getElementById('questionsContainer').addEventListener('click', function (event) {
        // Check if the clicked element is a remove button
        if (event.target && event.target.classList.contains('remove-question-btn')) {
            const questionId = event.target.getAttribute('data-question-id');
            const questionDiv = document.getElementById(questionId);
            if (questionDiv) {
                questionDiv.remove();  // Remove the question div
            }
            questionCount--;
        }
    });

    // Validate the form when it's submitted
    quizForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission
        let isValid = validateForm(quizForm);
        if (isValid) {
            // Traditional form submission
            quizForm.submit();  // Submit the form to the server via the form's action attribute
            quizModal.style.display = 'none'; // Optionally close the modal
        }
    });

    // Validate the entire form by checking each input field
    function validateForm(form) {
        let isValid = true;
        form.querySelectorAll("input").forEach(input => {
            if (!validateInput(input)) {
                isValid = false;
            }
        });
        return isValid;
    }

    // Validate individual input based on its name and value
    function validateInput(input) {
        let value = input.value.trim();
        // If empty, show required message
        if (value === "") {
            showError(input, "This field is required.");
            return false;
        }

        let errorMessage = "";

        if (input.name.includes("question")) {
            if (value.length < 5) {
                errorMessage = "Question must be at least 5 characters long.";
            }
        } else if (input.name.includes("answers")) {
            let answers = value.split(",");
            if (answers.length < 2) {
                errorMessage = "There should be at least two answers.";
            }
        } else if (input.name.includes("correct")) {
            if (value.trim() === "") {
                errorMessage = "Correct answer is required.";
            }
        }

        showError(input, errorMessage);
        return errorMessage === "";
    }

    // Display or clear an error message next to an input field
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
});*/
document.addEventListener('DOMContentLoaded', function () {
    let questionCountAdd = 1; // For Add Quiz Modal
    let questionCountEdit = 1; // For Edit Quiz Modal

    // Modal Elements for Quiz
    const quizModal = document.getElementById("quizModal");
    const editQuizModal = document.getElementById("editQuizModal");
    const openQuizPopup = document.getElementById("openQuizPopup");
    const editBtns = document.querySelectorAll('.btn-modify');
    const closeQuizModalBtn = document.querySelector(".close-btn-quiz");
    const closeEditQuizModalBtn = document.querySelector(".close-btn-edit-quiz");

    // Form Elements
    const quizForm = document.getElementById("quizForm");
    const editQuizForm = document.getElementById("editQuizForm");

    // Attach live validation for each input in the given form
    function attachLiveValidation(form) {
        form.querySelectorAll("input").forEach(input => {
            input.addEventListener('input', function () {
                validateInput(input);
            });
        });
    }

    // Attach live validation if forms exist
    if (quizForm) {
        attachLiveValidation(quizForm);
    }
    if (editQuizForm) {
        attachLiveValidation(editQuizForm);
    }

    // Open the Add Quiz Modal
    if (openQuizPopup && quizModal) {
        openQuizPopup.addEventListener("click", function () {
            quizModal.style.display = "flex"; // Open the modal
            console.log("Opening quiz modal...");
        });
    }

    // Close the Add Quiz Modal
    if (closeQuizModalBtn) {
        closeQuizModalBtn.addEventListener("click", function () {
            
            quizModal.style.display = "none"; // Close the modal
            console.log("Closing quiz modal...");
        });
    }
// Open the edit Quiz Modal
// Open the Edit Quiz Modal
if (editQuizModal && editBtns.length > 0) {
    editBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            editQuizModal.style.display = "flex"; // Open the modal
            console.log("Opening edit quiz modal...");
        });
    });
}

    // Close the Edit Quiz Modal
    if (closeEditQuizModalBtn) {
        closeEditQuizModalBtn.addEventListener("click", function () {
            editQuizModal.style.display = "none"; // Close the modal
        });
    }

    // Close the modal if clicked outside of the modal content
   window.addEventListener("click", function (event) {
        if (event.target === quizModal) {
            quizModal.style.display = "none";
            console.log("Clicked outside, closing quiz modal...");
        }
        if (event.target === editQuizModal) {
            editQuizModal.style.display = "none";
        }
    });

    // Empêche la propagation de l'événement pour les clics à l'intérieur du contenu du modal
    /*const modalContents = document.querySelectorAll(".modal-contenu");
    modalContents.forEach(content => {
        content.addEventListener("click", function (event) {
            event.stopPropagation(); // Empêche le clic de se propager et de déclencher la fermeture
        });
    });*/

    // Add Question Dynamically for Add Quiz
    document.getElementById("addQuestionButton").addEventListener("click", function () {
        questionCountAdd++;

        // Create new question HTML
        let newQuestionHTML = `
        <div class="question" id="question${questionCountAdd}">
            <label for="question${questionCountAdd}Text">Question ${questionCountAdd}:</label>
            <input type="text" name="questions[${questionCountAdd - 1}][question]" id="question${questionCountAdd}Text" required><br><br>

            <label for="answers${questionCountAdd}">Answers (comma-separated):</label>
            <input type="text" name="questions[${questionCountAdd - 1}][answers]" id="answers${questionCountAdd}" placeholder="Answer 1, Answer 2, ..." required><br><br>

            <label for="correct${questionCountAdd}">Correct Answer:</label>
            <input type="text" name="questions[${questionCountAdd - 1}][correct]" id="correct${questionCountAdd}" required><br><br>
            <button type="button" class="remove-question-btn" data-question-id="question${questionCountAdd}">-</button>
        </div>
        `;

        // Append the new question to the container
        const questionsContainer = document.getElementById('questionsContainer');
        questionsContainer.insertAdjacentHTML('beforeend', newQuestionHTML);
    });

    // Add Question Dynamically for Edit Quiz
    if (document.getElementById("addEditQuestionButton")) {
        document.getElementById("addEditQuestionButton").addEventListener("click", function () {
            questionCountEdit++;

            // Create new question HTML
            let newQuestionHTML = `
            <div class="question" id="editQuestion${questionCountEdit}">
                <label for="question${questionCountEdit}Text">Question ${questionCountEdit}:</label>
                <input type="text" name="questions[${questionCountEdit - 1}][question]" id="question${questionCountEdit}Text" required><br><br>

                <label for="answers${questionCountEdit}">Answers (comma-separated):</label>
                <input type="text" name="questions[${questionCountEdit - 1}][answers]" id="answers${questionCountEdit}" placeholder="Answer 1, Answer 2, ..." required><br><br>

                <label for="correct${questionCountEdit}">Correct Answer:</label>
                <input type="text" name="questions[${questionCountEdit - 1}][correct]" id="correct${questionCountEdit}" required><br><br>
                <button type="button" class="remove-question-btn" data-question-id="editQuestion${questionCountEdit}">-</button>
            </div>
            `;

            // Append the new question to the container
            const editQuestionsContainer = document.getElementById('editQuestionsContainer');
            editQuestionsContainer.insertAdjacentHTML('beforeend', newQuestionHTML);
        });
    }

    // Use event delegation to handle remove button clicks for Add Quiz
    if (document.getElementById('questionsContainer')) {
        document.getElementById('questionsContainer').addEventListener('click', function (event) {
            // Check if the clicked element is a remove button
            if (event.target && event.target.classList.contains('remove-question-btn')) {
                const questionId = event.target.getAttribute('data-question-id');
                const questionDiv = document.getElementById(questionId);
                if (questionDiv) {
                    questionDiv.remove();  // Remove the question div
                }
                questionCountAdd--;
            }
        });
    }

    // Use event delegation to handle remove button clicks for Edit Quiz
    if (document.getElementById('editQuestionsContainer')) {
        document.getElementById('editQuestionsContainer').addEventListener('click', function (event) {
            // Check if the clicked element is a remove button
            if (event.target && event.target.classList.contains('remove-question-btn')) {
                const questionId = event.target.getAttribute('data-question-id');
                const questionDiv = document.getElementById(questionId);
                if (questionDiv) {
                    questionDiv.remove();  // Remove the question div
                }
                questionCountEdit--;
            }
        });
    }

    // Validate the form when it's submitted for Add Quiz
    if (quizForm) {
        quizForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent default form submission
            let isValid = validateForm(quizForm);
            if (isValid) {
                // Traditional form submission
                quizForm.submit();  // Submit the form to the server via the form's action attribute
                quizModal.style.display = 'none'; // Optionally close the modal
            }
        });
    }

    // Validate the form when it's submitted for Edit Quiz
    if (editQuizForm) {
        editQuizForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent default form submission
            let isValid = validateForm(editQuizForm);
            if (isValid) {
                // Traditional form submission
                editQuizForm.submit();  // Submit the form to the server via the form's action attribute
                editQuizModal.style.display = 'none'; // Optionally close the modal
            }
        });
    }

    // Validate the entire form by checking each input field
    function validateForm(form) {
        let isValid = true;
        form.querySelectorAll("input").forEach(input => {
            if (!validateInput(input)) {
                isValid = false;
            }
        });
        return isValid;
    }

    // Validate individual input based on its name and value
    function validateInput(input) {
        let value = input.value.trim();
        // If empty, show required message
        if (value === "") {
            showError(input, "This field is required.");
            return false;
        }

        let errorMessage = "";

        if (input.name.includes("question")) {
            if (value.length < 5) {
                errorMessage = "Question must be at least 5 characters long.";
            }
        } else if (input.name.includes("answers")) {
            let answers = value.split(",");
            if (answers.length < 2) {
                errorMessage = "There should be at least two answers.";
            }
        } else if (input.name.includes("correct")) {
            if (value.trim() === "") {
                errorMessage = "Correct answer is required.";
            }
        }

        showError(input, errorMessage);
        return errorMessage === "";
    }

    // Display or clear an error message next to an input field
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
