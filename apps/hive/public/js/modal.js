// Handle the modal for when the game ends
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('gameEndModal');
    const closeButton = document.getElementsByClassName('close')[0];
    const gameEndMessage = document.getElementById('gameEndMessage');

    if (window.gameData.gameEnded) {
        gameEndMessage.textContent = window.gameData.gameEndMessage;
        modal.style.display = 'block';
    }

    closeButton.onclick = () => {
        modal.style.display = 'none';
    }

    // When the user clicks anywhere outside the modal, close it
    window.onclick = (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
});
