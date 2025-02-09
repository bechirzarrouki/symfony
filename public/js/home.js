// Toggle reaction menu visibility
const likeButton = document.querySelector('.like-btn');
const reactionMenu = document.querySelector('.reaction-menu');

likeButton.addEventListener('click', () => {
    reactionMenu.style.display = reactionMenu.style.display === 'flex' ? 'none' : 'flex';
});

// Toggle comment section visibility
const commentButton = document.querySelector('.comment-btn');
const commentSection = document.querySelector('.comment-section');

commentButton.addEventListener('click', () => {
    commentSection.style.display = commentSection.style.display === 'block' ? 'none' : 'block';
});
