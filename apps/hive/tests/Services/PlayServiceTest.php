<?php

namespace Hive\Tests\Services;

use Hive\Core\Game;
use Hive\Repositories\MoveRepository;
use Hive\Services\PlayService;
use Hive\Session;
use Mockery;
use PHPUnit\Framework\TestCase;

class PlayServiceTest extends TestCase
{
    public function testPlay(): void
    {
        $game = new Game();

        $session = Mockery::mock(Session::class);
        $repository = Mockery::mock(MoveRepository::class);

        $session->allows()->set(Mockery::any(), Mockery::any());
        $repository->allows()->create(Mockery::any(), Mockery::any(), Mockery::any())->andReturns(1);

        $service = new PlayService($session, $repository);

        $service->play($game, 'Q', '0,0');

        $this->assertTrue($game->board->hasTile('0,0'), 'The tile should be placed on the board.');
        $this->assertEquals(0, $game->hand[0]['Q'], 'The tile should be removed from the player\'s hand.');
        $this->assertEquals(1, $game->player, 'The player should be switched.');

        $repository->shouldHaveReceived()->create('play', 'Q', '0,0'); // The move should be saved.
        $session->shouldHaveReceived()->set('last_move', 1); // The last move should be saved.
    }
}
