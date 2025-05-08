<?php

class DatabaseBackup
{
    private string $db_name;
    protected \PDO $connection;
    public function __construct(string $db)
    {
        $this->connection = new \PDO(
            'mysql:host=localhost;dbname=' . $db,
            'root',
            '',
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]
        );

        $this->db_name = $db;
    }

    public function get_db_name(): string
    {
        return $this->db_name;
    }
    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \BadMethodCallException('Unable to deserialize database connection.');
    }

    public function prepare($statement): \PDOStatement
    {
        return $this->connection->prepare($statement);
    }

}

class DatabaseFunctions extends DatabaseBackup
{
    public function __construct(string $db)
    {
        parent::__construct($db);
    }
    public function get_tables(): ?array
    {
        $query = $this->prepare('SHOW TABLES');
        $query->execute();
        $tables = $query->fetchAll();

        if (!count($tables))
        {
            return null;
        }

        $result = [];
        foreach ($tables as $table)
        {
            $result[] = $table['Tables_in_' . $this->get_db_name()];
        }

        return $result;
    }

    public function get_table_create($table) : ?string
    {
        $query = $this->prepare('SHOW CREATE TABLE ' . $table);
        $query->execute();
        $create = $query->fetchALL();

        if (!count($create))
        {
            return null;
        }

        return $create[0]['Create Table'];
    }

    public function get_table_value($table) : ?array
    {
        $query = $this->prepare('SELECT * FROM ' . $table);
        $query->execute();
        $select = $query->fetchALL();

        if (!count($select))
        {
            return null;
        }

        return $select;
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

    $start = microtime(true);

    $db_name = 'wordpress';
    $db = new DatabaseFunctions($db_name);

    $dumpDir = $_SERVER['DOCUMENT_ROOT'].'/dump/';

    $message = "-- Для корректного выполнения запросов необходимо выполнить следующие действия:\n
-- 1) Создать базу данных " . backticks($db_name) . " с помощью запроса: \n" . "
CREATE DATABASE IF NOT EXISTS " . backticks($db_name) . ";\n" . "
-- 2) Перейти к работе с базой данных " . backticks($db_name) . " с помощью запроса: \n" . "
USE " . backticks($db_name) . ";\n" . "
-- 3) Выполнить запросы из всех файлов типа table.sql; \n
-- 4) Выполнить запросы из всех файлов типа table_foreign_keys.sql;";

    if(count($_GET) == 0)
    {
        if (file_exists($dumpDir))
        {
            foreach (glob($dumpDir . '/*') as $file)
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

        fwrite($fd, $message);
    }

    $tables = $db->get_tables();

    if(!is_null($tables))
    {
        $table_number = 0;

        if(isset($_GET['table']))
        {
            $table_number = $_GET['table'];
        }

        for($i = $table_number; $i < count($tables); $i++)
        {
            $file_name = $tables[$i] . '.sql';
            $create = $db->get_table_create($tables[$i]);

            if(!is_null($create))
            {
                preg_match('/FOREIGN KEY[\s\S]*REFERENCES/', $create, $match);

                if(count($match) > 0)
                {
                    $file_name = $tables[$i] . '_foreign_key.sql';
                }

                $fd = fopen($dumpDir . $file_name, "a");

                if(!isset($_GET['row']))
                {
                    fwrite($fd, "-- Дамп таблицы " . backticks($tables[$i]) . " из базы данных ". backticks($db_name) . "\n\n");
                    fwrite($fd, "-- Создание таблицы: \n\n");
                    fwrite($fd, $create . ";\n\n");
                }

                $select = $db->get_table_value($tables[$i]);

                if(!is_null($select))
                {
                    if(!isset($_GET['row']))
                    {
                        fwrite($fd, "-- Заполнение таблицы данными: \n\n");
                    }

                    $column = implode(', ', array_map('backticks', array_keys($select[0])));

                    $row_number = 0;

                    if(isset($_GET['row']))
                    {
                        $row_number = $_GET['row'];
                    }

                    for ($j = $row_number; $j < count($select); $j++)
                    {
                        $value = implode(', ', array_map('quotes', $select[$j]));
                        $insert = "INSERT INTO " . backticks($tables[$i]) . " (" . $column. ") VALUES (" . $value . ");";

                        fwrite($fd, $insert . "\n");

                        sleep(1);

                        if(microtime(true) - $start >= 25 && $j < count($select) - 1)
                        {
                            header('location: backup.php?table='.$i.'&row='.($j + 1));
                            exit();
                        }
                    }

                }

                fclose($fd);
            }

            if(isset($_GET['row']) && $i < count($tables) - 1)
            {
                header('location: backup.php?table='.($i + 1));
                exit();
            }
        }
    }
