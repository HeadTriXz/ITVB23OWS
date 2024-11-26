<?php

namespace Hive\Services;

use Hive\Core\Game;
use Hive\Repositories\MoveRepository;
use Hive\Session;

/**
 * Represents the service for moving a tile.
 */
class MoveService
{
    /**
     * Represents the service for playing a tile.
     *
     * @param Session $session The session instance.
     * @param MoveRepository $moves The moves repository.
     */
    public function __construct(protected Session $session, protected MoveRepository $moves)
    {
    }

    /**
     * Moves a tile on the board.
     *
     * @param Game $game The current game state.
     * @param string $from The current position of the tile.
     * @param string $to The new position of the tile.
     */
    public function move(Game $game, string $from, string $to): void
    {
        // Move the tile on the board.
        $tile = $game->board->removeTile($from);
        $game->board->addTile($to, $tile);

        // Switch players.
        $game->player = 1 - $game->player;

        // Save the game state.
        $moveId = $this->moves->create('move', $from, $to);
        $this->session->set('last_move', $moveId);
    }
}
