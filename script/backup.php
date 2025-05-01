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
        return [];
    }
    return $result[0]['Create Table'];
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
        fwrite($fd, "Дамп таблицы `" . $tables[$i] . "` из базы данных `". $db . "`\n\n");
        fwrite($fd, "Создание таблицы: \n\n");

        $create = get_table_create($tables[$i]);

        fwrite($fd, $create. "\n\n");
        fwrite($fd, "Заполнение таблицы данными: \n\n");

        $select = get_table_value($tables[$i]);
        $column = implode(', ', array_keys($select[0]));

        for ($j = 0; $j < count($select); $j++)
        {
            $value = implode(', ', $select[$j]);
            $insert = 'INSERT INTO ' . $tables[$i] . ' (' . $column. ') VALUES (' . $value . ')';

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