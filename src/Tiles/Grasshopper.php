<?php

namespace Hive\Tiles;

use Hive\Core\GameBoard;

class Grasshopper extends Tile
{
    protected TileType $type = TileType::Grasshopper;

    public function getValidMoves(GameBoard $board, string $pos): array
    {
        return []; // TODO: Implement getValidMoves() method.
    }
}
