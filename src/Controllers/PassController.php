<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Database;
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
        // TODO: pass is not implemented yet

        $game = $this->session->get('game');
        $game->player = 1 - $game->player;

        $state = $this->database->escape($game);
        $last = $this->session->get('last_move') ?? 'null';
        $this->database->query("
            INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state)
            VALUES ({$this->session->get('game_id')}, \"pass\", null, null, $last, \"$state\")
        ");
        $this->session->set('last_move', $this->database->getInsertId());

        App::redirect();
    }
}
