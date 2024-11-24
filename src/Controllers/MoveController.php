<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Database;
use Hive\Session;
use Hive\Tiles\TileType;
use Hive\Util;

/**
 * A controller for moving tiles on the board.
 */
class MoveController
{
    /**
     * A controller for moving tiles on the board.
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
     * @param string $from The position to move the tile from.
     * @param string $to The position to move the tile to.
     */
    public function handlePost(string $from, string $to): void
    {
        $game = $this->session->get('game');
        $hand = $game->hand[$game->player];

        if (!$game->board->hasTile($from)) {
            // cannot move tile from empty position
            $this->session->set('error', 'Board position is empty');
        } elseif ($game->board->getTiles($from)[0]->getPlayer() != $game->player) {
            // can only move top of stack and only if owned by current player
            $this->session->set('error', 'Tile is not owned by player');
        } elseif ($hand['Q']) {
            // cannot move unless queen bee has previously been played
            $this->session->set('error', 'Queen bee is not played');
        } elseif ($from === $to) {
            // a tile cannot return to its original position
            $this->session->set('error', 'Tile must move to a different position');
        } else {
            // temporarily remove tile from board
            $tile = $game->board->removeTile($from);
            if (!Util::hasNeighbour($to, $game->board)) {
                // target position is not connected to hive so move is invalid
                $this->session->set('error', 'Move would split hive');
            } elseif (Util::hasMultipleHives($game->board)) {
                // the move would split the hive in two so it is invalid
                $this->session->set('error', 'Move would split hive');
            } elseif ($game->board->hasTile($to) && $tile->getType() != TileType::Beetle) {
                // only beetles are allowed to stack on top of other tiles
                $this->session->set('error', 'Tile not empty');
            } elseif ($tile->getType() == TileType::QueenBee || $tile->getType() == TileType::Beetle) {
                // queen bees and beetles must move a single hex using the sliding rules
                if (!Util::isValidSlide($game->board, $from, $to)) {
                    $this->session->set('error', 'Tile must slide');
                }
            }
            // TODO: rules for other tiles aren't implemented yet
            if ($this->session->get('error')) {
                // illegal move so reset tile that was temporarily removed
                $game->board->addTile($from, $tile);
            } else {
                // move tile to new position and switch players
                $game->board->addTile($to, $tile);
                $game->player = 1 - $game->player;

                // store move in database
                $last = $this->session->get('last_move') ?? 'null';
                $state = $this->database->escape($game);
                $from = $this->database->escape($from);
                $to = $this->database->escape($to);

                $this->database->execute("
                    INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state)
                    VALUES ({$this->session->get('game_id')}, \"move\", \"$from\", \"$to\", $last, \"$state\")
                ");
                $this->session->set('last_move', $this->database->getInsertId());
            }
        }

        App::redirect();
    }
}
