<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Repositories\MoveRepository;
use Hive\Session;
use Hive\Validators\PassValidator;

/**
 * A controller for passing the turn.
 */
class PassController
{
    /**
     * A controller for passing the turn.
     *
     * @param Session $session The session instance.
     * @param PassValidator $validator The validator for passing the turn.
     * @param MoveRepository $moves The repository for the 'moves' table.
     */
    public function __construct(
        protected Session $session,
        protected PassValidator $validator,
        protected MoveRepository $moves
    ) {
    }

    /**
     * Handle a POST request.
     */
    public function handlePost(): void
    {
        $game = $this->session->get('game');
        $error = $this->validator->validate($game);

        if ($error) {
            $this->session->set('error', $error);
        } else {
            $game->player = 1 - $game->player;

            // Save the game state.
            $id = $this->moves->create('pass');
            $this->session->set('last_move', $id);
        }

        App::redirect();
    }
}
