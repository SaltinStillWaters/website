<?php
session_start();
$_SESSION = [];


header('Location: frontend/signup.php');
exit();