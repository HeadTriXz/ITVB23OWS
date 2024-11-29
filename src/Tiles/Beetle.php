<?php

namespace Hive\Tiles;

use Hive\Core\GameBoard;
use Hive\Util;

/**
 * Represents a Beetle tile.
 */
class Beetle extends Tile
{
    /**
     * The type of the tile (B for Beetle).
     *
     * @var TileType
     */
    protected TileType $type = TileType::Beetle;

    /**
     * Returns the valid moves for the tile.
     *
     * @param GameBoard $board The current board state.
     * @param string $pos The current position of the tile.
     *
     * @return array The valid moves for the tile.
     */
    public function getValidMoves(GameBoard $board, string $pos): array
    {
        // Temporarily remove the tile from the board to check for valid moves.
        $tile = $board->removeTile($pos);

        $positions = [];
        $neighbours = Util::getNeighbours($pos);

        foreach ($neighbours as $neighbour) {
            if (!Util::hasNeighbour($neighbour, $board)) {
                continue;
            }

            if (!Util::isValidSlide($board, $pos, $neighbour)) {
                continue;
            }

            $positions[] = $neighbour;
        }

        $board->addTile($pos, $tile);
        return $positions;
    }
}
