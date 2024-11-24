<?php

namespace Hive\Tiles;

use Hive\Core\GameBoard;

/**
 * Represents a SoldierAnt tile.
 */
class SoldierAnt extends Tile
{
    /**
     * The type of the tile (A for Soldier**A**nt).
     *
     * @var TileType
     */
    protected TileType $type = TileType::SoldierAnt;

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
