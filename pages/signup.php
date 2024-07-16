<?php
session_start();
require_once('../backend/form.php');
require_once('../backend/db/db.php');
require_once('../backend/page_controller.php');
PageController::init();

Form::$SESSION_NAME = 'user';
Form::init();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    Form::updateContents();
    Form::updateErrors();

    if (!Form::hasErrors())
    {
        if (DB::addNewUser($_SESSION[Form::$SESSION_NAME], 'user'))
        {
            unset($_SESSION[Form::$SESSION_NAME]);
            header('Location: login.php');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Felper</title>
    <link href="../css/pages/login_signup.css" rel="stylesheet">
    <link href="../css/dependencies/boxicons-2.1.4/css/boxicons.min.css" rel='stylesheet'>
    <script src="../backend/js/utils.js"></script>
</head>
<body>
    <div class="wrapper">
        <form method="post">
            <h1>Sign up</h1>
            <?php
            Form::inputText('email', Type::$Email, 'Email', "<i class='bx bxs-envelope'></i>", true, true);
            Form::inputText('name', Type::$Text, 'Username', "<i class='bx bxs-user'></i>", true, true);
            Form::inputPassword('password', 'Password', "<i class='bx bxs-lock-alt'></i>", true);
            Form::inputPassword('password_confirm', 'Confrim Password', "<i class='bx bxs-lock-alt'></i>", true);
            ?>

            <button type="submit" class="btn">Register</button>

            <div class="register-link">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>

        </form>
    </div>
</body>
</html>