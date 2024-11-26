<?php

namespace Hive\Tests\Repositories;

use Hive\Database;
use Hive\Repositories\GameRepository;
use Mockery;
use PHPUnit\Framework\TestCase;

class GameRepositoryTest extends TestCase
{
    public function testCreate(): void
    {
        $database = Mockery::mock(Database::class);
        $database->allows([
            'execute' => null,
            'getInsertId' => 1
        ]);

        $repository = new GameRepository($database);

        $this->assertEquals(1, $repository->create());
    }
}
