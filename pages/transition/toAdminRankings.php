<?php
session_start();

require_once('../../backend/db/db.php');
require_once('../../backend/page_controller.php');

PageController::init(true);
$conn = DB::openConnection();

$sql = 'CREATE OR REPLACE TABLE hero_temp AS SELECT * FROM hero';
mysqli_query($conn, $sql);

PageController::setCanAccess(true, 'adminRankings.php');
header('Location: ../adminRankings.php');
exit();