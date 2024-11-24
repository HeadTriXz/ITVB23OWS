<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Core\Game;
use Hive\Database;
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
     * @param Database $database The database instance.
     */
    public function __construct(protected Session $session, protected Database $database)
    {
    }

    /**
     * Handle a GET request.
     */
    public function handleGet(): void
    {
        $this->session->set('game', new Game());

        $this->database->execute('INSERT INTO games VALUES ()');
        $this->session->set('game_id', $this->database->getInsertId());

        App::redirect();
    }
}
