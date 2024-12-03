<?php

namespace Hive\Services;

use Hive\Core\Game;
use Hive\Repositories\MoveRepository;
use Hive\Session;

/**
 * Represents the service for letting the AI play.
 */
class AIService
{
    /**
     * Represents the service for letting the AI play.
     *
     * @param Session $session The session instance.
     * @param MoveRepository $moves The moves repository.
     * @param MoveService $moveService The service for moving a tile.
     * @param PassService $passService The service for passing the turn.
     * @param PlayService $playService The service for playing a tile.
     */
    public function __construct(
        protected Session $session,
        protected MoveRepository $moves,
        protected MoveService $moveService,
        protected PassService $passService,
        protected PlayService $playService
    ) {
    }

    /**
     * Let the AI play a move.
     *
     * @param Game $game The current game state.
     */
    public function play(Game $game): void
    {
        if ($game->hasEnded()) {
            return;
        }

        $move = $this->fetchMove($game);
        if ($move === null) {
            return;
        }

        $type = $move[0];
        $from = $move[1];
        $to = $move[2];

        switch ($type) {
            case 'play':
                $this->playService->play($game, $from, $to);
                break;
            case 'move':
                $this->moveService->move($game, $from, $to);
                break;
            case 'pass':
                $this->passService->pass($game);
                break;
        }
    }

    /**
     * Fetch a move from the AI.
     *
     * @param Game $game The current game state.
     * @return ?array The move from the AI, or null if an error occurred.
     */
    public function fetchMove(Game $game): ?array
    {
        $gameId = $this->session->get('game_id');
        $moveNumber = $this->moves->count($gameId);

        $context = stream_context_create([
            'http' => [
                'content' => json_encode([
                    'board' => $game->board->toJSON(),
                    'hand' => $game->hand,
                    'move_number' => $moveNumber
                ]),
                'header' => 'Content-Type: application/json',
                'method' => 'POST'
            ]
        ]);

        /** @noinspection PhpVulnerablePathsInspection - AI_URL is set in .env */
        $result = @file_get_contents($_ENV['AI_URL'], false, $context);
        if ($result === false) {
            $this->session->set('error', 'Could not fetch move from AI');
            return null;
        }

        return json_decode($result, true);
    }
}
