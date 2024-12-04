<?php

use Hive\Core\Game;

// Avoid IDE warnings about undefined variables.

/** @var array $boardData */
/** @var string $error */
/** @var Game $game */
/** @var string $gameEndMessage */
/** @var array $movableTilesMap */
/** @var array $movesHistory */
/** @var array $placePositions */
/** @var array $placeableTiles */
/** @var string $turn */
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hive</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="board">
    <?php foreach ($boardData as $tile): ?>
        <div class="tile <?= $tile['class'] ?>"
             style="left: <?= $tile['style']['left'] ?>; top: <?= $tile['style']['top'] ?>;">
            <?= $tile['label'] ?><span><?= $tile['type'] ?></span>
        </div>
    <?php endforeach; ?>
</div>
<div class="hand">
    White:
    <?php
    foreach ($game->hand[0] as $tile => $count) {
        for ($i = 0; $i < $count; $i++) {
            echo '<div class="tile player0"><span>' . $tile . '</span></div>';
        }
    }
    ?>
</div>
<div class="hand">
    Black:
    <?php
    foreach ($game->hand[1] as $tile => $count) {
        for ($i = 0; $i < $count; $i++) {
            echo '<div class="tile player1"><span>' . $tile . '</span></div>';
        }
    }
    ?>
</div>
<div class="turn">Turn: <?= $turn ?></div>
<form method="post" action="/play">
    <select name="piece">
        <?php foreach ($placeableTiles as $tile): ?>
            <option value="<?= $tile ?>"><?= $tile ?></option>
        <?php endforeach; ?>
    </select>
    <select name="to">
        <?php foreach ($placePositions as $pos): ?>
            <option value="<?= $pos ?>"><?= $pos ?></option>
        <?php endforeach; ?>
    </select>
    <input type="submit" value="Play" <?= $game->hasEnded() ? 'disabled' : '' ?>>
</form>
<form method="post" action="/move">
    <select name="from" id="from" onchange="updateMoveTo()">
        <?php foreach ($movableTilesMap as $fromPos => $toPositions): ?>
            <option value="<?= $fromPos ?>"><?= $fromPos ?></option>
        <?php endforeach; ?>
    </select>
    <select name="to" id="to"></select>
    <input type="submit" value="Move" <?= $game->hasEnded() ? 'disabled' : '' ?>>
</form>
<div class="actions">
    <form method="post" action="/ai">
        <input type="submit" value="Use AI" <?= $game->hasEnded() ? 'disabled' : '' ?>>
    </form>
    <form method="post" action="/pass">
        <input type="submit" value="Pass" <?= $game->hasEnded() ? 'disabled' : '' ?>>
    </form>
    <form method="get" action="/restart">
        <input type="submit" value="Restart">
    </form>
</div>
<strong><?= $error ?></strong>
<strong><?= $gameEndMessage ?></strong>
<ol>
    <?php foreach ($movesHistory as $move): ?>
        <li><?= $move['type'] ?> <?= $move['move_from'] ?> <?= $move['move_to'] ?></li>
    <?php endforeach; ?>
</ol>
<form method="post" action="/undo">
    <input type="submit" value="Undo" <?= $game->hasEnded() ? 'disabled' : '' ?>>
</form>
<div id="gameEndModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="gameEndMessage"></p>
        <form method="get" action="/restart">
            <input type="submit" value="Restart">
        </form>
    </div>
</div>
<script>
    const validMoves = <?= json_encode($movableTilesMap) ?>;

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
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('gameEndModal');
        const closeButton = document.getElementsByClassName('close')[0];
        const gameEndMessage = document.getElementById('gameEndMessage');

        if (<?= json_encode($game->hasEnded()) ?>) {
            gameEndMessage.textContent = <?= json_encode($gameEndMessage) ?>;
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
</script>
</body>
</html>
