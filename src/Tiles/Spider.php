<?php

namespace Hive\Tiles;

use Hive\Core\GameBoard;

/**
 * Represents a Spider tile.
 */
class Spider extends Tile
{
    /**
     * The type of the tile (S for Spider).
     *
     * @var TileType
     */
    protected TileType $type = TileType::Spider;

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
        return []; // TODO: Implement getValidMoves() method.
    }
}
