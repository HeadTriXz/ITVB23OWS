<?php

namespace Hive\Tests\Tiles;

use Hive\Core\GameBoard;
use Hive\Tiles\QueenBee;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;
use PHPUnit\Framework\TestCase;

class QueenBeeTest extends TestCase
{
    public function testGetValidMovesSingle(): void
    {
        $board = new GameBoard();
        $queenBee = new QueenBee(0);

        $board->addTile("0,0", $queenBee);
        $board->addTile("0,1", new QueenBee(1));

        $validMoves = $queenBee->getValidMoves($board, "0,0");

        $this->assertEqualsCanonicalizing(["-1,1", "1,0"], $validMoves);
    }

    public function testGetValidMovesMultiple(): void
    {
        $board = new GameBoard();
        $queenBee = new QueenBee(0);

        $board->addTile("0,0", $queenBee);
        $board->addTile("0,1", Tile::from(TileType::SoldierAnt, 1));
        $board->addTile("1,0", Tile::from(TileType::Beetle, 1));

        $validMoves = $queenBee->getValidMoves($board, "0,0");

        $this->assertEqualsCanonicalizing(["-1,1", "1,-1"], $validMoves);
    }

    public function testGetValidMovesBlocked(): void
    {
        $board = new GameBoard();
        $queenBee = new QueenBee(0);

        // Surround the Queen Bee with other tiles, making no valid moves possible.
        $board->addTile("0,0", $queenBee);
        $board->addTile("-1,0", Tile::from(TileType::Beetle, 1));
        $board->addTile("-1,1", Tile::from(TileType::Grasshopper, 1));
        $board->addTile("0,-1", Tile::from(TileType::Spider, 1));
        $board->addTile("0,1", Tile::from(TileType::SoldierAnt, 1));
        $board->addTile("1,-1", Tile::from(TileType::Beetle, 1));
        $board->addTile("1,0", Tile::from(TileType::Spider, 1));

        $validMoves = $queenBee->getValidMoves($board, "0,0");

        $this->assertEmpty($validMoves, "Queen Bee should have no valid moves when surrounded.");
    }

    public function testGetValidMovesSliding(): void
    {
        $board = new GameBoard();
        $queenBee = new QueenBee(0);

        $board->addTile("0,0", $queenBee);
        $board->addTile("0,1", Tile::from(TileType::SoldierAnt, 1));
        $board->addTile("1,0", Tile::from(TileType::Beetle, 1));
        $board->addTile("-1,0", Tile::from(TileType::Spider, 1));

        $validMoves = $queenBee->getValidMoves($board, "0,0");

        $this->assertEqualsCanonicalizing(["0,-1", "1,-1"], $validMoves);
    }

    public function testGetValidMovesRestoreTile(): void
    {
        $board = new GameBoard();
        $queenBee = new QueenBee(0);

        $board->addTile("0,0", $queenBee);
        $board->addTile("0,1", new QueenBee(1));
        $initialJSON = $board->toJSON();

        $queenBee->getValidMoves($board, "0,0");

        $newJSON = $board->toJSON();
        $this->assertEquals($initialJSON, $newJSON, "Board state should be restored after getting valid moves.");
    }
}
