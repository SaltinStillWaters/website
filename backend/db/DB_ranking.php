<?php
require_once('db.php');

class DB_ranking
{
    public static $tableName = 'hero';

    public static function getTable($table = '')
    {
        if ($table === '')
        {
            $table = self::$tableName;
        }
        
        $conn = DB::openConnection();

        $sql = "SELECT * FROM " . $table;

        $result = mysqli_query($conn, $sql);

        if (!$result)
        {
            echo "ERROR IN SQL FROM getTable(): " . mysqli_error($conn);
            exit();
        }

        return mysqli_fetch_all($result, MYSQLI_NUM);
    }
}