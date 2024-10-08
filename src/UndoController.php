<?php

namespace Hive;

// undo last move
class UndoController
{
    public function handlePost() {
        $session = Session::inst();
        $db = Database::inst();

        // restore last move from database
        $last_move = $session->get('last_move') ?? 0;
        $result = $db->Query("SELECT previous_id, state FROM moves WHERE id = {$last_move}")->fetch_array();
        $session->set('last_move', $result[0]);
        $session->set('game', Game::fromString($result[1]));

        // redirect back to index
        App::redirect();
    }
}
