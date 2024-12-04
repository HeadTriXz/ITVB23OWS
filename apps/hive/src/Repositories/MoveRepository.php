<?php

namespace Hive\Repositories;

use Hive\Database;
use Hive\Session;

/**
 * Represents the repository for moves.
 */
class MoveRepository
{
    /**
     * Represents the repository for moves.
     *
     * @param Session $session The session instance.
     * @param Database $database The database instance.
     */
    public function __construct(protected Session $session, protected Database $database)
    {
    }

    /**
     * Count the number of moves for a specific game.
     *
     * @param int $gameId The ID of the game.
     * @return int The number of moves.
     */
    public function count(int $gameId): int
    {
        $result = $this->database->query("
            SELECT COUNT(*) FROM moves WHERE game_id = $gameId
        ");

        return $result->fetch_row()[0];
    }

    /**
     * Delete a move by ID.
     *
     * @param int $gameId The ID of the game.
     * @param int $id The ID of the move.
     */
    public function delete(int $gameId, int $id): void
    {
        $this->database->execute("
            DELETE FROM moves WHERE game_id = $gameId AND id = $id
        ");
    }

    /**
     * Find a move by ID.
     *
     * @param int $gameId The ID of the game.
     * @param int $id The ID of the move.
     * @return ?array The move as an associative array, or null if not found.
     */
    public function find(int $gameId, int $id): ?array
    {
        $result = $this->database->query("
            SELECT * FROM moves WHERE game_id = $gameId AND id = $id
        ");

        return $result->fetch_assoc();
    }

    /**
     * Find all moves for a specific game.
     *
     * @param int $gameId The ID of the game.
     * @return array The moves for the game.
     */
    public function findAll(int $gameId): array
    {
        $result = $this->database->query("
            SELECT * FROM moves WHERE game_id = $gameId
        ");

        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }

        return $history;
    }

    /**
     * Store a new move in the database.
     *
     * @param string $type The type of the move (e.g. "play", "move").
     * @param string $from The original position of the tile, or the type of tile.
     * @param string $to The new position of the tile.
     * @return int The ID of the new move.
     */
    public function create(string $type, string $from = 'null', string $to = 'null'): int
    {
        $game = $this->session->get('game');
        $gameId = $this->session->get('game_id');
        $previousId = $this->session->get('last_move') ?? 'null';

        $game = $this->database->escape($game);
        $from = $this->database->escape($from);
        $to = $this->database->escape($to);

        $this->database->execute("
            INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state)
            VALUES ($gameId, \"$type\", \"$from\", \"$to\", $previousId, \"$game\")
        ");

        return $this->database->getInsertId();
    }
}
