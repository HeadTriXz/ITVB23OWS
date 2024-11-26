<?php

namespace Hive\Tests\Repositories;

use Hive\Core\Game;
use Hive\Database;
use Hive\Repositories\MoveRepository;
use Hive\Session;
use Mockery;
use mysqli_result;
use PHPUnit\Framework\TestCase;

class MoveRepositoryTest extends TestCase
{
    protected const array MOCK_MOVE = [
        'id' => 3,
        'game_id' => 1,
        'type' => 'play',
        'move_from' => 'Q',
        'move_to' => '0,0',
        'previous_id' => 2,
        'state' => '[{"0,0":[[0,"Q"]]},[{"Q":0,"B":2,"S":2,"A":3,"G":3},{"Q":1,"B":2,"S":2,"A":3,"G":3}],1]'
    ];

    public function testFind(): void
    {
        $database = Mockery::mock(Database::class);
        $session = Mockery::mock(Session::class);
        $result = Mockery::mock(mysqli_result::class);

        $database->allows()->query(Mockery::any())->andReturn($result);
        $result->allows()->fetch_assoc()->andReturn(self::MOCK_MOVE);

        $repository = new MoveRepository($session, $database);

        $results = $repository->find(3);

        $this->assertEquals(self::MOCK_MOVE, $results);
        $database->shouldHaveReceived()->query(Mockery::pattern("/id = 3/"));
    }

    public function testFindAll(): void
    {
        $database = Mockery::mock(Database::class);
        $session = Mockery::mock(Session::class);
        $result = Mockery::mock(mysqli_result::class);

        $database->allows()->query(Mockery::any())->andReturn($result);
        $result->allows()->fetch_assoc()->andReturn(self::MOCK_MOVE, self::MOCK_MOVE, null);

        $repository = new MoveRepository($session, $database);

        $results = $repository->findAll(1);

        $this->assertEquals([self::MOCK_MOVE, self::MOCK_MOVE], $results);
        $database->shouldHaveReceived()->query(Mockery::pattern("/game_id = 1/"));
    }

    public function testCreate(): void
    {
        $database = Mockery::mock(Database::class);
        $session = Mockery::mock(Session::class);

        $database->allows([
            'escape' => 'ESCAPED',
            'execute' => null,
            'getInsertId' => 3
        ]);

        $session->allows()->get('game')->andReturns(new Game());
        $session->allows()->get('game_id')->andReturns(1);
        $session->allows()->get('last_move')->andReturns(2);

        $repository = new MoveRepository($session, $database);

        $id = $repository->create('play', 'Q', '0,0');

        $this->assertEquals(3, $id);
    }
}
