<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Repositories\MoveRepository;
use Hive\Session;

/**
 * A controller for passing the turn.
 */
class PassController
{
    /**
     * A controller for passing the turn.
     *
     * @param Session $session The session instance.
     * @param MoveRepository $moves The repository for the 'moves' table.
     */
    public function __construct(protected Session $session, protected MoveRepository $moves)
    {
    }

    /**
     * Handle a POST request.
     */
    public function handlePost(): void
    {
        // TODO: pass is not implemented yet

        $game = $this->session->get('game');
        $game->player = 1 - $game->player;

        // Save the game state.
        $id = $this->moves->create('pass', null, null);
        $this->session->set('last_move', $id);

        App::redirect();
    }
}
