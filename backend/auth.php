<?php

class Auth
{
    public static function login()
    {
        $username = strtoupper($_POST['name']);
        $pass = $_POST['password'];
        
        $sql = "SELECT user_password FROM USER
                WHERE user_name = '$username'";

        $conn = db::openConnection();
        $result = mysqli_query($conn, $sql);

        if (!$result)
        {
            echo "ERROR IN SQL FROM exists(): " . mysqli_error($conn);
            exit();
        }

        $hash = mysqli_fetch_all($result);
        if (empty($hash))
        {
            return 0;
        }
        
        $hash = $hash[0][0];
        $match = password_verify($pass, $hash);
        
        mysqli_close($conn);

        if ($match) {
            // Store username in session
            $_SESSION['user_name'] = $username;
        }


        return $match;
    }
}