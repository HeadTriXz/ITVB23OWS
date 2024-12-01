<?php

namespace Hive\Validators;

use Hive\Core\Game;

/**
 * Represents a validator for passing a turn.
 */
class PassValidator
{
    /**
     * Check if the player can pass the turn.
     *
     * @param Game $game The current game state.
     * @return ?string An error message if they can't pass, null otherwise.
     */
    public function validate(Game $game): ?string
    {
        $placeableTiles = $game->getPlaceableTiles($game->player);
        if (count($placeableTiles) > 0) {
            return 'You can still place a tile';
        }

        $movableTiles = $game->getMovableTiles($game->player);
        if (count($movableTiles) > 0) {
            return 'You can still move a tile';
        }

        return null;
    }
}
