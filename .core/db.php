<?php

class Database
{
    private static $instance = null;
    private $connection = null;
    protected function __construct()
    {
        $this->connection = new \PDO(
            'mysql:host=localhost;dbname=rolldelivery',
            'root',
            '',
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    }
    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \BadMethodCallException('Unable to deserialize database connection.');
    }

    public static function getInstance(): Database
    {
        if (null == self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function connection(): \PDO
    {
        return self::getInstance()->connection;
    }

    public static function prepare($statement): \PDOStatement
    {
        return static::connection()->prepare($statement);
    }

    public static function lastInsertId(): int
    {
        return intval(static::connection()->lastInsertId());
    }
}