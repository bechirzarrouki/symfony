
    function toggleInvestmentDetails(element) {
        const details = element.querySelector('.investment-details');
        if (details) {
            details.style.display = details.style.display === 'none' ? 'block' : 'none';
        }
    }

