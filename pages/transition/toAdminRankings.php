<?php
require_once('../../backend/db/db.php');
$conn = DB::openConnection();

$sql = 'CREATE OR REPLACE TABLE hero_temp AS SELECT * FROM hero';
mysqli_query($conn, $sql);

header('Location: ../adminRankings.php');
exit();