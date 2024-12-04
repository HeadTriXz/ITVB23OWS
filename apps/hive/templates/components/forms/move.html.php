<?php
/**
 * @var array $movableTilesMap
 * @var Hive\Core\Game $game
 */

$disable = $game->hasEnded() || empty($movableTilesMap);
?>

<form method="post" action="/move">
    <select name="from" id="from" onchange="updateMoveTo()">
        <?php foreach ($movableTilesMap as $fromPos => $toPositions): ?>
            <option value="<?= $fromPos ?>"><?= $fromPos ?></option>
        <?php endforeach; ?>
    </select>
    <select name="to" id="to"></select>
    <input type="submit" value="Move" <?= $disable ? 'disabled' : '' ?>>
</form>
