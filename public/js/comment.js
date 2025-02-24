// Wait until the DOM content is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event listener to each comment options link (ellipsis)
            document.querySelectorAll('.comment-options-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Get the popup related to this comment
                    const popup = this.parentElement.querySelector('.comment-options-popup');
                    
                    // Hide any other open popups
                    document.querySelectorAll('.comment-options-popup').forEach(otherPopup => {
                        if (otherPopup !== popup) {
                            otherPopup.style.display = 'none';
                        }
                    });
                    
                    // Toggle the display of this popup
                    popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
                });
            });
            
            // Hide the popup when clicking outside of it
            document.addEventListener('click', function(e) {
                document.querySelectorAll('.comment-options-popup').forEach(popup => {
                    if (!popup.contains(e.target) && !e.target.classList.contains('comment-options-link')) {
                        popup.style.display = 'none';
                    }
                });
            });
        });
