<!DOCTYPE html>
<html>
<head>
    <title>Hive</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="board">
    <?php

    use Hive\Util;

    $width = 35;
    $height = 30;

    // find minimum values for q and r to render board
    $min_q = 1000;
    $min_r = 1000;
    foreach ($game->board->toArray() as $pos => $stack) {
        $qr = Util::parsePosition($pos);
        if ($qr[0] < $min_q) {
            $min_q = $qr[0];
        }

        if ($qr[1] < $min_r) {
            $min_r = $qr[1];
        }
    }

    // reduce minimum values for q and r to make room for empty spaces adjacent to tiles
    $min_q--;
    $min_r--;

    // store rendered tiles so they can later be rendered in the proper order
    $rendered_tiles = [];

    // render tiles in play
    foreach ($game->board->toArray() as $pos => $stack) {
        $qr = Util::parsePosition($pos);
        $str = '<div class="tile player';
        $str .= $stack[0]->getPlayer();
        if (count($stack) > 1) {
            $str .= ' stacked';
        }

        $str .= '" style="left: ';
        $str .= $width * (($qr[0] - $min_q) + ($qr[1] - $min_r) / 2);
        $str .= 'px; top: ';
        $str .= $height * ($qr[1] - $min_r);
        $str .= "px;\">$qr[0],$qr[1]<span>";
        $str .= $stack[0]->getType()->value;
        $str .= '</span></div>';
        $rendered_tiles[$pos] = $str;
    }

    // render empty tiles adjacent to existing tiles
    foreach ($to as $pos) {
        if (!$game->board->hasTile($pos)) {
            $qr = Util::parsePosition($pos);
            $str = '<div class="tile empty" style="left: ';
            $str .= $width * (($qr[0] - $min_q) + ($qr[1] - $min_r) / 2);
            $str .= 'px; top: ';
            $str .= $height * ($qr[1] - $min_r);
            $str .= "px;\">$qr[0],$qr[1]<span>";
            $str .= "&nbsp;";
            $str .= '</span></div>';
            $rendered_tiles[$pos] = $str;
        }
    }

    // sort in display order
    uksort($rendered_tiles, function ($a, $b) {
        // split coordinates
        $a = Util::parsePosition($a);
        $b = Util::parsePosition($b);

        // compare second (vertical) coordinate first
        return $a[1] == $b[1]
            ? $a[0] <=> $b[0]
            : $a[1] <=> $b[1];
    });

    // display tiles
    foreach ($rendered_tiles as $str) {
        echo($str);
    }
    ?>
</div>
<div class="hand">
    White:
    <?php
    // render tiles in white's hand
    foreach ($game->hand[0] as $tile => $ct) {
        for ($i = 0; $i < $ct; $i++) {
            echo '<div class="tile player0"><span>' . $tile . "</span></div>";
        }
    }
    ?>
</div>
<div class="hand">
    Black:
    <?php
    // render tiles in black's hand
    foreach ($game->hand[1] as $tile => $ct) {
        for ($i = 0; $i < $ct; $i++) {
            echo '<div class="tile player1"><span>' . $tile . "</span></div>";
        }
    }
    ?>
</div>
<div class="turn">
    Turn: <?php
        // render active player
        if ($game->player == 0) echo "White"; else echo "Black";
    ?>
</div>
<form method="post" action="/play">
    <select name="piece">
        <?php
        // render list of tile types
        foreach ($game->getPlaceableTiles($game->player) as $tile) {
            echo "<option value=\"$tile\">$tile</option>";
        }
        ?>
    </select>
    <select name="to">
        <?php
        // render list of possible moves
        foreach ($game->getValidPlacePositions($game->player) as $pos) {
            echo "<option value=\"$pos\">$pos</option>";
        }
        ?>
    </select>
    <input type="submit" value="Play">
</form>
<form method="post" action="/move">
    <select name="from" id="from" onchange="updateMoveTo()">
        <?php
        // render list of positions in board
        foreach ($game->getMovableTiles($game->player) as $pos) {
            echo "<option value=\"$pos\">$pos</option>";
        }
        ?>
    </select>
    <select name="to" id="to"></select>
    <input type="submit" value="Move">
</form>
<form method="post" action="/pass">
    <input type="submit" value="Pass">
</form>
<form method="get" action="/restart">
    <input type="submit" value="Restart">
</form>
<strong><?php
    // render error message
    if (isset($_SESSION['error'])) {
        echo($_SESSION['error']);
    }

    unset($_SESSION['error']);
    ?></strong>
<ol>
    <?php
    // render list of moves
    $db = \Hive\Database::inst();
    $session = \Hive\Session::inst();
    $result = $db->Query("SELECT * FROM moves WHERE game_id = {$session->get('game_id')}");
    while ($row = $result->fetch_array()) {
        echo '<li>' . $row[2] . ' ' . $row[3] . ' ' . $row[4] . '</li>';
    }
    ?>
</ol>
<form method="post" action="/undo">
    <input type="submit" value="Undo">
</form>
<script>
    // Preload valid move positions for each "from" position
    const validMoves = <?php
        $moves = [];
        foreach ($game->getMovableTiles($game->player) as $fromPos) {
            $tile = $game->board->getTiles($fromPos)[0];
            $moves[$fromPos] = $tile->getValidMoves($game->board, $fromPos);
        }

        echo json_encode($moves);
    ?>;

    // Function to update the "to" dropdown based on the selected "from" position
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
</body>
</html>

