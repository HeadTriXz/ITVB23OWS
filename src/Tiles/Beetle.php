<?php

namespace Hive\Tiles;

use Hive\Core\GameBoard;

class Beetle extends Tile
{
    protected TileType $type = TileType::Beetle;

    public function getValidMoves(GameBoard $board, string $pos): array
    {
        return []; // TODO: Implement getValidMoves() method.
    }
}
