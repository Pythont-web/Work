<?php
function get_db_name()
{
    $query = Database::prepare('SELECT DATABASE()');
    $query->execute();
    $result = $query->fetchALL();
    if (!count($result))
    {
        return "";
    }
    return $result[0]['DATABASE()'];
}
function get_tables(): array
{
    $query = Database::prepare('SHOW TABLES');
    $query->execute();
    $temp = $query->fetchALL();
    if (!count($temp))
    {
        return [];
    }
    $result = [];
    for ($i = 0; $i < count($temp); $i++)
    {
        $result[] = $temp[$i]['Tables_in_' . get_db_name()];
    }
    return $result;
}

function get_table_create($table)
{
    $query = Database::prepare('SHOW CREATE TABLE ' . $table);
    $query->execute();
    $result = $query->fetchALL();
    if (!count($result))
    {
        return "";
    }
    return $result[0]['Create Table'];
}
function get_table_column($table)
{
    $query = Database::prepare('DESC ' . $table);
    $query->execute();
    $result = $query->fetchALL();
    if (!count($result))
    {
        return [];
    }
    return $result;
}

function get_table_value($table)
{
    $query = Database::prepare('SELECT * FROM ' . $table);
    $query->execute();
    $result = $query->fetchALL();
    if (!count($result))
    {
        return [];
    }
    return $result;
}

function get_table_info($table): array
{
    $query = Database::prepare("SELECT ENGINE, SUBSTRING_INDEX(TABLE_COLLATION, '_', 1) AS CHARSET, 
       TABLE_COLLATION AS 'COLLATE' FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '". get_db_name().
        "' AND TABLE_NAME = '". $table. "'");
    $query->execute();
    $temp = $query->fetchALL();

    if (!count($temp))
    {
        return [];
    }

    $result = [];
    $keys = array_keys($temp[0]);
    for($i = 0; $i < count($temp[0]); $i++)
    {
        $result[$keys[$i]] = $temp[0][$keys[$i]];
    }

    return $result;

}
function ZipDirectory($src_dir, $zip, $dir_in_archive='') {
    $dirHandle = opendir($src_dir);
    while (false !== ($file = readdir($dirHandle))) {
        if (($file != '.')&&($file != '..')) {
            if (!is_dir($src_dir.$file)) {
                $zip->addFile($src_dir.$file, $dir_in_archive.$file);
            } else {
                $zip->addEmptyDir($dir_in_archive.$file);
                $zip = ZipDirectory($src_dir.$file.DIRECTORY_SEPARATOR,$zip,$dir_in_archive.$file.DIRECTORY_SEPARATOR);
            }
        }
    }
    return $zip;
}

function ZipFull($src_dir, $archive_path): bool
{
    if(file_exists($archive_path))
    {
        unlink($archive_path);
    }

    $zip = new ZipArchive();
    if ($zip->open($archive_path, ZIPARCHIVE::CREATE) !== true) {
        return false;
    }
    $zip = ZipDirectory($src_dir,$zip);
    $zip->close();
    return true;
}

function quotes($str): string
{
    return ("'". $str. "'");
}

function backticks($str): string
{
    return ("`". $str. "`");
}

$success_db = false;
$success_web = false;

if(isset($_POST['action']) && $_POST['action'] == 'backup')
{
    $dumpDir = './dump/';
    $ZipName = 'dump.zip';
    $WebName = 'backup.zip';

    if (!file_exists($dumpDir))
    {
        mkdir($dumpDir);
    }

    $db = get_db_name();
    $tables = get_tables();

    for ($i = 0; $i < count($tables); $i++)
    {
        $file_name = $tables[$i] . '.sql';
        $fd = fopen($dumpDir . $file_name, "w");
        fwrite($fd, "-- Дамп таблицы `" . $tables[$i] . "` из базы данных `". $db . "`\n\n");
        fwrite($fd, "-- Создание таблицы: \n\n");

        $create = "CREATE TABLE `". $tables[$i] ."` (\n";
        $table_info = get_table_column($tables[$i]);

        for($j = 0; $j < count($table_info); $j++)
        {
            $create .= '  `' . $table_info[$j]['Field'] . '` ' . $table_info[$j]['Type'];

            if($table_info[$j]['Null'] == 'NO')
            {
                $create .= ' NOT NULL';
                if(!is_null($table_info[$j]['Default']))
                {
                    $create .= " DEFAULT '" . $table_info[$j]['Default']. "'";
                }
            }
            else if($table_info[$j]['Null'] == 'YES')
            {
                $create .= ' DEFAULT NULL';
            }

            if($table_info[$j]['Key'] == 'PRI')
            {
                $create .= ' PRIMARY KEY';
            }

            if($table_info[$j]['Extra'] == 'auto_increment')
            {
                $create .= ' AUTO_INCREMENT';
            }

            if($j < count($table_info) - 1)
            {
                $create .= ",\n";
            }
            else
            {
                $create .= "\n)";
            }
        }

        $info = get_table_info($tables[$i]);
        $create .= " ". "ENGINE = ". $info['ENGINE'] . " DEFAULT CHARSET = ". $info['CHARSET'] . " COLLATE = ". $info['COLLATE'] . ";\n\n";

        fwrite($fd, $create);
        fwrite($fd, "-- Заполнение таблицы данными: \n\n");

        $select = get_table_value($tables[$i]);
        $column = implode(', ', array_map('backticks', array_keys($select[0])));

        for ($j = 0; $j < count($select); $j++)
        {
            $value = implode(', ', array_map('quotes', $select[$j]));
            $insert = "INSERT INTO `" . $tables[$i] . "` (" . $column. ") VALUES (" . $value . ");";

            fwrite($fd, $insert . "\n");
        }

        fclose($fd);
    }

    if(ZipFull($dumpDir, '../' . $ZipName))
    {
        $success_db = true;
    }

    if (file_exists($dumpDir)) {
        foreach (glob($dumpDir. '/*') as $file) {
            unlink($file);
        }
    }

    rmdir($dumpDir);

    if(ZipFull('./', '../' . $WebName))
    {
        $success_web = true;
    }

}