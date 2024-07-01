<?php
session_start();
$_SESSION = [];

require_once('backend/db/db.php');



header('Location: frontend/login.php');
exit();