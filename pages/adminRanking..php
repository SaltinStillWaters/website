<?php
require_once('../backend/db/DB_ranking.php');
require_once('../backend/db/db.php');
var_dump($_POST);
function generateTable($table)
{
    foreach ($table as $row)
    {
        echo "<tr>";
        echo "<td><input type='text' name='hero_name[]' value='" . $row[0] . "' readonly></td>";
        echo "<td><input type='text' name='hero_win_rate[]' value='" . $row[1] . "'></td>";
        echo "<td><input type='text' name='hero_pick_rate[]' value='" . $row[2] . "'></td>";
        echo "<td><input type='text' name='hero_ban_rate[]' value='" . $row[3] . "'></td>";
        echo "</tr>";
    }
}

function checkData()
{
    $hero_names = $_POST['hero_name'];
    $win_rates = $_POST['hero_win_rate'];
    $pick_rates = $_POST['hero_pick_rate'];
    $ban_rates = $_POST['hero_ban_rate'];

    for ($i = 0; $i < count($hero_names); $i++) 
    {
        $win_rate = $win_rates[$i];
        $pick_rate = $pick_rates[$i];
        $ban_rate = $ban_rates[$i];

        $pattern = "/^(100\.00|[0-9]{1,2}\.[0-9]{2})%$/";
        if (preg_match($pattern, $win_rate) && preg_match($pattern, $pick_rate) && preg_match($pattern, $ban_rate)) 
        {
        }
        else
        {
            return false;
        }
    }

    return true;
}

function updateDB()
{
    $hero_names = $_POST['hero_name'];
    $win_rates = $_POST['hero_win_rate'];
    $pick_rates = $_POST['hero_pick_rate'];
    $ban_rates = $_POST['hero_ban_rate'];

    for ($i = 0; $i < count($hero_names); $i++) 
    {
        $hero_name = $hero_names[$i];
        $win_rate = $win_rates[$i];
        $pick_rate = $pick_rates[$i];
        $ban_rate = $ban_rates[$i];

        $conn = DB::openConnection();
        $sql = "UPDATE hero SET hero_win_rate='$win_rate', hero_pick_rate='$pick_rate', hero_ban_rate='$ban_rate' WHERE hero_name='$hero_name'";

        mysqli_query($conn, $sql);
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    echo 'request method';
    if (isset($_POST['update']))
    {
        echo 'update';
        if (checkData())
        {
            echo 'update db';
            updateDB();
        }
        else
        {
            echo 'INPUT CAN ONLY BE: 0.00% - 100.00%';
        }
    }
    else
    {
        echo 'no update';
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form method="post">
<table>
    <?php
    generateTable(DB_ranking::getTable());
    ?>
</table>
<input type='submit' name='update' value="Update">
</form>
</body>
</html>