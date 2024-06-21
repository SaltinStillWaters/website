<?php
session_start();
require_once('backend/form.php');

Form::init();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    Form::updateContents();
    Form::updateErrors();

    if ($_SESSION[Form::$SESSION_NAME]['password']['content'] === $_SESSION[Form::$SESSION_NAME]['password_confirm']['content'])
    {
        $passErrMsg = '';
    }
    else
    {
        $passErrMsg = 'Passwords does not match';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Felper</title>
    <link href="stylesheet.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="backend/js/utils.js"></script>
</head>
<body>
    <div class="wrapper">
        <form method="post">
            <h1>Sign up</h1>
            <?php
            Form::inputText('email', Type::$Email, 'Email', "<i class='bx bxs-envelope'></i>", true);
            Form::inputText('username', Type::$Text, 'Username', "<i class='bx bxs-user'></i>", true);
            Form::inputPassword('password', 'Password', "<i class='bx bxs-lock-alt'></i>", true);
            Form::inputPassword('password_confirm', 'Confrim Password', "<i class='bx bxs-lock-alt'></i>", true);
            //echo $passErrMsg;
            ?>

            <button type="submit" class="btn">Register</button>

            <div class="register-link">
                <p>Already have an account? <a href="#">Login</a></p>
            </div>

        </form>
    </div>
</body>
</html>