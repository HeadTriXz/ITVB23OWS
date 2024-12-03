<?php

namespace Hive\Tests\Validators;

use Hive\Core\Game;
use Hive\Tiles\QueenBee;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;
use Hive\Validators\MoveValidator;
use Mockery;
use PHPUnit\Framework\TestCase;

class MoveValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $game = new Game();
        $validator = new MoveValidator();

        $game->player = 0;
        $game->board->addTile('0,0', Tile::from(TileType::Beetle, 0));
        $game->board->addTile('0,1', Tile::from(TileType::Beetle, 1));
        $game->board->addTile('0,-1', Tile::from(TileType::QueenBee, 0));
        $game->board->addTile('0,2', Tile::from(TileType::QueenBee, 1));

        $game->hand = [
            0 => ['Q' => 0, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 0, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $error = $validator->validate($game, '0,-1', '1,-1');

        $this->assertNull($error);
    }

    public function testValidateEmptyBoard(): void
    {
        $game = new Game();
        $validator = new MoveValidator();

        $error = $validator->validate($game, '0,0', '0,1');

        $this->assertEquals('Board position is empty', $error);
    }

    public function testValidateTileNotOwned(): void
    {
        $game = new Game();
        $validator = new MoveValidator();

        $game->player = 1;
        $game->board->addTile('0,0', Tile::from(TileType::Beetle, 0));
        $game->board->addTile('0,1', Tile::from(TileType::Beetle, 1));
        $game->board->addTile('0,-1', Tile::from(TileType::QueenBee, 0));

        $game->hand = [
            0 => ['Q' => 0, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 1, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $error = $validator->validate($game, '0,-1', '1,-1');

        $this->assertEquals('Tile is not owned by player', $error);
    }

    public function testValidateQueenBeeNotPlayed(): void
    {
        $game = new Game();
        $validator = new MoveValidator();

        $game->player = 1;
        $game->board->addTile('0,0', Tile::from(TileType::Beetle, 0));
        $game->board->addTile('0,1', Tile::from(TileType::Beetle, 1));
        $game->board->addTile('0,-1', Tile::from(TileType::QueenBee, 0));

        $game->hand = [
            0 => ['Q' => 0, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 1, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $error = $validator->validate($game, '0,1', '1,0');

        $this->assertEquals('Queen bee is not played', $error);
    }

    public function testValidateSamePosition(): void
    {
        $game = new Game();
        $validator = new MoveValidator();

        $game->player = 0;
        $game->board->addTile('0,0', Tile::from(TileType::Beetle, 0));
        $game->board->addTile('0,1', Tile::from(TileType::Beetle, 1));
        $game->board->addTile('0,-1', Tile::from(TileType::QueenBee, 0));
        $game->board->addTile('0,2', Tile::from(TileType::QueenBee, 1));

        $game->hand = [
            0 => ['Q' => 0, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 0, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $error = $validator->validate($game, '0,-1', '0,-1');

        $this->assertEquals('Tile must move to a different position', $error);
    }

    public function testValidateHiveSplit(): void
    {
        $game = new Game();
        $validator = new MoveValidator();

        $game->player = 0;
        $game->board->addTile('0,0', Tile::from(TileType::Beetle, 0));
        $game->board->addTile('0,1', Tile::from(TileType::Beetle, 1));
        $game->board->addTile('0,-1', Tile::from(TileType::QueenBee, 0));
        $game->board->addTile('0,2', Tile::from(TileType::QueenBee, 1));

        $game->hand = [
            0 => ['Q' => 0, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 0, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $error = $validator->validate($game, '0,0', '1,-1');

        $this->assertEquals('Move would split hive', $error);
    }

    public function testValidateTileNotEmptyBeetle(): void
    {
        $game = new Game();
        $validator = new MoveValidator();

        $game->player = 0;
        $game->board->addTile('0,0', Tile::from(TileType::QueenBee, 0));
        $game->board->addTile('0,1', Tile::from(TileType::QueenBee, 1));
        $game->board->addTile('0,-1', Tile::from(TileType::Beetle, 0));
        $game->board->addTile('0,2', Tile::from(TileType::Beetle, 1));

        $game->hand = [
            0 => ['Q' => 0, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 0, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $error = $validator->validate($game, '0,-1', '0,0');

        $this->assertNull($error);
        $this->assertNotEquals('Tile not empty', $error);
    }

    public function testValidateTileNotSliding(): void
    {
        $game = new Game();
        $validator = new MoveValidator();

        $game->player = 0;
        $game->board->addTile('0,0', Tile::from(TileType::SoldierAnt, 0));
        $game->board->addTile('0,1', Tile::from(TileType::Beetle, 1));
        $game->board->addTile('1,0', Tile::from(TileType::Beetle, 1));
        $game->board->addTile('1,-1', Tile::from(TileType::Spider, 1));
        $game->board->addTile('0,-1', Tile::from(TileType::Spider, 1));
        $game->board->addTile('-1,0', Tile::from(TileType::SoldierAnt, 1));

        $game->hand = [
            0 => ['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 1, 'B' => 0, 'S' => 0, 'A' => 2, 'G' => 3]
        ];

        $error = $validator->validate($game, '0,0', '-1,1');

        $this->assertNotEquals('Tile must slide', $error);
    }

    public function testValidateInvalidMove(): void
    {
        $game = new Game();
        $validator = new MoveValidator();

        $queenBee = Mockery::mock(QueenBee::class);
        $queenBee->allows([
            'getPlayer' => 0,
            'getValidMoves' => [],
            'getType' => TileType::QueenBee
        ]);

        $game->player = 0;
        $game->board->addTile('0,0', Tile::from(TileType::Beetle, 0));
        $game->board->addTile('0,1', Tile::from(TileType::Beetle, 1));
        $game->board->addTile('0,2', Tile::from(TileType::QueenBee, 1));
        $game->board->addTile('1,-1', $queenBee);

        $game->hand = [
            0 => ['Q' => 0, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 0, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $error = $validator->validate($game, '1,-1', '1,0');

        $this->assertEquals('Tile must move to a valid position', $error);
    }

    public function testValidateRestoreBoard(): void
    {
        $game = new Game();
        $validator = new MoveValidator();

        $game->player = 0;
        $game->board->addTile('0,0', Tile::from(TileType::Beetle, 0));
        $game->board->addTile('0,1', Tile::from(TileType::Beetle, 1));
        $game->board->addTile('0,-1', Tile::from(TileType::QueenBee, 0));
        $game->board->addTile('0,2', Tile::from(TileType::QueenBee, 1));

        $game->hand = [
            0 => ['Q' => 0, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 0, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $oldBoard = $game->board->toJSON();
        $error = $validator->validate($game, '0,-1', '1,-1');
        $newBoard = $game->board->toJSON();

        $this->assertEquals($oldBoard, $newBoard);
        $this->assertNull($error);
    }
}
