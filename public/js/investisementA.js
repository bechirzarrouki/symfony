
    function toggleInvestmentDetails(element) {
        console.log(element);
        const details = element.querySelector('.investment-details');
        if (details) {
            details.style.display = details.style.display === 'none' ? 'block' : 'none';
        }
    }

