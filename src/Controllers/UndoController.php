<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Core\Game;
use Hive\Repositories\MoveRepository;
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
        $lastMove = $this->session->get('last_move') ?? 0;
        $result = $this->moves->find($lastMove);

        $this->session->set('last_move', $result['previous_id']);
        $this->session->set('game', Game::fromString($result['state']));

        App::redirect();
    }
}
