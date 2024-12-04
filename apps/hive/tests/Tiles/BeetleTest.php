<?php

namespace Hive\Tests\Tiles;

use Hive\Core\GameBoard;
use Hive\Tiles\Beetle;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;
use PHPUnit\Framework\TestCase;

class BeetleTest extends TestCase
{
    public function testGetValidMovesStackOwn(): void
    {
        $board = new GameBoard();
        $beetle = new Beetle(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $board->addTile("0,-1", $beetle);
        $board->addTile("0,2", Tile::from(TileType::Beetle, 0));

        $validMoves = $beetle->getValidMoves($board, "0,-1");

        $this->assertEqualsCanonicalizing(["-1,0", "0,0", "1,-1"], $validMoves);
    }

    public function testGetValidMovesStackOpponent(): void
    {
        $board = new GameBoard();
        $beetle = new Beetle(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $board->addTile("1,0", $beetle);
        $board->addTile("0,2", Tile::from(TileType::Beetle, 1));

        $validMoves = $beetle->getValidMoves($board, "1,0");

        $this->assertEqualsCanonicalizing(["0,0", "0,1", "1,-1", "1,1"], $validMoves);
    }

    public function testGetValidMovesSliding(): void
    {
        $board = new GameBoard();
        $beetle = new Beetle(0);

        $board->addTile("0,0", $beetle);
        $board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $board->addTile("1,0", Tile::from(TileType::Beetle, 1));
        $board->addTile("-1,0", Tile::from(TileType::Beetle, 1));

        $validMoves = $beetle->getValidMoves($board, "0,0");

        $this->assertNotContains("-1,1", $validMoves);
    }

    public function testGetValidMovesSplit(): void
    {
        $board = new GameBoard();
        $beetle = new Beetle(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $board->addTile("0,-1", $beetle);
        $board->addTile("0,2", Tile::from(TileType::Beetle, 0));

        $validMoves = $beetle->getValidMoves($board, "0,-1");

        $this->assertNotContains("0,-2", $validMoves);
    }

    public function testGetValidMovesRestoreTile(): void
    {
        $board = new GameBoard();
        $beetle = new Beetle(0);

        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $board->addTile("0,-1", $beetle);
        $board->addTile("0,2", Tile::from(TileType::Beetle, 0));
        $initialJSON = $board->toJSON();

        $beetle->getValidMoves($board, "0,-1");
        $newJSON = $board->toJSON();

        $this->assertEquals($initialJSON, $newJSON, "Board state should be restored after getting valid moves.");
    }
}
