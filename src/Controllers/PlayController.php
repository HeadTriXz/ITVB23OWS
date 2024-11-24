<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Database;
use Hive\Session;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;
use Hive\Util;

/**
 * A controller for placing new tiles on the board.
 */
class PlayController
{
    /**
     * A controller for placing new tiles on the board.
     *
     * @param Session $session The session instance.
     * @param Database $database The database instance.
     */
    public function __construct(protected Session $session, protected Database $database)
    {
    }

    /**
     * Handle a POST request.
     *
     * @param string $piece The piece to play.
     * @param string $to The position to play the piece to.
     */
    public function handlePost(string $piece, string $to): void
    {
        $game = $this->session->get('game');
        $hand = $game->hand[$game->player];

        if (!$hand[$piece]) {
            // must still have tile in hand to be able to play it
            $this->session->set('error', 'Player does not have tile');
        } elseif ($game->board->hasTile($to)) {
            // can only play on empty positions (even beetles)
            $this->session->set('error', 'Board position is not empty');
        } elseif (!$game->board->isEmpty() && !Util::hasNeighbour($to, $game->board)) {
            // every tile except the very first one of the game must be played adjacent to the hive
            $this->session->set('error', 'board position has no neighbour');
        } elseif (array_sum($hand) < 11 && !Util::neighboursAreSameColor($game->player, $to, $game->board)) {
            // every tile after the first one a player plays may not be adjacent to enemy tiles
            $this->session->set('error', 'Board position has opposing neighbour');
        } elseif (Util::mustPlayQueen($hand) && $piece != 'Q') {
            // must play the queen bee in one of the first four turns
            $this->session->set('error', 'Must play queen bee');
        } else {
            // add the new tile to the board, remove it from its owners hand and switch players
            $tile = Tile::from(TileType::from($piece), $game->player);
            $game->board->addTile($to, $tile);

            $game->hand[$game->player][$piece]--;
            $game->player = 1 - $game->player;

            // store move in database
            $last = $this->session->get('last_move') ?? 'null';
            $state = $this->database->escape($game);
            $piece = $this->database->escape($piece);
            $to = $this->database->escape($to);

            $this->database->execute("
                INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state)
                VALUES ({$this->session->get('game_id')}, \"play\", \"$piece\", \"$to\", $last, \"$state\")
            ");
            $this->session->set('last_move', $this->database->getInsertId());
        }

        // redirect back to index
        App::redirect();
    }
}
