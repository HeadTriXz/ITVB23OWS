<?php

namespace Hive\Repositories;

use Hive\Database;

/**
 * Represents the repository for games.
 */
class GameRepository
{
    /**
     * Represents the repository for games.
     *
     * @param Database $database The database instance.
     */
    public function __construct(protected Database $database)
    {
    }

    /**
     * Creates a new game.
     *
     * @return int The ID of the new game.
     */
    public function create(): int
    {
        $this->database->execute('INSERT INTO games VALUES ()');

        return $this->database->getInsertId();
    }
}
