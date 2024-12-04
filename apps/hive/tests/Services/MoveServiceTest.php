<?php

namespace Hive\Tests\Services;

use Hive\Core\Game;
use Hive\Repositories\MoveRepository;
use Hive\Services\MoveService;
use Hive\Session;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;
use Mockery;
use PHPUnit\Framework\TestCase;

class MoveServiceTest extends TestCase
{
    public function testMove(): void
    {
        $game = new Game();
        $game->board->addTile('0,0', Tile::from(TileType::Beetle, 0));
        $game->board->addTile('0,1', Tile::from(TileType::Beetle, 1));
        $game->board->addTile('0,-1', Tile::from(TileType::QueenBee, 0));
        $game->board->addTile('0,2', Tile::from(TileType::QueenBee, 1));

        $session = Mockery::mock(Session::class);
        $repository = Mockery::mock(MoveRepository::class);

        $session->allows()->set(Mockery::any(), Mockery::any());
        $repository->allows()->create(Mockery::any(), Mockery::any(), Mockery::any())->andReturns(1);

        $service = new MoveService($session, $repository);

        $service->move($game, '0,-1', '1,-1');

        $this->assertFalse($game->board->hasTile('0,-1'), 'The tile should be removed from the board.');
        $this->assertTrue($game->board->hasTile('1,-1'), 'The tile should be placed on the new position.');
        $this->assertEquals(1, $game->player, 'The player should be switched.');

        $repository->shouldHaveReceived()->create('move', '0,-1', '1,-1'); // The move should be saved.
        $session->shouldHaveReceived()->set('last_move', 1); // The last move should be saved.
    }
}
