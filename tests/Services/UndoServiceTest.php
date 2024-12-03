<?php

namespace Hive\Tests\Services;

use Hive\Core\Game;
use Hive\Core\GameStatus;
use Hive\Repositories\MoveRepository;
use Hive\Services\UndoService;
use Hive\Session;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class UndoServiceTest extends MockeryTestCase
{
    public function testUndo(): void
    {
        $game = new Game();
        $game->board->addTile('0,0', Tile::from(TileType::QueenBee, 0));
        $game->board->addTile('0,1', Tile::from(TileType::QueenBee, 1));
        $game->board->addTile('0,-1', Tile::from(TileType::Beetle, 0));
        $game->player = 1;

        $session = Mockery::mock(Session::class);
        $repository = Mockery::mock(MoveRepository::class);

        $session->allows()->get('game_id')->andReturns(1);
        $session->allows()->get('last_move')->andReturns(3);
        $session->allows()->set(Mockery::any(), Mockery::any());

        $repository->allows()->delete(1, 3);
        $repository->allows()->find(1, 3)->andReturns([
            'id' => 3,
            'game_id' => 1,
            'move_from' => 'B',
            'move_to' => '0,-1',
            'previous_id' => 2,
            'state' => '[{"0,1":[[1,"Q"]],"0,0":[[0,"Q"]],"0,-1":[[0,"B"]]},[{"Q":0,"B":1,"S":2,"A":3,"G":3},{"Q":0,"B":2,"S":2,"A":3,"G":3}],1,"ongoing"]',
            'type' => 'play'
        ]);

        $repository->allows()->find(1, 2)->andReturns([
            'id' => 2,
            'game_id' => 1,
            'move_from' => 'Q',
            'move_to' => '0,1',
            'previous_id' => 1,
            'state' => '[{"0,0":[[0,"Q"]],"0,1":[[1,"Q"]]},[{"Q":0,"B":2,"S":2,"A":3,"G":3},{"Q":0,"B":2,"S":2,"A":3,"G":3}],0,"ongoing"]',
            'type' => 'play'
        ]);

        $service = new UndoService($session, $repository);

        $service->undo($game);

        $session->shouldHaveReceived()->set('last_move', 2);
        $session->shouldHaveReceived()->set('game', Mockery::on(function ($game) {
            return !$game->board->hasTile('0,-1')
                && $game->board->hasTile('0,0')
                && $game->board->hasTile('0,1')
                && $game->player === 0;
        }));

        $repository->shouldHaveReceived()->delete(1, 3);
    }

    public function testUndoNoMoves(): void
    {
        $game = new Game();

        $session = Mockery::mock(Session::class);
        $repository = Mockery::mock(MoveRepository::class);

        $session->allows()->get('game_id')->andReturns(1);
        $session->allows()->get('last_move')->andReturns(0);
        $session->allows()->set(Mockery::any(), Mockery::any());

        $repository->allows()->find(1, 0)->andReturns(null);

        $service = new UndoService($session, $repository);

        $service->undo($game);

        $session->shouldHaveReceived()->set('error', 'No moves to undo');
    }

    public function testUndoFirstMove(): void
    {
        $game = new Game();
        $game->board->addTile('0,0', Tile::from(TileType::QueenBee, 0));
        $game->player = 1;

        $session = Mockery::mock(Session::class);
        $repository = Mockery::mock(MoveRepository::class);

        $session->allows()->get('game_id')->andReturns(1);
        $session->allows()->get('last_move')->andReturns(1);
        $session->allows()->set(Mockery::any(), Mockery::any());

        $repository->allows()->delete(1, 1);
        $repository->allows()->find(1, 0)->andReturns(null);
        $repository->allows()->find(1, 1)->andReturns([
            'id' => 1,
            'game_id' => 1,
            'move_from' => 'Q',
            'move_to' => '0,0',
            'previous_id' => 0,
            'state' => '[{"0,0":[[0,"Q"]]},[{"Q":0,"B":2,"S":2,"A":3,"G":3},{"Q":1,"B":2,"S":2,"A":3,"G":3}],0,"ongoing"]',
            'type' => 'play'
        ]);

        $service = new UndoService($session, $repository);

        $service->undo($game);

        $session->shouldHaveReceived()->set('game', Mockery::on(function ($game) {
            return $game->board->isEmpty() && $game->player === 0;
        }));

        $repository->shouldHaveReceived()->delete(1, 1);
    }

    public function testUndoGameEnd(): void
    {
        $game = new Game();
        $game->status = GameStatus::WHITE_WINS;

        $session = Mockery::mock(Session::class);
        $repository = Mockery::mock(MoveRepository::class);

        $service = new UndoService($session, $repository);

        $service->undo($game);

        $session->shouldNotHaveReceived()->set(Mockery::any(), Mockery::any());
    }
}
