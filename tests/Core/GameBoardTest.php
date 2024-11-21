<?php

namespace Hive\Tests\Core;

use Hive\Core\GameBoard;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;
use PHPUnit\Framework\TestCase;

class GameBoardTest extends TestCase
{
    public function testAddTile(): void
    {
        $board = new GameBoard();
        $tile = Tile::from(TileType::QueenBee, 0);

        $board->addTile("0,0", $tile);

        $this->assertTrue($board->hasTile("0,0"));
        $this->assertEquals([$tile], $board->getTiles("0,0"));
    }

    public function testAddTileMultiple(): void
    {
        $board = new GameBoard();
        $tile1 = Tile::from(TileType::QueenBee, 0);
        $tile2 = Tile::from(TileType::Beetle, 1);

        $board->addTile("0,0", $tile1);
        $board->addTile("0,0", $tile2);

        $this->assertTrue($board->hasTile("0,0"));
        $this->assertEquals([$tile2, $tile1], $board->getTiles("0,0"));
    }

    public function testRemoveTile(): void
    {
        $board = new GameBoard();
        $tile = Tile::from(TileType::Beetle, 1);

        $board->addTile("0,0", $tile);
        $removedTile = $board->removeTile("0,0");

        $this->assertEquals($tile, $removedTile);
        $this->assertFalse($board->hasTile("0,0"));
    }

    public function testRemoveTileRemaining(): void
    {
        $board = new GameBoard();
        $tile1 = Tile::from(TileType::QueenBee, 0);
        $tile2 = Tile::from(TileType::Beetle, 1);

        $board->addTile("0,0", $tile1);
        $board->addTile("0,0", $tile2);

        $removedTile = $board->removeTile("0,0");

        $this->assertEquals($tile2, $removedTile);
        $this->assertTrue($board->hasTile("0,0"));
        $this->assertEquals([$tile1], $board->getTiles("0,0"));
    }

    public function testRemoveTileFromEmptyPosition(): void
    {
        $board = new GameBoard();

        $removedTile = $board->removeTile("0,0");

        $this->assertNull($removedTile);
    }

    public function testGetTiles(): void
    {
        $board = new GameBoard();
        $tile1 = Tile::from(TileType::QueenBee, 0);
        $tile2 = Tile::from(TileType::Beetle, 1);

        $board->addTile("0,0", $tile1);
        $board->addTile("0,0", $tile2);

        $tiles = $board->getTiles("0,0");

        $this->assertEquals([$tile2, $tile1], $tiles);
    }

    public function testIsEmpty(): void
    {
        $board = new GameBoard();
        $this->assertTrue($board->isEmpty());

        $board->addTile("0,0", Tile::from(TileType::Grasshopper, 0));

        $this->assertFalse($board->isEmpty());
    }

    public function testHasTile(): void
    {
        $board = new GameBoard();
        $this->assertFalse($board->hasTile("0,0"));

        $board->addTile("0,0", Tile::from(TileType::Spider, 0));

        $this->assertTrue($board->hasTile("0,0"));
    }

    public function testKeys(): void
    {
        $board = new GameBoard();
        $board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $board->addTile("1,0", Tile::from(TileType::Beetle, 1));

        $keys = $board->keys();

        $this->assertEqualsCanonicalizing(["0,0", "1,0"], $keys);
    }

    public function testToArray(): void
    {
        $board = new GameBoard();
        $tile1 = Tile::from(TileType::QueenBee, 0);
        $tile2 = Tile::from(TileType::Beetle, 1);

        $board->addTile("0,0", $tile1);
        $board->addTile("0,0", $tile2);

        $array = $board->toArray();

        $this->assertEquals([
            "0,0" => [$tile2, $tile1]
        ], $array);
    }

    public function testToJSON(): void
    {
        $board = new GameBoard();
        $tile1 = Tile::from(TileType::QueenBee, 0);
        $tile2 = Tile::from(TileType::Beetle, 1);

        $board->addTile("0,0", $tile1);
        $board->addTile("0,0", $tile2);

        $json = $board->toJSON();

        $this->assertEquals([
            "0,0" => [
                [1, TileType::Beetle],
                [0, TileType::QueenBee]
            ]
        ], $json);
    }

    public function testFromArray(): void
    {
        $board = new GameBoard();

        $data = [
            "0,0" => [
                [0, TileType::QueenBee],
                [1, TileType::Beetle]
            ]
        ];

        $board->fromArray($data);

        $tiles = $board->getTiles("0,0");

        $this->assertCount(2, $tiles);
        $this->assertEquals(Tile::from(TileType::QueenBee, 0), $tiles[1]);
        $this->assertEquals(Tile::from(TileType::Beetle, 1), $tiles[0]);
    }

    public function testFromArrayEmpty(): void
    {
        $board = new GameBoard();
        $board->fromArray([]);

        $this->assertTrue($board->isEmpty());
    }
}
