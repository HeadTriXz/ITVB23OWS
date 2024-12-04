<?php

namespace Hive\Tests\Tiles;

use Hive\Core\GameBoard;
use Hive\Tiles\SoldierAnt;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;
use PHPUnit\Framework\TestCase;

class SoldierAntTest extends TestCase
{
    public function testGetValidMovesAnyDistance(): void
    {
        $board = new GameBoard();
        $ant = new SoldierAnt(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $board->addTile("0,-1", $ant);
        $board->addTile("0,2", Tile::from(TileType::Beetle, 1));

        $validMoves = $ant->getValidMoves($board, "0,-1");

        $this->assertEqualsCanonicalizing([
            "-1,0", "-1,1", "-1,2", "-1,3", "0,3", "1,2", "1,1", "1,0", "1,-1"
        ], $validMoves);
    }

    public function testGetValidMovesExcludeOccupied(): void
    {
        $board = new GameBoard();
        $ant = new SoldierAnt(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $board->addTile("0,-1", $ant);
        $board->addTile("0,2", Tile::from(TileType::Beetle, 1));

        $validMoves = $ant->getValidMoves($board, "0,-1");

        $this->assertNotContains("0,2", $validMoves);
    }

    public function testGetValidMovesExcludeCurrent(): void
    {
        $board = new GameBoard();
        $ant = new SoldierAnt(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $board->addTile("0,-1", $ant);
        $board->addTile("0,2", Tile::from(TileType::Beetle, 1));

        $validMoves = $ant->getValidMoves($board, "0,-1");

        $this->assertNotContains("0,-1", $validMoves);
    }

    public function testGetValidMovesSliding(): void
    {
        $board = new GameBoard();
        $ant = new SoldierAnt(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("1,0", Tile::from(TileType::QueenBee, 1));
        $board->addTile("0,2", Tile::from(TileType::Beetle, 0));
        $board->addTile("2,0", Tile::from(TileType::Beetle, 1));
        $board->addTile("-1,2", Tile::from(TileType::Beetle, 0));
        $board->addTile("2,1", Tile::from(TileType::Beetle, 1));
        $board->addTile("-2,2", $ant);
        $board->addTile("1,2", Tile::from(TileType::Grasshopper, 1));

        $validMoves = $ant->getValidMoves($board, "-2,2");

        $this->assertNotContains("1,1", $validMoves, "Ant must adhere to the sliding rule.");
        $this->assertContains("0,-1", $validMoves);
    }

    public function testGetValidMovesBlocked(): void
    {
        $board = new GameBoard();
        $ant = new SoldierAnt(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("1,0", Tile::from(TileType::QueenBee, 1));
        $board->addTile("-1,0", Tile::from(TileType::Beetle, 0));
        $board->addTile("-1,1", $ant);
        $board->addTile("-1,2", Tile::from(TileType::Beetle, 1));
        $board->addTile("-2,1", Tile::from(TileType::Beetle, 0));
        $board->addTile("-2,2", Tile::from(TileType::Beetle, 1));

        $validMoves = $ant->getValidMoves($board, "-1,1");

        $this->assertEmpty($validMoves, "Ant should have no valid moves when surrounded.");
    }
}
