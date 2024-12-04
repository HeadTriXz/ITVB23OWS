<?php

use Hive\Core\Game;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;
use Hive\Validators\PassValidator;
use PHPUnit\Framework\TestCase;

class PassValidatorTest extends TestCase
{
    public function testValidateNoMove(): void
    {
        $game = new Game();
        $validator = new PassValidator();

        $game->player = 0;
        $game->hand = [
            0 => ['Q' => 0, 'B' => 0, 'S' => 0, 'A' => 0, 'G' => 0],
            1 => ['Q' => 0, 'B' => 0, 'S' => 0, 'A' => 0, 'G' => 0]
        ];

        $error = $validator->validate($game);

        $this->assertNull($error);
    }

    public function testValidateCanPlaceTile(): void
    {
        $game = new Game();
        $validator = new PassValidator();

        $game->player = 0;
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 1));

        $game->hand = [
            0 => ['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3]
        ];

        $error = $validator->validate($game);

        $this->assertEquals('You can still place a tile', $error);
    }

    public function testValidateCanMoveTile(): void
    {
        $game = new Game();
        $validator = new PassValidator();

        $game->player = 0;
        $game->board->addTile("0,0", Tile::from(TileType::QueenBee, 0));
        $game->board->addTile("0,1", Tile::from(TileType::QueenBee, 1));
        $game->board->addTile("0,-1", Tile::from(TileType::Beetle, 0));
        $game->board->addTile("0,2", Tile::from(TileType::Beetle, 1));
        $game->board->addTile("0,-2", Tile::from(TileType::Beetle, 0));
        $game->board->addTile("0,3", Tile::from(TileType::Beetle, 1));
        $game->board->addTile("0,-3", Tile::from(TileType::Spider, 0));
        $game->board->addTile("0,4", Tile::from(TileType::Spider, 1));
        $game->board->addTile("0,-4", Tile::from(TileType::Spider, 0));
        $game->board->addTile("0,5", Tile::from(TileType::Spider, 1));
        $game->board->addTile("0,-5", Tile::from(TileType::SoldierAnt, 0));
        $game->board->addTile("0,6", Tile::from(TileType::SoldierAnt, 1));
        $game->board->addTile("0,-6", Tile::from(TileType::SoldierAnt, 0));
        $game->board->addTile("0,7", Tile::from(TileType::SoldierAnt, 1));
        $game->board->addTile("0,-7", Tile::from(TileType::SoldierAnt, 0));
        $game->board->addTile("0,8", Tile::from(TileType::SoldierAnt, 1));
        $game->board->addTile("0,-8", Tile::from(TileType::Grasshopper, 0));
        $game->board->addTile("0,9", Tile::from(TileType::Grasshopper, 1));
        $game->board->addTile("0,-9", Tile::from(TileType::Grasshopper, 0));
        $game->board->addTile("0,10", Tile::from(TileType::Grasshopper, 1));
        $game->board->addTile("0,-10", Tile::from(TileType::Grasshopper, 0));
        $game->board->addTile("0,11", Tile::from(TileType::Grasshopper, 1));

        $game->hand = [
            0 => ['Q' => 0, 'B' => 0, 'S' => 0, 'A' => 0, 'G' => 0],
            1 => ['Q' => 0, 'B' => 0, 'S' => 0, 'A' => 0, 'G' => 0]
        ];

        $error = $validator->validate($game);

        $this->assertEquals('You can still move a tile', $error);
    }
}
