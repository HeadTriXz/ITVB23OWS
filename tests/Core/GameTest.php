<?php

namespace Hive\Tests\Core;

use Hive\Core\Game;
use Hive\Core\GameStatus;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public function testGetAdjacentPositionsSingle(): void
    {
        $game = new Game();
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->hand[0]["Q"]--;

        $positions = $game->getAdjacentPositions();

        $this->assertEqualsCanonicalizing([
            "-1,0", "-1,1", "0,-1", "0,1", "1,-1", "1,0"
        ], $positions);
    }

    public function testGetAdjacentPositionsMultiple(): void
    {
        $game = new Game();
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 1));

        $game->hand[0]["Q"]--;
        $game->hand[1]["Q"]--;

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

    public function testGetPlaceableTilesForceQueen(): void
    {
        $game = new Game();
        $game->hand = [
            0 => ["Q" => 1, "B" => 0, "S" => 1, "A" => 3, "G" => 3],
            1 => ["Q" => 0, "B" => 0, "S" => 2, "A" => 3, "G" => 3]
        ];

        $tiles = $game->getPlaceableTiles(0);

        // The queen bee must be played within the first four moves
        $this->assertEquals(["Q"], $tiles);
    }

    public function testGetMovableTiles(): void
    {
        $game = new Game();
        $game->board->addTile("0,0", Tile::from(TileType::Beetle, 0));
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $game->board->addTile("0,-1", Tile::from(TileType::QueenBee, 0));

        $game->hand[0]["B"]--;
        $game->hand[1]["Q"]--;
        $game->hand[0]["Q"]--;

        $tiles = $game->getMovableTiles(0);

        $this->assertEqualsCanonicalizing(["0,-1"], $tiles);
    }

    public function testGetMovableTilesEmpty(): void
    {
        $game = new Game();
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->hand[0]["Q"]--;

        $tiles = $game->getMovableTiles(1);

        $this->assertEquals([], $tiles);
    }

    public function testGetValidPlacePositions(): void
    {
        $game = new Game();
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 1));

        $game->hand[0]["Q"]--;
        $game->hand[1]["Q"]--;

        $positions = $game->getValidPlacePositions(0);

        $this->assertEqualsCanonicalizing(["-1,0", "0,-1", "1,-1"], $positions);
    }

    public function testGetValidPlacePositionsFirst(): void
    {
        $game = new Game();
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->hand[0]["Q"]--;

        $positions = $game->getValidPlacePositions(1);

        $this->assertEqualsCanonicalizing([
            "-1,0", "-1,1", "0,-1", "0,1", "1,-1", "1,0"
        ], $positions);
    }

    public function testIsQueenSurroundedNoQueen(): void
    {
        $game = new Game();

        $isSurrounded = $game->isQueenSurrounded(0);

        $this->assertFalse($isSurrounded);
    }

    public function testIsQueenSurroundedMissing(): void
    {
        $game = new Game();
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $game->board->addTile("1,0", Tile::from(TileType::Beetle, 0));
        $game->board->addTile("-1,2", Tile::from(TileType::Beetle, 1));
        $game->board->addTile("-1,1", Tile::from(TileType::Beetle, 0));
        $game->board->addTile("0,2", Tile::from(TileType::Beetle, 1));

        $isSurrounded = $game->isQueenSurrounded(1);

        $this->assertFalse($isSurrounded);
    }

    public function testIsQueenSurroundedAll(): void
    {
        $game = new Game();
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $game->board->addTile("1,0", Tile::from(TileType::Beetle, 0));
        $game->board->addTile("-1,2", Tile::from(TileType::Beetle, 1));
        $game->board->addTile("-1,1", Tile::from(TileType::Beetle, 0));
        $game->board->addTile("0,2", Tile::from(TileType::Beetle, 1));
        $game->board->addTile("1,1", Tile::from(TileType::Spider, 0));

        $isSurrounded = $game->isQueenSurrounded(1);

        $this->assertTrue($isSurrounded);
    }

    public function testUpdateStatusNewGame(): void
    {
        $game = new Game();

        $game->updateStatus();

        $this->assertEquals(GameStatus::ONGOING, $game->status);
    }

    public function testUpdateStatusBlackSurrounded(): void
    {
        $game = new Game();
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $game->board->addTile("1,0", Tile::from(TileType::Beetle, 0));
        $game->board->addTile("-1,2", Tile::from(TileType::Beetle, 1));
        $game->board->addTile("-1,1", Tile::from(TileType::Beetle, 0));
        $game->board->addTile("0,2", Tile::from(TileType::Beetle, 1));
        $game->board->addTile("1,1", Tile::from(TileType::Spider, 0));

        $game->updateStatus();

        $this->assertEquals(GameStatus::WHITE_WINS, $game->status);
    }

    public function testUpdateStatusWhiteSurrounded(): void
    {
        $game = new Game();
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 1));
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 0));
        $game->board->addTile("1,0", Tile::from(TileType::Beetle, 1));
        $game->board->addTile("-1,2", Tile::from(TileType::Beetle, 0));
        $game->board->addTile("-1,1", Tile::from(TileType::Beetle, 1));
        $game->board->addTile("0,2", Tile::from(TileType::Beetle, 0));
        $game->board->addTile("1,1", Tile::from(TileType::Spider, 1));

        $game->updateStatus();

        $this->assertEquals(GameStatus::BLACK_WINS, $game->status);
    }

    public function testUpdateStatusDraw(): void
    {
        $game = new Game();
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $game->board->addTile("1,0", Tile::from(TileType::Beetle, 0));
        $game->board->addTile("-1,2", Tile::from(TileType::Beetle, 1));
        $game->board->addTile("-1,1", Tile::from(TileType::Beetle, 0));
        $game->board->addTile("0,2", Tile::from(TileType::Beetle, 1));
        $game->board->addTile("1,1", Tile::from(TileType::Spider, 0));
        $game->board->addTile("-1,0", Tile::from(TileType::Spider, 1));
        $game->board->addTile("0,-1", Tile::from(TileType::Spider, 0));
        $game->board->addTile("1,-1", Tile::from(TileType::Spider, 1));

        $game->updateStatus();

        $this->assertEquals(GameStatus::DRAW, $game->status);
    }
}
