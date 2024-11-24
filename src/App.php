<?php

namespace Hive;

use Hive\Controllers\IndexController;
use Hive\Controllers\MoveController;
use Hive\Controllers\PassController;
use Hive\Controllers\PlayController;
use Hive\Controllers\RestartController;
use Hive\Controllers\UndoController;

/**
 * The main application.
 */
class App
{
    /**
     * The main application.
     *
     * @param Session $session The session instance.
     * @param Database $database The database instance.
     */
    public function __construct(protected Session $session, protected Database $database)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(): void
    {
        // Get current route
        $path = explode('/', $_SERVER['PATH_INFO'] ?? '');
        $route = $path[1] ?? 'index';

        // Find corresponding controller
        $controller = match ($route) {
            'index' => new IndexController($this->session, $this->database),
            'move' => new MoveController($this->session, $this->database),
            'pass' => new PassController($this->session, $this->database),
            'play' => new PlayController($this->session, $this->database),
            'restart' => new RestartController($this->session, $this->database),
            'undo' => new UndoController($this->session, $this->database),
        };

        // Dispatch GET or POST request
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') == 'GET') {
            // noinspection PhpMethodParametersCountMismatchInspection - $_GET is currently not used
            $controller->handleGet(...$_GET);
        } else {
            $controller->handlePost(...$_POST);
        }
    }

    /**
     * Redirect to a given URL.
     *
     * @param string $url The URL to redirect to.
     */
    public static function redirect(string $url = '/'): void
    {
        header("Location: $url");
    }
}
