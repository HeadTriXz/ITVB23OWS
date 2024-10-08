<?php

namespace Hive;

// pass, which should only be allowed if there are no other valid moves
class PassController {
    public function handlePost() {
        // get state from session
        $session = Session::inst();
        $game = $session->get('game');

        // TODO: pass is not implemented yet
        // switch players
        $game->player = 1 - $game->player;

        // store move in database
        $db = Database::inst();
        $state = $db->Escape($game);
        $last = $session->get('last_move') ?? 'null';
        $db->Query("
                insert into moves (game_id, type, move_from, move_to, previous_id, state)
                values ({$session->get('game_id')}, \"pass\", null, null, $last, \"$state\")
            ");
        $session->set('last_move', $db->Get_Insert_Id());

        // redirect back to index
        App::redirect();
    }
}