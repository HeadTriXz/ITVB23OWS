<?php

namespace Hive;

use Hive\Controllers\AIController;
use Hive\Controllers\IndexController;
use Hive\Controllers\MoveController;
use Hive\Controllers\PassController;
use Hive\Controllers\PlayController;
use Hive\Controllers\RestartController;
use Hive\Controllers\UndoController;
use Hive\Repositories\GameRepository;
use Hive\Repositories\MoveRepository;
use Hive\Services\AIService;
use Hive\Services\MoveService;
use Hive\Services\PassService;
use Hive\Services\PlayService;
use Hive\Services\UndoService;
use Hive\Validators\MoveValidator;
use Hive\Validators\PassValidator;
use Hive\Validators\PlayValidator;

/**
 * The main application.
 */
class App
{
    /**
     * The database instance.
     *
     * @var Database
     */
    protected Database $database;

    /**
     * The session instance.
     *
     * @var Session
     */
    protected Session $session;

    /**
     * The repository for the 'games' table.
     *
     * @var GameRepository
     */
    protected GameRepository $gameRepository;

    /**
     * The repository for the 'moves' table.
     *
     * @var MoveRepository
     */
    protected MoveRepository $moveRepository;

    /**
     * The main application.
     *
     * @param Session $session The session instance.
     * @param Database $database The database instance.
     */
    public function __construct(Session $session, Database $database)
    {
        $this->session = $session;
        $this->database = $database;

        $this->gameRepository = new GameRepository($database);
        $this->moveRepository = new MoveRepository($session, $database);
    }

    /**
     * Handle an incoming request.
     */
    public function handle(): void
    {
        // Get current route
        $path = explode('/', $_SERVER['PATH_INFO'] ?? '');
        $route = $path[1] ?? 'index';

        // Initialize services
        $moveService = new MoveService($this->session, $this->moveRepository);
        $passService = new PassService($this->session, $this->moveRepository);
        $playService = new PlayService($this->session, $this->moveRepository);
        $undoService = new UndoService($this->session, $this->moveRepository);
        $aiService = new AIService($this->session, $this->moveRepository, $moveService, $passService, $playService);

        // Find corresponding controller
        $controller = match ($route) {
            'ai' => new AIController($this->session, $aiService),
            'index' => new IndexController($this->session, $this->moveRepository),
            'move' => new MoveController($this->session, new MoveValidator(), $moveService),
            'pass' => new PassController($this->session, new PassValidator(), $passService),
            'play' => new PlayController($this->session, new PlayValidator(), $playService),
            'restart' => new RestartController($this->session, $this->gameRepository),
            'undo' => new UndoController($this->session, $undoService)
        };

        // Dispatch GET or POST request
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') == 'GET') {
            /** @noinspection PhpMethodParametersCountMismatchInspection - $_GET is currently not used */
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
