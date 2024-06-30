<?php
session_start();
$_SESSION = [];


header('Location: frontend/login.php');
exit();