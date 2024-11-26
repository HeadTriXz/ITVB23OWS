<?php

namespace Hive\Validators;

use Hive\Core\Game;
use Hive\Util;

/**
 * Represents a validator for playing a tile.
 */
class PlayValidator implements ValidatorInterface
{
    /**
     * Check if the tile can be placed on the board.
     *
     * @param Game $game The current game state.
     * @param string $from The tile to place.
     * @param string $to The new position of the tile.
     * @return ?string An error message if the move is invalid, null otherwise.
     */
    public function validate(Game $game, string $from, string $to): ?string
    {
        $hand = $game->hand[$game->player];

        if (!$hand[$from]) {
            return 'Player does not have tile';
        }

        if ($game->board->hasTile($to)) {
            return 'Board position is not empty';
        }

        if (!$game->board->isEmpty() && !Util::hasNeighbour($to, $game->board)) {
            return 'Board position has no neighbour';
        }

        if (array_sum($hand) < 11 && !Util::neighboursAreSameColor($game->player, $to, $game->board)) {
            return 'Board position has opposing neighbour';
        }

        if (Util::mustPlayQueen($hand) && $from != 'Q') {
            return 'Must play queen bee';
        }

        return null;
    }
}
