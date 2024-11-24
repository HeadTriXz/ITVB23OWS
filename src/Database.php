<?php

namespace Hive;

use mysqli;
use mysqli_result;
use RuntimeException;

/**
 * A wrapper class for database handling.
 */
class Database
{
    /**
     * The mysqli connection.
     *
     * @var mysqli
     */
    protected mysqli $database;

    /**
     * A wrapper class for database handling.
     */
    public function __construct()
    {
        $this->database = new mysqli(
            $_ENV['DB_HOST'],
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
            $_ENV['DB_DATABASE'],
            $_ENV['DB_PORT']
        );
    }

    /**
     * Execute a query and return the result.
     *
     * @param string $string The query string.
     * @return mysqli_result The result of the query.
     */
    public function query(string $string): mysqli_result
    {
        $result = $this->database->query($string);
        if ($result === false) {
            throw new RuntimeException($this->database->error);
        }
        return $result;
    }

    /**
     * Execute a query without a result.
     *
     * @param string $string The query string.
     */
    public function execute(string $string): void
    {
        $result = $this->database->query($string);
        if ($result === false) {
            throw new RuntimeException($this->database->error);
        }
    }

    /**
     * Escape a string to prevent SQL injection.
     *
     * @param string $string The string to escape.
     * @return string The escaped string.
     */
    public function escape(string $string): string
    {
        return mysqli_real_escape_string($this->database, $string);
    }

    /**
     * Get the last insert ID.
     *
     * @return int The last insert ID.
     */
    public function getInsertId(): int
    {
        return intval($this->database->insert_id);
    }
}
