<?php

namespace Hive;

// restart the game
class RestartController {
    public function handleGet() {
        // create new game
        $session = Session::inst();
        $session->set('game', new Game());

        // get new game id from database
        $db = Database::inst();
        $db->Execute('INSERT INTO games VALUES ()');
        $session->set('game_id', $db->Get_Insert_Id());

        // redirect back to index
        App::redirect();
    }
}
