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
            mysqli_close($conn);
            exit();
        }

        $column_names = [];
        while ($row = mysqli_fetch_assoc($result))
        {
            $column_names[] = $row['Field'];
        }

        return $column_names;
    }
    private static function validateInfos($infos, $tableName, $conn)
    {
        $column_names = self::getColumnNames($conn, $tableName);

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
    private static function checkDuplicates($tableName, $conn)
    {
        $sql = "SELECT * FROM $tableName";

        $result = mysqli_query($conn, $sql);

        if (!$result)
        {
            echo "ERROR IN SQL FROM checkDuplicates(): " . mysqli_error($conn);
            exit();
        }

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        $uniqueIds = [];
        
        foreach ($_SESSION[Form::$SESSION_NAME] as $id => $key)
        {
            if ($key['unique'])
            {
                $uniqueIds[] = $id;
            }
        }
        
        $hasDuplicate = 0;
        foreach ($rows as $row)
        {
            foreach ($uniqueIds as $id)
            {
                if ($_SESSION[Form::$SESSION_NAME][$id]['content'] === $row[$tableName . "_" . $id])
                {
                    $_SESSION[Form::$SESSION_NAME][$id]['error'] = "$id is already taken";
                    $hasDuplicate = 1;
                }
            }    
        }

        return $hasDuplicate;
    }
    public static function addNewUser(array $infos, string $tableName)
    {
        $conn = self::openConnection();
        $infos = self::validateInfos($infos, $tableName, $conn);

        if (self::checkDuplicates($tableName, $conn))
        {
            return;
        }

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
            mysqli_close($conn);
            exit();
        }

        mysqli_close($conn);
    }
    private static function exists($columnName, $tableName, $needle, $conn)
    {
        $sql = "SELECT $columnName FROM $tableName
                WHERE $columnName = '$needle'";

        $result = mysqli_query($conn, $sql);

        if (!$result)
        {
            echo "ERROR IN SQL FROM exists(): " . mysqli_error($conn);
            exit();
        }

        return mysqli_fetch_array($result) != [];
    }
}