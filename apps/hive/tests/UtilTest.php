<?php

use Hive\Core\GameBoard;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;
use Hive\Util;
use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    public function testGetQueenPosition(): void
    {
        $board = new GameBoard();
        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));

        $pos = Util::getQueenPosition($board, 0);

        $this->assertEquals("0,0", $pos);
    }

    public function testGetQueenPositionEmpty(): void
    {
        $board = new GameBoard();

        $pos = Util::getQueenPosition($board, 0);

        $this->assertNull($pos);
    }

    public function testGetQueenPositionMultiple(): void
    {
        $board = new GameBoard();
        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("0,1", Tile::from(TileType::QueenBee, 1));

        $pos = Util::getQueenPosition($board, 0);

        $this->assertEquals("0,0", $pos);
    }
}
