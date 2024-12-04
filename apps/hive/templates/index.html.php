<?php
/**
 * @var string $error
 * @var Hive\Core\Game $game
 * @var string $gameEndMessage
 * @var array $movableTilesMap
 * @var string $turn
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Hive</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>

<?php include 'components/board.html.php' ?>
<?php $player = 0; include 'components/hand.html.php' ?>
<?php $player = 1; include 'components/hand.html.php' ?>

<div class="turn">Turn: <?= $turn ?></div>

<?php include 'components/forms/play.html.php' ?>
<?php include 'components/forms/move.html.php' ?>
<?php include 'components/forms/actions.html.php' ?>

<strong><?= $error ?></strong>
<strong><?= $gameEndMessage ?></strong>

<?php include 'components/history.html.php' ?>
<?php include 'components/modal.html.php' ?>

<script>
    window.gameData = <?= json_encode([
        'gameEnded' => $game->hasEnded(),
        'gameEndMessage' => $gameEndMessage,
        'movableTiles' => $movableTilesMap
    ]) ?>;
</script>
<script src="/js/modal.js"></script>
<script src="/js/moves.js"></script>
</body>
</html>
