<?php

namespace Hive\Tests\Tiles;

use Hive\Core\GameBoard;
use Hive\Tiles\Grasshopper;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;
use PHPUnit\Framework\TestCase;

class GrasshopperTest extends TestCase
{
    public function testGetValidMovesStraight(): void
    {
        $board = new GameBoard();
        $grasshopper = new Grasshopper(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $board->addTile("0,-1", $grasshopper);
        $board->addTile("0,2", Tile::from(TileType::Beetle, 1));

        $validMoves = $grasshopper->getValidMoves($board, "0,-1");

        $this->assertEquals(["0,3"], $validMoves, "Grasshopper should be able to move in a straight line.");
    }

    public function testGetValidMovesAtLeastOneTile(): void
    {
        $board = new GameBoard();
        $grasshopper = new Grasshopper(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $board->addTile("0,-1", $grasshopper);
        $board->addTile("0,2", Tile::from(TileType::Beetle, 1));

        $validMoves = $grasshopper->getValidMoves($board, "0,-1");

        $this->assertNotContains("0,-2", $validMoves, "Grasshopper must move at least one tile.");
    }

    public function testGetValidMovesNoOccupiedTiles(): void
    {
        $board = new GameBoard();
        $grasshopper = new Grasshopper(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $board->addTile("0,-1", $grasshopper);
        $board->addTile("0,2", Tile::from(TileType::Beetle, 1));

        $validMoves = $grasshopper->getValidMoves($board, "0,-1");

        $this->assertNotContains("0,0", $validMoves, "Grasshopper cannot move to an occupied tile.");
    }

    public function testGetValidMovesJumpOverEmptyTiles(): void
    {
        $board = new GameBoard();
        $grasshopper = new Grasshopper(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $board->addTile("-1,2", Tile::from(TileType::Beetle, 0));
        $board->addTile("-1,3", Tile::from(TileType::Beetle, 1));
        $board->addTile("0,-1", $grasshopper);
        $board->addTile("0,3", Tile::from(TileType::Spider, 1));

        $validMoves = $grasshopper->getValidMoves($board, "0,-1");

        $this->assertNotContains("0,4", $validMoves, "Grasshopper cannot jump over empty tiles.");
        $this->assertContains("0,2", $validMoves, "Grasshopper must stop at the first occupied tile.");
    }
}
