<?php

namespace Hive\Tests\Validators;

use Hive\Core\Game;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;
use Hive\Validators\PlayValidator;
use PHPUnit\Framework\TestCase;

class PlayValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $game = new Game();
        $validator = new PlayValidator();

        $game->player = 0;
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 1));

        $game->hand = [
            0 => ['Q' => 1, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 1, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $error = $validator->validate($game, 'B', '0,-1');

        $this->assertNull($error);
    }

    public function testValidateTileNotInHand(): void
    {
        $game = new Game();
        $validator = new PlayValidator();

        $game->player = 0;
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 1));

        $game->hand = [
            0 => ['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $error = $validator->validate($game, 'Q', '0,-1');

        $this->assertEquals('Player does not have tile', $error);
    }

    public function testValidateTileAlreadyExists(): void
    {
        $game = new Game();
        $validator = new PlayValidator();

        $game->player = 1;
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));

        $game->hand = [
            0 => ['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 1, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $error = $validator->validate($game, 'Q', '0,0');

        $this->assertEquals('Board position is not empty', $error);
    }

    public function testValidateNoNeighbours(): void
    {
        $game = new Game();
        $validator = new PlayValidator();

        $game->player = 0;
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 1));

        $game->hand = [
            0 => ['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $error = $validator->validate($game, 'B', '2,-1');

        $this->assertEquals('Board position has no neighbour', $error);
    }

    public function testValidateNoNeighboursOnFirstMove(): void
    {
        $game = new Game();
        $validator = new PlayValidator();

        $game->player = 0;
        $game->hand = [
            0 => ['Q' => 1, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 1, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $error = $validator->validate($game, 'Q', '0,0');

        $this->assertNull($error);
    }

    public function testValidateOpposingNeighbours(): void
    {
        $game = new Game();
        $validator = new PlayValidator();

        $game->player = 0;
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 1));

        $game->hand = [
            0 => ['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $error = $validator->validate($game, 'B', '1,0');

        $this->assertEquals('Board position has opposing neighbour', $error);
    }

    public function testValidateMustPlaceQueenBee(): void
    {
        $game = new Game();
        $validator = new PlayValidator();

        $game->player = 0;
        $game->hand = [
            0 => ['Q' => 1, 'B' => 0, 'S' => 1, 'A' => 3, 'G' => 3],
            1 => ['Q' => 0, 'B' => 1, 'S' => 1, 'A' => 3, 'G' => 3]
        ];

        $game->board->addTile("0,0", Tile::from(TileType::Beetle, 0)); // 1
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $game->board->addTile("0,-1", Tile::from(TileType::Beetle, 0)); // 2
        $game->board->addTile("0,2", Tile::from(TileType::Beetle, 1));
        $game->board->addTile("0,-2", Tile::from(TileType::Spider, 0)); // 3
        $game->board->addTile("0,3", Tile::from(TileType::Spider, 1));

        $error = $validator->validate($game, 'S', '0,-3');

        $this->assertEquals('Must play queen bee', $error);
    }
}
