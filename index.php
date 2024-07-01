<?php
session_start();
$_SESSION = [];

require_once('backend/db/db.php');

DB::createDB();
DB::createUserTable();
DB::createForumsTables();

header('Location: frontend/login.php');
exit();