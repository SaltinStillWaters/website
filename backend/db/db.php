<?php

class DB
{
    public static $HOST = 'localhost';
    public static $USER = 'root';
    public static $PASSWORD = '';
    public static $NAME = 'website';

    public static function openConnection($host='', $user='', $pass='', $name='')
    {
        $host = $host === '' ? self::$HOST : $host;
        $user = $user === '' ? self::$USER : $user;
        $pass = $pass === '' ? self::$PASSWORD : $pass;
        $name = $name === '' ? self::$NAME : $name;

        $conn = mysqli_connect($host, $user, $pass, $name);

        if (mysqli_connect_errno()) 
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
			exit();
		}

        return $conn;
    }
    private static function getColumnNames($conn, $tableName)
    {
        $sql = "SHOW COLUMNS FROM $tableName";
        $result = mysqli_query($conn, $sql);

        if (!$result)
        {
            echo "ERROR IN getColumnNames() " . mysqli_error($conn);
            exit();
        }

        $column_names = [];
        while ($row = mysqli_fetch_assoc($result))
        {
            $column_names[] = $row['Field'];
        }

        return $column_names;
    }
    private static function validateInfos($infos, $column_names, $tableName)
    {
        $validated = [];
        foreach ($infos as $id => $content)
        {
            if (in_array($tableName . "_" . $id, $column_names))
            {
                $validated[$id] = $content;
            }
        }

        return $validated;
    }
    public static function addNewUser(array $infos, string $tableName)
    {
        $conn = self::openConnection();
        $column_names = self::getColumnNames($conn, $tableName);
        
        $infos = self::validateInfos($infos, $column_names, $tableName);
        
        $sql = "INSERT INTO $tableName(";
        foreach ($infos as $id => $content)
        {
            $sql .= "$tableName" . "_" . "$id, ";
        }
        $sql = substr($sql, 0, -2) . ") VALUES(";

        foreach ($infos as $id => $content)
        {
            $sql .= "'$content', ";
        }
        $sql = substr($sql, 0, -2) . ");";
        
        
        if (!mysqli_query($conn, $sql))
        {
            echo "ERROR IN SQL FROM addNewUser(): " . mysqli_error($conn);
            exit();
        }

        mysqli_close($conn);
    }
}