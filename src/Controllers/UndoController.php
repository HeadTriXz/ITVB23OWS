<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Core\Game;
use Hive\Database;
use Hive\Session;

/**
 * A controller for undoing the last move.
 */
class UndoController
{
    /**
     * A controller for undoing the last move.
     *
     * @param Session $session The session instance.
     * @param Database $database The database instance.
     */
    public function __construct(protected Session $session, protected Database $database)
    {
    }

    /**
     * Handle a POST request.
     */
    public function handlePost(): void
    {
        $lastMove = $this->session->get('last_move') ?? 0;
        $result = $this->database
            ->query("SELECT previous_id, state FROM moves WHERE id = $lastMove")
            ->fetch_array();

        $this->session->set('last_move', $result[0]);
        $this->session->set('game', Game::fromString($result[1]));

        App::redirect();
    }
}
