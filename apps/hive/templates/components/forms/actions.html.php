<?php
/**
 * @var Hive\Core\Game $game
 */
?>

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
