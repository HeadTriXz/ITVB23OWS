<?php

namespace Hive\Tiles;

use Hive\Core\GameBoard;

class Spider extends Tile
{
    protected TileType $type = TileType::Spider;

    public function getValidMoves(GameBoard $board, string $pos): array
    {
        return []; // TODO: Implement getValidMoves() method.
    }
}
