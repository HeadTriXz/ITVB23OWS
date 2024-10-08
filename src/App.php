<?php

namespace Hive;

class App {
    public function handle(): void {
        // get current route
        $path = explode('/', $_SERVER['PATH_INFO'] ?? '');
        $route = $path[1] ?? 'index';

        // find corresponding controller
        $controller = match ($route) {
            'index' => new IndexController(),
            'move' => new MoveController(),
            'pass' => new PassController(),
            'play' => new PlayController(),
            'restart' => new RestartController(),
            'undo' => new UndoController(),
        };

        // dispatch get or post request
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') == 'GET') {
            $controller->handleGet(...$_GET);
        } else {
            $controller->handlePost(...$_POST);
        }
    }

    // redirect to given url
    public static function redirect(string $url = '/') {
        header("Location: $url");
    }
}
