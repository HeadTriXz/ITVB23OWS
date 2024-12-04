<?php
/**
 * @var array $placeableTiles
 * @var array $placePositions
 * @var Hive\Core\Game $game
 */

$disable = $game->hasEnded() || empty($placeableTiles) || empty($placePositions);
?>

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
    <input type="submit" value="Play" <?= $disable ? 'disabled' : '' ?>>
</form>
