<?php

namespace Hive\Services;

use Hive\Core\Game;
use Hive\Repositories\MoveRepository;
use Hive\Session;

/**
 * Represents the service for passing the turn.
 */
class PassService
{
    /**
     * Represents the service for passing the turn.
     *
     * @param Session $session The session instance.
     * @param MoveRepository $moves The moves repository.
     */
    public function __construct(protected Session $session, protected MoveRepository $moves)
    {
    }

    /**
     * Passes the turn to the other player.
     *
     * @param Game $game The current game state.
     */
    public function pass(Game $game): void
    {
        if ($game->hasEnded()) {
            return;
        }

        $game->player = 1 - $game->player;

        // Save the game state.
        $id = $this->moves->create('pass');
        $this->session->set('last_move', $id);
    }
}
