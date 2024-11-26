<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Services\MoveService;
use Hive\Session;
use Hive\Validators\MoveValidator;

/**
 * A controller for moving tiles on the board.
 */
class MoveController
{
    /**
     * A controller for moving tiles on the board.
     *
     * @param Session $session The session instance.
     * @param MoveValidator $validator The move validator.
     * @param MoveService $service The move service.
     */
    public function __construct(
        protected Session $session,
        protected MoveValidator $validator,
        protected MoveService $service
    ) {
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
        $error = $this->validator->validate($game, $from, $to);
        if ($error) {
            $this->session->set('error', $error);
            return;
        }

        $this->service->move($game, $from, $to);
        App::redirect();
    }
}
