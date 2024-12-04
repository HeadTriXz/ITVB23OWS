<?php
/**
 * @var Hive\Core\Game $game
 * @var int $player
 */
?>

<div class="hand">
    <?php
    $players = ['White', 'Black'];
    echo $players[$player] . ': ';

    foreach ($game->hand[$player] as $tile => $count) {
        echo str_repeat('<div class="tile player' . $player . '"><span>' . $tile . '</span></div>', $count);
    }
    ?>
</div>
