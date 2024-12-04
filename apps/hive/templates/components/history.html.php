<?php
/**
 * @var array $movesHistory
 * @var Hive\Core\Game $game
 */
?>

<div class="move-history">
    <ol>
        <?php foreach ($movesHistory as $move): ?>
            <li><?= $move['type'] ?> <?= $move['move_from'] ?> <?= $move['move_to'] ?></li>
        <?php endforeach; ?>
    </ol>
</div>

<form method="post" action="/undo">
    <input type="submit" value="Undo" <?= $game->hasEnded() ? 'disabled' : '' ?>>
</form>
