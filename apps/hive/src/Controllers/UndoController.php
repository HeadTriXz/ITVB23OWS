<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Services\UndoService;
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
     * @param UndoService $service The service for undoing the last move.
     */
    public function __construct(protected Session $session, protected UndoService $service)
    {
    }

    /**
     * Handle a POST request.
     */
    public function handlePost(): void
    {
        $game = $this->session->get('game');

        $this->service->undo($game);

        App::redirect();
    }
}
