<?php

namespace Hive\Tests\Services;

use Hive\Core\Game;
use Hive\Repositories\MoveRepository;
use Hive\Services\AIService;
use Hive\Services\MoveService;
use Hive\Services\PassService;
use Hive\Services\PlayService;
use Hive\Session;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class AIServiceTest extends MockeryTestCase
{
    public function testPlayPerformMove(): void
    {
        $game = new Game();

        $session = Mockery::mock(Session::class);
        $moveRepository = Mockery::mock(MoveRepository::class);
        $moveService = Mockery::mock(MoveService::class);
        $passService = Mockery::mock(PassService::class);
        $playService = Mockery::mock(PlayService::class);

        $service = Mockery::mock(AIService::class, [
            $session, $moveRepository, $moveService, $passService, $playService
        ])->makePartial();

        $moveRepository->allows()->count(1)->andReturn(1);

        $moveService->allows()->move($game, 'from', 'to');
        $passService->allows()->pass($game);
        $playService->allows()->play($game, 'from', 'to');

        $session->allows()->get('game')->andReturn($game);
        $session->allows()->get('game_id')->andReturn(1);
        $session->allows()->set('error', Mockery::any());

        $service->allows()->fetchMove($game)->andReturn(['move', 'from', 'to']);

        $service->play($game);

        $moveService->shouldHaveReceived()->move($game, 'from', 'to');
        $passService->shouldNotHaveBeenCalled();
        $playService->shouldNotHaveBeenCalled();
    }

    public function testPlayPerformPlay(): void
    {
        $game = new Game();

        $session = Mockery::mock(Session::class);
        $moveRepository = Mockery::mock(MoveRepository::class);
        $moveService = Mockery::mock(MoveService::class);
        $passService = Mockery::mock(PassService::class);
        $playService = Mockery::mock(PlayService::class);

        $service = Mockery::mock(AIService::class, [
            $session, $moveRepository, $moveService, $passService, $playService
        ])->makePartial();

        $moveRepository->allows()->count(1)->andReturn(1);

        $moveService->allows()->move($game, 'from', 'to');
        $passService->allows()->pass($game);
        $playService->allows()->play($game, 'from', 'to');

        $session->allows()->get('game')->andReturn($game);
        $session->allows()->get('game_id')->andReturn(1);
        $session->allows()->set('error', Mockery::any());

        $service->allows()->fetchMove($game)->andReturn(['play', 'from', 'to']);

        $service->play($game);

        $playService->shouldHaveReceived()->play($game, 'from', 'to');
        $moveService->shouldNotHaveBeenCalled();
        $passService->shouldNotHaveBeenCalled();
    }

    public function testPlayPerformPass(): void
    {
        $game = new Game();

        $session = Mockery::mock(Session::class);
        $moveRepository = Mockery::mock(MoveRepository::class);
        $moveService = Mockery::mock(MoveService::class);
        $passService = Mockery::mock(PassService::class);
        $playService = Mockery::mock(PlayService::class);

        $service = Mockery::mock(AIService::class, [
            $session, $moveRepository, $moveService, $passService, $playService
        ])->makePartial();

        $moveRepository->allows()->count(1)->andReturn(1);

        $moveService->allows()->move($game, 'from', 'to');
        $passService->allows()->pass($game);
        $playService->allows()->play($game, 'from', 'to');

        $session->allows()->get('game')->andReturn($game);
        $session->allows()->get('game_id')->andReturn(1);
        $session->allows()->set('error', Mockery::any());

        $service->allows()->fetchMove($game)->andReturn(['pass', null, null]);

        $service->play($game);

        $passService->shouldHaveReceived()->pass($game);
        $moveService->shouldNotHaveBeenCalled();
        $playService->shouldNotHaveBeenCalled();
    }

    public function testPlayNoResponse(): void
    {
        $game = new Game();

        $session = Mockery::mock(Session::class);
        $moveRepository = Mockery::mock(MoveRepository::class);
        $moveService = Mockery::mock(MoveService::class);
        $passService = Mockery::mock(PassService::class);
        $playService = Mockery::mock(PlayService::class);

        $service = Mockery::mock(AIService::class, [
            $session, $moveRepository, $moveService, $passService, $playService
        ])->makePartial();

        $moveRepository->allows()->count(1)->andReturn(1);

        $moveService->allows()->move($game, 'from', 'to');
        $passService->allows()->pass($game);
        $playService->allows()->play($game, 'from', 'to');

        $session->allows()->get('game')->andReturn($game);
        $session->allows()->get('game_id')->andReturn(1);
        $session->allows()->set('error', Mockery::any());

        $service->allows()->fetchMove($game)->andReturn(null);

        $service->play($game);

        $moveService->shouldNotHaveBeenCalled();
        $passService->shouldNotHaveBeenCalled();
        $playService->shouldNotHaveBeenCalled();
    }
}
