const validMoves = window.gameData.movableTiles;

function updateMoveTo() {
    const fromSelect = document.getElementById('from');
    const toSelect = document.getElementById('to');
    const selectedFrom = fromSelect.value;

    // Clear the existing options in the "to" dropdown
    toSelect.innerHTML = '';

    // Populate the "to" dropdown with valid move positions for the selected "from" position
    if (validMoves[selectedFrom] !== undefined) {
        validMoves[selectedFrom].forEach((pos) => {
            const option = document.createElement('option');
            option.value = pos;
            option.textContent = pos;
            toSelect.appendChild(option);
        });
    }
}

// Initialize the "to" dropdown on page load
document.addEventListener('DOMContentLoaded', updateMoveTo);
