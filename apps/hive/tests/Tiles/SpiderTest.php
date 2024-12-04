<?php

namespace Hive\Tests\Tiles;

use Hive\Core\GameBoard;
use Hive\Tiles\Spider;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;
use PHPUnit\Framework\TestCase;

class SpiderTest extends TestCase
{
    public function testGetValidMovesMoveThreeTiles(): void
    {
        $board = new GameBoard();
        $spider = new Spider(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $board->addTile("0,-1", $spider);
        $board->addTile("0,2", Tile::from(TileType::Beetle, 1));

        $validMoves = $spider->getValidMoves($board, "0,-1");

        $this->assertContains("1,1", $validMoves, "Spider should move three tiles.");
        $this->assertNotContains("1,0", $validMoves, "Spider should not be able to move less than three tiles.");
    }

    public function testGetValidMovesMustTouchHive(): void
    {
        $board = new GameBoard();
        $spider = new Spider(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("1,0", Tile::from(TileType::QueenBee, 1));
        $board->addTile("-1,1", Tile::from(TileType::Beetle, 0));
        $board->addTile("2,0", Tile::from(TileType::Beetle, 1));
        $board->addTile("-1,2", Tile::from(TileType::Beetle, 0));
        $board->addTile("2,1", Tile::from(TileType::Beetle, 1));
        $board->addTile("-2,2", $spider);
        $board->addTile("1,2", Tile::from(TileType::Grasshopper, 1));

        $validMoves = $spider->getValidMoves($board, "-2,2");

        $this->assertContains("0,2", $validMoves, "Spider should keep touching the hive.");
        $this->assertNotContains("0,3", $validMoves, "Spider may not skip a tile.");
    }

    public function testGetValidMovesSliding(): void
    {
        $board = new GameBoard();
        $spider = new Spider(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("1,0", Tile::from(TileType::QueenBee, 1));
        $board->addTile("0,2", Tile::from(TileType::Beetle, 0));
        $board->addTile("2,0", Tile::from(TileType::Beetle, 1));
        $board->addTile("-1,2", Tile::from(TileType::Beetle, 0));
        $board->addTile("2,1", Tile::from(TileType::Beetle, 1));
        $board->addTile("-2,2", $spider);
        $board->addTile("1,2", Tile::from(TileType::Grasshopper, 1));

        $validMoves = $spider->getValidMoves($board, "-2,2");

        $this->assertNotContains("1,1", $validMoves, "Spider must adhere to the sliding rule.");
        $this->assertContains("0,-1", $validMoves);
    }

    public function testGetValidMovesBlocked(): void
    {
        $board = new GameBoard();
        $spider = new Spider(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("1,0", Tile::from(TileType::QueenBee, 1));
        $board->addTile("0,2", Tile::from(TileType::Beetle, 0));
        $board->addTile("2,0", Tile::from(TileType::Beetle, 1));
        $board->addTile("-1,2", Tile::from(TileType::Beetle, 0));
        $board->addTile("2,1", Tile::from(TileType::Beetle, 1));
        $board->addTile("0,1", $spider);
        $board->addTile("1,2", Tile::from(TileType::Grasshopper, 1));

        $validMoves = $spider->getValidMoves($board, "0,1");

        $this->assertEmpty($validMoves, "Spider should have no valid moves when surrounded.");
    }
}
