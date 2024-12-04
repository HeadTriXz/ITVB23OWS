<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Services\PlayService;
use Hive\Session;
use Hive\Validators\PlayValidator;

/**
 * A controller for placing new tiles on the board.
 */
class PlayController
{
    /**
     * A controller for placing new tiles on the board.
     *
     * @param Session $session The session instance.
     */
    public function __construct(
        protected Session $session,
        protected PlayValidator $validator,
        protected PlayService $service
    ) {
    }

    /**
     * Handle a POST request.
     *
     * @param string $piece The piece to play.
     * @param string $to The position to play the piece to.
     */
    public function handlePost(string $piece, string $to): void
    {
        $game = $this->session->get('game');
        $error = $this->validator->validate($game, $piece, $to);

        if ($error) {
            $this->session->set('error', $error);
        } else {
            $this->service->play($game, $piece, $to);
        }

        App::redirect();
    }
}
