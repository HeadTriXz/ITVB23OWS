<?php

namespace Hive\Validators;

use Hive\Core\Game;

/**
 * Represents the base of a validator.
 */
interface ValidatorInterface
{
    /**
     * Check if the move is valid.
     *
     * @param Game $game The current game state.
     * @param string $from The current position of the tile, or the tile to place.
     * @param string $to The new position of the tile.
     * @return ?string An error message if the move is invalid, null otherwise.
     */
    public function validate(Game $game, string $from, string $to): ?string;
}
