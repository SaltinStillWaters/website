<?php

class DB
{
    public static $HOST = 'localhost';
    public static $USER = 'root';
    public static $PASSWORD = '';
    public static $NAME = 'website';

    public static function createUserTable()
    {
        $sql = "CREATE OR REPLACE TABLE USER(
            user_name varchar(255) PRIMARY KEY,
            user_email varchar(255) NOT NULL,
            user_password varchar(255) NOT NULL
        )";

        $conn = self::openConnection();

        mysqli_query($conn, $sql);
    }

    public static function createForumsTables()
    {
        $sql =    "CREATE OR REPLACE TABLE posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_name VARCHAR(255) NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";
$conn = self::openConnection();

mysqli_query($conn, $sql);

        $sql = "CREATE OR REPLACE TABLE comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_name VARCHAR(255) NOT NULL,
            post_id INT NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (post_id) REFERENCES posts(id));";

        mysqli_query($conn, $sql);
        
        $sql = "CREATE OR REPLACE TABLE upvotes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_name VARCHAR(255) NOT NULL,
                post_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (post_id) REFERENCES posts(id)
            );";

        mysqli_query($conn, $sql);
    }
    /**
     * Opens a mysqli connection and returns it
     * @param string $host The host of the database to connect to. Leave blank to use self::$HOST
     * @param string $user The owner of the database. Leave blank to use self::$USER
     * @param string $pass The password of the database. Leave blank to use self::$PASSWORD
     * @param string $name The name of the database. Leave blank to use self::$NAME
     * 
     * @return mysqli Returns an object that represents the connection
     */
    public static function openConnection(string $host='', string $user='', string $pass='', string $name='') : mysqli
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
    public static function addNewUser(array $infos, string $tableName) : bool
    {
        if (!self::passwordsMatch())
        {
            return false;
        }

        $conn = self::openConnection();
        $infos = self::validateInfos($infos, $tableName, $conn);
        
        $infos['password'] = password_hash($infos['password'], PASSWORD_BCRYPT);
        
        if (self::checkDuplicates($tableName, $conn))
        {
            return false;
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
        return true;
    }
    /**
     * Iterates through $infos and returns an associative array where $id matches the columns of the table
     * 
     * @param array $infos An associative array that holds user input. This will most likely be $_POST or $_SESSION[$x]
     * @param string $tableName The table to check against
     * @param mysqli $conn The connection to the database
     */
    private static function validateInfos(array $infos, string $tableName, mysqli $conn) : array
    {
        $column_names = self::getColumnNames($conn, $tableName);

        $validated = [];
        
        foreach ($infos as $id => $content)
        {
            if (in_array($tableName . "_" . $id, $column_names))
            {
                if (is_array($content))
                {
                    $content = $content['content'];
                }

                $validated[$id] = $content;
            }
        }
        return $validated;
    }
    /**
     * Retrieves the column names of a table
     * @param mysqli $conn The connection to the database
     * @param string $tableName The table to retrieve the columns of
     * 
     * @return array Returns the columns of a table as an array
     */
    private static function getColumnNames(mysqli $conn, string $tableName) : array
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
                if (strtoupper($_SESSION[Form::$SESSION_NAME][$id]['content']) === strtoupper($row[$tableName . "_" . $id]))
                {
                    $id = str_replace('_', ' ', $id);
                    $_SESSION[Form::$SESSION_NAME][$id]['error'] = "$id is already taken";
                    $hasDuplicate = 1;
                }
            }    
        }

        return $hasDuplicate;
    }
    private static function passwordsMatch()
    {
        $matched = $_SESSION[Form::$SESSION_NAME]['password']['content'] === $_SESSION[Form::$SESSION_NAME]['password_confirm']['content'];
        if (!$matched)
        {
            $_SESSION[Form::$SESSION_NAME]['password']['error'] = 'Passwords do not match';
            $_SESSION[Form::$SESSION_NAME]['password_confirm']['error'] = 'Passwords do not match';            
        }

        return $matched;
    }
}