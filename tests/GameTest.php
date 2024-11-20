<?php

use Hive\Game;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public function testGetAdjacentPositionsSingle(): void
    {
        $game = new Game();
        $game->board["0,0"] = [[0, "Q"]]; // Place a Queen Bee at 0,0.

        $positions = $game->getAdjacentPositions();

        $this->assertEqualsCanonicalizing([
            "-1,0", "-1,1", "0,-1", "0,1", "1,-1", "1,0"
        ], $positions);
    }

    public function testGetAdjacentPositionsMultiple(): void
    {
        $game = new Game();
        $game->board["0,0"] = [[0, "Q"]]; // Place a Queen Bee at 0,0 (white).
        $game->board["0,1"] = [[1, "Q"]]; // Place a Queen Bee at 0,1 (black).

        $positions = $game->getAdjacentPositions();

        $this->assertEqualsCanonicalizing([
            "-1,0", "-1,1", "0,-1", "0,0", "0,1", "1,-1", "1,0", "1,1", "-1,2", "0,2"
        ], $positions);
    }

    public function testGetPlaceableTiles(): void
    {
        $game = new Game();
        $game->hand = [
            0 => ["Q" => 0, "B" => 2, "S" => 2, "A" => 3, "G" => 3],
            1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]
        ];

        $tiles = $game->getPlaceableTiles(0);

        $this->assertEqualsCanonicalizing(["B", "S", "A", "G"], $tiles);
    }

    public function testGetPlaceableTilesEmpty(): void
    {
        $game = new Game();
        $game->hand = [
            0 => ["Q" => 0, "B" => 0, "S" => 0, "A" => 0, "G" => 0],
            1 => ["Q" => 0, "B" => 0, "S" => 0, "A" => 0, "G" => 0]
        ];

        $tiles = $game->getPlaceableTiles(0);

        $this->assertEquals([], $tiles);
    }

    public function testGetMovableTiles(): void
    {
        $game = new Game();
        $game->board["0,0"] = [[0, "Q"]]; // Place a Queen Bee at 0,0 (white).
        $game->board["0,1"] = [[1, "Q"]]; // Place a Queen Bee at 0,1 (black).
        $game->board["0,-1"] = [[0, "B"]]; // Place a Beetle at 0,-1 (white).

        $tiles = $game->getMovableTiles(0);

        $this->assertEqualsCanonicalizing(["0,0", "0,-1"], $tiles);
    }

    public function testGetMovableTilesEmpty(): void
    {
        $game = new Game();
        $game->board["0,0"] = [[0, "Q"]]; // Place a Queen Bee at 0,0 (white).

        $tiles = $game->getMovableTiles(1);

        $this->assertEquals([], $tiles);
    }

    public function testGetValidPlacePositions(): void
    {
        $game = new Game();
        $game->board["0,0"] = [[0, "Q"]]; // Place a Queen Bee at 0,0 (white).
        $game->board["0,1"] = [[1, "Q"]]; // Place a Queen Bee at 0,1 (black).

        $positions = $game->getValidPlacePositions(0);

        $this->assertEqualsCanonicalizing([
            "-1,0", "-1,1", "0,-1", "1,-1", "1,0", "1,1", "-1,2", "0,2"
        ], $positions);
    }

    public function testGetValidPlacePositionsFirst(): void
    {
        $game = new Game();
        $game->board["0,0"] = [[0, "Q"]]; // Place a Queen Bee at 0,0 (white).

        $positions = $game->getValidPlacePositions(1);

        $this->assertEqualsCanonicalizing([
            "-1,0", "-1,1", "0,-1", "0,1", "1,-1", "1,0"
        ], $positions);
    }
}
