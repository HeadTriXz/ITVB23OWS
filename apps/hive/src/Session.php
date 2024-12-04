<?php

namespace Hive;

/**
 * A wrapper class for PHP session handling.
 */
class Session
{
    /**
     * A wrapper class for PHP session handling.
     */
    public function __construct()
    {
        session_start();
    }

    /**
     * Delete a session variable.
     *
     * @param string $key The key of the variable.
     */
    public function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Get a session variable.
     *
     * @param string $key The key of the variable.
     * @return mixed The value of the variable.
     */
    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Set a session variable.
     *
     * @param string $key The key of the variable.
     * @param mixed $value The value of the variable.
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }
}
