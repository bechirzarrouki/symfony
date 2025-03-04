function sortProjects(order) {
    let projectsContainer = document.querySelector(".post-container");
    let projects = Array.from(document.querySelectorAll(".investment-box"));

    projects.sort((a, b) => {
        let budgetA = parseFloat(a.querySelector("p strong + p").textContent.trim().replace("€", "").replace(",", ""));
        let budgetB = parseFloat(b.querySelector("p strong + p").textContent.trim().replace("€", "").replace(",", ""));

        return order === "asc" ? budgetA - budgetB : budgetB - budgetA;
    });

    projects.forEach(project => projectsContainer.appendChild(project));
}

// Attach event listeners to buttons
document.getElementById("sort-asc").addEventListener("click", function() {
    sortProjects("asc");
});

document.getElementById("sort-desc").addEventListener("click", function() {
    sortProjects("desc");
});