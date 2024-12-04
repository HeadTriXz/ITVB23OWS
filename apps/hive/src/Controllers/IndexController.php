<?php

namespace Hive\Controllers;

use Hive\App;
use Hive\Core\Game;
use Hive\Core\GameStatus;
use Hive\Repositories\MoveRepository;
use Hive\Session;
use Hive\Util;

/**
 * A controller for the index page.
 */
class IndexController
{
    /**
     * A controller for the index page.
     *
     * @param Session $session The session instance.
     * @param MoveRepository $moves The repository for the 'moves' table.
     */
    public function __construct(protected Session $session, protected MoveRepository $moves)
    {
    }

    /**
     * Handle a GET request.
     */
    public function handleGet(): void
    {
        $game = $this->session->get('game');
        if (!$game) {
            App::redirect('/restart');
            return;
        }

        $boardData = $this->prepareBoardData($game);
        $turn = $game->player === 0 ? 'White' : 'Black';

        $placeableTiles = $game->getPlaceableTiles($game->player);
        $placePositions = $game->getValidPlacePositions($game->player);

        $movableTilesMap = $this->getMovableTilesMap($game);
        $movesHistory = $this->moves->findAll($this->session->get('game_id'));

        $gameEndMessage = $this->getGameEndMessage($game);
        $error = $this->session->get('error') ?? '';
        $this->session->delete('error');

        require_once TEMPLATE_DIR . '/index.html.php';
    }

    /**
     * Get the message to display when the game ends.
     *
     * @param Game $game The current game state.
     * @return string The message to display when the game ends.
     */
    public function getGameEndMessage(Game $game): string
    {
        return match ($game->status) {
            GameStatus::WHITE_WINS => 'White wins!',
            GameStatus::BLACK_WINS => 'Black wins!',
            GameStatus::DRAW => 'Draw!',
            default => '',
        };
    }

    /**
     * Maps the possible moves for each movable tile.
     *
     * @param Game $game The game instance.
     * @return array The possible moves for each movable tile.
     */
    public function getMovableTilesMap(Game $game): array
    {
        $map = [];
        foreach ($game->getMovableTiles($game->player) as $fromPos) {
            $tile = $game->board->getTiles($fromPos)[0];
            $map[$fromPos] = $tile->getValidMoves($game->board, $fromPos);
        }

        return $map;
    }

    /**
     * Prepare board data for rendering.
     */
    public function prepareBoardData($game): array
    {
        $width = 35;
        $height = 30;
        $minQ = 1000;
        $minR = 1000;

        // Calculate minimum q and r
        foreach ($game->board->toArray() as $pos => $stack) {
            [$q, $r] = Util::parsePosition($pos);
            $minQ = min($minQ, $q);
            $minR = min($minR, $r);
        }

        $minQ--;
        $minR--;

        // Generate tile data
        $tiles = [];
        foreach ($game->board->toArray() as $pos => $stack) {
            [$q, $r] = Util::parsePosition($pos);
            $left = $width * (($q - $minQ) + ($r - $minR) / 2) . 'px';
            $top = $height * ($r - $minR) . 'px';

            $tiles[$pos] = [
                'class' => 'player' . $stack[0]->getPlayer() . (count($stack) > 1 ? ' stacked' : ''),
                'style' => "left: $left; top: $top;",
                'label' => "$q,$r",
                'type' => $stack[0]->getType()->value,
            ];
        }

        // Generate empty tile data
        foreach ($game->getAdjacentPositions() as $pos) {
            if ($game->board->hasTile($pos)) {
                continue;
            }

            [$q, $r] = Util::parsePosition($pos);
            $left = $width * (($q - $minQ) + ($r - $minR) / 2) . 'px';
            $top = $height * ($r - $minR) . 'px';

            $tiles[$pos] = [
                'class' => 'empty',
                'style' => "left: $left; top: $top;",
                'label' => "$q,$r",
                'type' => '&nbsp;',
            ];
        }

        // Sort tiles
        uksort($tiles, function ($a, $b) {
            [$q1, $r1] = Util::parsePosition($a);
            [$q2, $r2] = Util::parsePosition($b);
            return $r1 === $r2
                ? $q1 <=> $q2
                : $r1 <=> $r2;
        });

        return $tiles;
    }
}
