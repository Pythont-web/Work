<?php

namespace core;
use Database;
abstract class TableModule
{
    abstract protected function getTableName() : string;
    abstract protected function getTableColumns() : array;
    abstract protected function getTableValue() : array;
    abstract protected function getTablePrimaryKey() : string;

    public function get_table() : array
    {
        $query = Database::prepare('SELECT * FROM '.$this->getTableName());
        $query->execute();
        $result = $query->fetchALL();
        if (!count($result))
        {
            return [];
        }
        return $result;
    }

    public function get_string_by_id(int $id)
    {
        $query = Database::prepare('SELECT * FROM '.$this->getTableName().' WHERE '.$this->getTablePrimaryKey().' = :id LIMIT 1');
        $query->bindValue(':id', $id);
        $query->execute();

        $result = $query->fetch();

        if (!$result)
        {
            return null;
        }
        return $result;
    }

    public function get_string_by_name($name, $value)
    {
        $query = Database::prepare('SELECT * FROM '.$this->getTableName().' WHERE '.$name.' = :value LIMIT 1');
        $query->bindValue(':value', $value);
        $query->execute();

        $result = $query->fetch();

        if (!$result)
        {
            return null;
        }
        return $result;
    }

    public function get_table_united($table) : array
    {
        $query = Database::prepare('SELECT * FROM '.$this->getTableName().' inner join '.$table->getTableName().
            ' using ('.$table->getTablePrimaryKey().') order by '.$this->getTablePrimaryKey());
        $query->execute();
        $result = $query->fetchALL();
        if (!count($result))
        {
            return [];
        }
        return $result;
    }

    public function get_table_united_filtered($table, $name, $value) : array
    {
        $query = Database::prepare('SELECT * FROM '.$this->getTableName(). ' inner join '.$table->getTableName().
          ' using ('.$table->getTablePrimaryKey().')  WHERE '. $name . ' =  :value');
        $query->bindValue(':value', $value);
        $query->execute();

        $result = $query->fetchALL();

        if (!$result)
        {
            return [];
        }
        return $result;
    }

    public function create($value)
    {
        $column_string = implode(', ', $this->getTableColumns());
        $value_string = implode(', ', $this->getTableValue());

        $sql = 'INSERT INTO '.$this->getTableName().' ('.$column_string.') '.' VALUES '.'('.$value_string.')';
        $query = Database::prepare($sql);

        for($i = 0; $i < count($this->getTableValue()); $i++)
        {
            $query->bindValue($this->getTableValue()[$i], $value[$i]);
        }

        try
        {
            $query->execute();
        }
        catch (\Throwable $exception)
        {
            throw new \PDOException('При добавлении записи в таблицу произошла ошибка');
        }
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM '.$this->getTableName().' WHERE '.$this->getTablePrimaryKey().' = :id';
        $query = Database::prepare($sql);

        $query->bindValue(':id', $id);

        try
        {
            $query->execute();
        }
        catch (\Throwable $exception)
        {
            throw new \PDOException('При удалении записи произошла ошибка');
        }
    }

    public function update($name, $value, $id)
    {
        $sql = 'UPDATE '.$this->getTableName().' SET '.$name.' = :value WHERE '.$this->getTablePrimaryKey().'= :id';
        $query = Database::prepare($sql);

        $query->bindValue(':value', $value);
        $query->bindValue(':id', $id);

        try
        {
            $query->execute();
        }
        catch (\Throwable $exception)
        {
            throw new \PDOException('При обновлении таблицы произошла ошибка');
        }

    }
}