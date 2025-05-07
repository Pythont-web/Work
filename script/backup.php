<?php

class DatabaseBackup
{
    private static $db_name = 'rolldelivery';
    private static $instance = null;
    private $connection = null;
    protected function __construct()
    {
        $host = '';
        $user_name = '';
        $password = '';
        include "setting.php";
        $this->connection = new \PDO(
            'mysql:host='.$host.';dbname=' . self::$db_name,
            $user_name,
            $password,
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

    public static function getInstance(): DatabaseBackup
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

class Db_func
{
    private static $db_name = 'rolldelivery';

    public function get_db_name(): string
    {
        return self::$db_name;
    }
    public function get_tables()
    {
        $query = DatabaseBackup::prepare('SHOW TABLES');
        $query->execute();
        $tables = $query->fetchALL();
        if (!count($tables))
        {
            return null;
        }
        $result = [];
        foreach ($tables as $table)
        {
            $result[] = $table['Tables_in_' . self::get_db_name()];
        }
        return $result;
    }

    public function get_table_create($table)
    {
        $query = DatabaseBackup::prepare('SHOW CREATE TABLE ' . $table);
        $query->execute();
        $result = $query->fetchALL();
        if (!count($result))
        {
            return null;
        }
        return $result[0]['Create Table'];
    }

    public function get_table_value($table)
    {
        $query = DatabaseBackup::prepare('SELECT * FROM ' . $table);
        $query->execute();
        $result = $query->fetchALL();
        if (!count($result))
        {
            return null;
        }
        return $result;
    }

}

function quotes($str): string
{
    return ("'". $str. "'");
}

function backticks($str): string
{
    return ("`". $str. "`");
}

    $db = new Db_func();

    $dumpDir = $_SERVER['DOCUMENT_ROOT'].'/dump/';

    if (file_exists($dumpDir))
    {
        foreach (glob($dumpDir. '/*') as $file)
        {
            unlink($file);
        }
    }
    else
    {
        mkdir($dumpDir);
    }

    $file_name = 'README.sql';
    $fd = fopen($dumpDir . $file_name, "w");

    fwrite($fd, "-- Для корректного выполнения запросов необходимо выполнить следующие действия:\n
-- 1) Создать базу данных `" . $db->get_db_name() . "` с помощью запроса: \n" . "
CREATE DATABASE IF NOT EXISTS `" . $db->get_db_name() . "`;\n" . "
-- 2) Перейти к работе с базой данных `" . $db->get_db_name() . "` с помощью запроса: \n" . "
USE `" . $db->get_db_name() . "`;\n" . "
-- 3) Выполнить запросы из всех файлов типа table.sql; \n
-- 4) Выполнить запросы из всех файлов типа table_foreign_keys.sql;");

    $db_name = $db->get_db_name();
    $tables = $db->get_tables();

    if(!is_null($tables))
    {
        foreach ($tables as $table)
        {
            $file_name = $table . '.sql';
            $create = $db->get_table_create($table);

            if(!is_null($create))
            {
                preg_match('/FOREIGN KEY/', $create, $match);

                if(count($match) > 0)
                {
                    $file_name = $table . '_foreign_key.sql';
                }

                $fd = fopen($dumpDir . $file_name, "w");
                fwrite($fd, "-- Дамп таблицы `" . $table . "` из базы данных `". $db_name . "`\n\n");
                fwrite($fd, "-- Создание таблицы: \n\n");
                fwrite($fd, $create . ";\n\n");
            }

            $select = $db->get_table_value($table);

            if(!is_null($select))
            {
                fwrite($fd, "-- Заполнение таблицы данными: \n\n");
                $column = implode(', ', array_map('backticks', array_keys($select[0])));

                for ($j = 0; $j < count($select); $j++)
                {
                    $value = implode(', ', array_map('quotes', $select[$j]));
                    $insert = "INSERT INTO `" . $table . "` (" . $column. ") VALUES (" . $value . ");";

                    fwrite($fd, $insert . "\n");
                }
            }

            fclose($fd);
        }
    }
