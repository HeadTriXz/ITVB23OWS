<?php

namespace Hive;

// move an existing tile
class MoveController
{
    public function handlePost(string $from, string $to)
    {
        // get state from session
        $session = Session::inst();
        $game = $session->get('game');
        $hand = $game->hand[$game->player];

        if (!isset($game->board[$from])) {
            // cannot move tile from empty position
            $session->set('error', 'Board position is empty');
        } elseif ($game->board[$from][count($game->board[$from])-1][0] != $game->player)
            // can only move top of stack and only if owned by current player
            $session->set("error", "Tile is not owned by player");
        elseif ($hand['Q'])
            // cannot move unless queen bee has previously been played
            $session->set('error', "Queen bee is not played");
        elseif ($from === $to) {
            // a tile cannot return to its original position
            $session->set('error', 'Tile must move to a different position');
        } else {
            // temporarily remove tile from board
            $tile = array_pop($game->board[$from]);
            if (!Util::has_NeighBour($to, $game->board))
                // target position is not connected to hive so move is invalid
                $session->set("error", "Move would split hive");
            elseif (Util::hasMultipleHives($game->board)) {
                // the move would split the hive in two so it is invalid
                $session->set("error", "Move would split hive");
            } elseif (isset($game->board[$to]) && $tile[1] != "B") {
                // only beetles are allowed to stack on top of other tiles
                $session->set("error", 'Tile not empty');
            } elseif ($tile[1] == "Q" || $tile[1] == "B") {
                // queen bees and beetles must move a single hex using the sliding rules
                if (!Util::slide($game->board, $from, $to))
                    $session->set("error", 'Tile must slide');
            }
            // TODO: rules for other tiles aren't implemented yet
            if ($session->get('error')) {
                // illegal move so reset tile that was temporarily removed
                if (isset($game->board[$from])) array_push($game->board[$from], $tile);
                else $game->board[$from] = [$tile];
            } else {
                // move tile to new position and switch players
                if (isset($game->board[$to])) array_push($game->board[$to], $tile);
                else $game->board[$to] = [$tile];
                $game->player = 1 - $game->player;

                // store move in database
                $db = Database::inst();
                $state = $db->Escape($game);
                $last = $session->get('last_move') ?? 'null';
                $db->Execute("
                    insert into moves (game_id, type, move_from, move_to, previous_id, state)
                    values ({$session->get('game_id')}, \"move\", \"$from\", \"$to\", $last, \"$state\")
                ");
                $session->set('last_move', $db->Get_Insert_Id());
            }
        }

        // redirect back to index
        App::redirect();
    }
}
