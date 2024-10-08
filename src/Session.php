<?php

namespace Hive;

// encapsulate session superglobal
class Session {
    private function __construct() {
        session_start();
    }

    private static self $inst;

    public static function inst(): self {
        if (!isset(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    // get session variable
    public function get(string $key): mixed {
        return $_SESSION[$key] ?? null;
    }

    // set session variable
    public function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }
}
