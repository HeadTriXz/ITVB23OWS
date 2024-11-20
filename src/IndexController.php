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
        $to = [];
        $hand = $game->hand[$game->player];
        foreach (Util::OFFSETS as $qr) {
            foreach (array_keys($game->board) as $pos) {
                [$x, $y] = explode(',', $pos);
                $to[] = ($qr[0] + $x).','.($qr[1] + $y);
            }
        }

        $to = array_unique($to);
        if (!count($to)) {
            $to[] = '0,0';
        }

        // render view
        require_once TEMPLATE_DIR.'/index.html.php';
    }
}