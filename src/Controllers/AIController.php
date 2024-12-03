<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Services\AIService;
use Hive\Session;

/**
 * A controller for the AI player.
 */
class AIController
{
    /**
     * A controller for the AI player.
     *
     * @param Session $session The session instance.
     * @param AIService $service The service for letting the AI play.
     */
    public function __construct(protected Session $session, protected AIService $service)
    {
    }

    /**
     * Handle a POST request.
     */
    public function handlePost(): void
    {
        $game = $this->session->get('game');

        $this->service->play($game);

        App::redirect();
    }
}
