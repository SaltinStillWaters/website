<?php
session_start();
$_SESSION = [];

header('Location: signup.php');
exit();