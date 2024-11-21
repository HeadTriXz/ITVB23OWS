<?php

namespace Hive\Tiles;

use Hive\Core\GameBoard;

class SoldierAnt extends Tile
{
    protected TileType $type = TileType::SoldierAnt;

    public function getValidMoves(GameBoard $board, string $pos): array
    {
        return []; // TODO: Implement getValidMoves() method.
    }
}
