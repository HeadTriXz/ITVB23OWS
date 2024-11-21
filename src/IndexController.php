<?php

namespace Hive;

/**
 * Handle index page.
 */
class IndexController
{
    public function handleGet() {
        $session = Session::inst();

        // ensure session contains a game
        $game = $session->get('game');
        if (!$game) {
            App::redirect('/restart');
            return;
        }

        // find all positions that are adjacent to one of the tiles in the hive as candidates for a new tile
        $to = $game->getAdjacentPositions();

        // render view
        require_once TEMPLATE_DIR.'/index.html.php';
    }
}
