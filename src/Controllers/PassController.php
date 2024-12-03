<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Services\PassService;
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
     * @param PassService $service The service for passing the turn.
     */
    public function __construct(
        protected Session $session,
        protected PassValidator $validator,
        protected PassService $service
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
            $this->service->pass($game);
        }

        App::redirect();
    }
}
