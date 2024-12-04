<?php

namespace Hive\Services;

use Hive\Core\Game;
use Hive\Repositories\MoveRepository;
use Hive\Session;

/**
 * Represents the service for undoing the last move.
 */
class UndoService
{
    /**
     * Represents the service for undoing the last move.
     *
     * @param Session $session The session instance.
     * @param MoveRepository $moves The moves repository.
     */
    public function __construct(protected Session $session, protected MoveRepository $moves)
    {
    }

    /**
     * Undo the last move.
     *
     * @param Game $game The current game state.
     */
    public function undo(Game $game): void
    {
        if ($game->hasEnded()) {
            return;
        }

        $gameId = $this->session->get('game_id');
        $lastMoveId = $this->session->get('last_move') ?? 0;

        $lastMove = $this->moves->find($gameId, $lastMoveId);
        if ($lastMove === null) {
            $this->session->set('error', 'No moves to undo');
            return;
        }

        // Delete the last move
        $this->moves->delete($gameId, $lastMoveId);

        // Get the previous move
        $prevMove = $this->moves->find($gameId, (int)$lastMove['previous_id']);
        if ($prevMove === null) {
            $this->session->set('game', new Game());
            return;
        }

        // Set the game state to the previous move
        $this->session->set('last_move', $prevMove['id']);
        $this->session->set('game', Game::fromString($prevMove['state']));
    }
}
