<?php

namespace Hive;

// database connectivity
class Database
{
    private \mysqli $db;

    private function __construct() {
        $this->db = new \mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE'], $_ENV['DB_PORT']);
    }

    private static self $inst;

    public static function inst(): self {
        if (!isset(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    // execute query with result
    public function Query(string $string): \mysqli_result {
        $result = $this->db->query($string);
        if ($result === false) {
            throw new \RuntimeException($this->db->error);
        }
        return $result;
    }

    // execute query without result
    public function Execute(string $string) {
        $result = $this->db->query($string);
        if ($result === false) {
            throw new \RuntimeException($this->db->error);
        }
    }

    // escape string for mysql
    public function Escape(string $string): string {
        return mysqli_real_escape_string($this->db, $string);
    }

    // get last insert id
    public function Get_Insert_Id(): int {
        return intval($this->db->insert_id);
    }
}
