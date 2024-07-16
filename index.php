<?php
session_start();
$_SESSION = [];

$sql = "CREATE OR REPLACE TABLE HERO(
    hero_name varchar(255) PRIMARY KEY,
    hero_win_rate varchar(7) NOT NULL,
    hero_pick_rate varchar(7) NOT NULL,
    hero_ban_rate varchar(7) NOT NULL
)";

require_once('backend/db/db.php');
$conn = DB::openConnection();

mysqli_query($conn, $sql);

$sql = "INSERT INTO HERO (hero_name, hero_win_rate, hero_pick_rate, hero_ban_rate) VALUES
('Lolita', '61.24%', '0.20%', '2.17%'),
('Hylos', '56.56%', '0.51%', '0.53%'),
('Popol and Kupa', '56.46%', '0.67%', '3.84%'),
('Argus', '55.98%', '0.49%', '2.01%'),
('Edith', '55.57%', '0.57%', '0.20%'),
('Carmilla', '55.12%', '0.21%', '0.39%'),
('Floryn', '55.11%', '0.57%', '11.79%'),
('Masha', '55.05%', '0.15%', '0.78%'),
('Belerick', '54.62%', '0.79%', '6.44%'),
('Terizla', '54.39%', '1.23%', '1.51%'),
('Gatotkaca', '54.13%', '2.04%', '5.58%'),
('Gloo', '54.12%', '0.11%', '0.43%'),
('Ling', '53.73%', '0.95%', '28.74%'),
('Yve', '53.71%', '0.05%', '0.13%'),
('Bane', '53.64%', '0.94%', '0.92%'),
('Khaleed', '53.40%', '0.28%', '0.16%'),
('Yi Sun-shin', '53.27%', '0.16%', '0.10%'),
('Ruby', '53.24%', '0.67%', '0.31%');
";



mysqli_query($conn, $sql);

DB::createDB();
DB::createUserTable();
DB::createForumsTables();

header('Location: pages/login.php');
exit();