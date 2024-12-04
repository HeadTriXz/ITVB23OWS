<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Core\Game;
use Hive\Repositories\GameRepository;
use Hive\Session;

/**
 * A controller for restarting the game.
 */
class RestartController
{
    /**
     * A controller for restarting the game.
     *
     * @param Session $session The session instance.
     * @param GameRepository $games The repository for the 'games' table.
     */
    public function __construct(protected Session $session, protected GameRepository $games)
    {
    }

    /**
     * Handle a GET request.
     */
    public function handleGet(): void
    {
        $id = $this->games->create();
        $this->session->set('game_id', $id);
        $this->session->set('game', new Game());

        App::redirect();
    }
}
