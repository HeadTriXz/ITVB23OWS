<?php

namespace Hive\Validators;

use Hive\Core\Game;
use Hive\Util;

/**
 * Represents a validator for moving a tile.
 */
class MoveValidator implements ValidatorInterface
{
    /**
     * Check if the tile can be moved to the new position.
     *
     * @param Game $game The current game state.
     * @param string $from The old position of the tile.
     * @param string $to The new position of the tile.
     * @return ?string An error message if the move is invalid, null otherwise.
     */
    public function validate(Game $game, string $from, string $to): ?string
    {
        $hand = $game->hand[$game->player];

        if (!$game->board->hasTile($from)) {
            return 'Board position is empty';
        }

        if ($game->board->getTiles($from)[0]->getPlayer() != $game->player) {
            return 'Tile is not owned by player';
        }

        if ($hand['Q']) {
            return 'Queen bee is not played';
        }

        if ($from === $to) {
            return 'Tile must move to a different position';
        }

        // Temporarily remove tile from board
        $tile = $game->board->removeTile($from);
        $error = $this->checkTemporarilyRemovedTile($game, $to);

        $game->board->addTile($from, $tile);
        if ($error) {
            return $error;
        }

        // Check if the move is valid
        $validMoves = $tile->getValidMoves($game->board, $from);
        if (!in_array($to, $validMoves)) {
            return 'Tile must move to a valid position';
        }

        return null;
    }

    /**
     * Run checks on the temporarily removed tile.
     *
     * @param Game $game The current game state.
     * @param string $to The new position of the tile.
     * @return ?string An error message if the move is invalid, null otherwise.
     */
    protected function checkTemporarilyRemovedTile(Game $game, string $to): ?string
    {
        if (!Util::hasNeighbour($to, $game->board) || Util::hasMultipleHives($game->board)) {
            return 'Move would split hive';
        }

        return null;
    }
}
