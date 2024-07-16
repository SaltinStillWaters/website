<?php
require_once('../backend/db/DB_ranking.php');
require_once('../backend/db/db.php');
function generateTable($table)
{
    foreach ($table as $row)
    {
        echo "<tr>";
        echo "<td><input type='text' name='hero_name[]' value='" . $row[0] . "'></td>";
        echo "<td><input type='text' name='hero_win_rate[]' value='" . $row[1] . "'></td>";
        echo "<td><input type='text' name='hero_pick_rate[]' value='" . $row[2] . "'></td>";
        echo "<td><input type='text' name='hero_ban_rate[]' value='" . $row[3] . "'></td>";
        echo "<td><button type='submit' name='remove[]' value='" .$row[0] . "'>X</button></td>";
        echo "</tr>";
    }

    if (isset($_POST['add']))
    {
        echo "<tr>";
        echo "<td><input type='text' name='hero_name[]' value=''></td>";
        echo "<td><input type='text' name='hero_win_rate[]' value=''></td>";
        echo "<td><input type='text' name='hero_pick_rate[]' value=''></td>";
        echo "<td><input type='text' name='hero_ban_rate[]' value=''></td>";
        echo "<td><button type='submit' name='remove[]' value=''>X</button></td>";
        echo "</tr>";
    }
}

function removeRow($name)
{
    $sql = 'DELETE FROM hero_temp where hero_name = "' . "$name" . '"';
    $conn = DB::openConnection();

    mysqli_query($conn, $sql);
}

function addRow()
{

}
function updateDB()
{
    $hero_names = $_POST['hero_name'];
    $win_rates = $_POST['hero_win_rate'];
    $pick_rates = $_POST['hero_pick_rate'];
    $ban_rates = $_POST['hero_ban_rate'];

    for ($i = 0; $i < count($hero_names); $i++) 
    {
        $win_rate = str_replace(array(' ', '%'), '', $win_rates[$i]);
        $pick_rate = str_replace(array(' ', '%'), '', $pick_rates[$i]);
        $ban_rate = str_replace(array(' ', '%'), '', $ban_rates[$i]);

        $pattern = "/^100$|^\d{1,2}(\.\d+)?$/";
        if (preg_match($pattern, $win_rate) && preg_match($pattern, $pick_rate) && preg_match($pattern, $ban_rate))
        {
            $win_rate = number_format((float)$win_rate, 2) . '%';
            $pick_rate = number_format((float)$pick_rate, 2) . '%';
            $ban_rate = number_format((float)$ban_rate, 2) . '%';
            
            $win_rates[$i] = $win_rate;
            $pick_rates[$i] = $pick_rate;
            $ban_rates[$i] = $ban_rate;
        }
        else
        {
            echo 'INVALID INPUT AT: ' . $hero_names[$i] . "<br><br>";
            return false;
        }

    }

    $conn = DB::openConnection();
    
    for ($i = 0; $i < count($hero_names); $i++) 
    {
        $hero_name = $hero_names[$i];
        $win_rate = $win_rates[$i];
        $pick_rate = $pick_rates[$i];
        $ban_rate = $ban_rates[$i];

        $sql = "UPDATE hero_temp SET hero_win_rate='$win_rate', hero_pick_rate='$pick_rate', hero_ban_rate='$ban_rate' WHERE hero_name='$hero_name'";

        mysqli_query($conn, $sql);
    }

    if (isset($_POST['addSubmit']))
    {
        $sql = "INSERT INTO hero_temp VALUES ('" . end($hero_names) . "', '" . end($win_rates) . "', '" . end($pick_rates) . "', '" . end($ban_rates) . "')";

        mysqli_query($conn, $sql);
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if (isset($_POST['update']) || isset($_POST['addSubmit']))
    {
        updateDB();
    }   
    else if (isset($_POST['remove']))
    {
        removeRow($_POST['remove'][0]);
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
    generateTable(DB_ranking::getTable('hero_temp'));
    ?>
</table>
<?php
    if (isset($_POST['add']))
    {
        echo "<input type='hidden' name='addSubmit' value='Add Row'>";
    }
?>
    <input type='submit' name='add' value='Add Row'>
    <input type='submit' name='update' value='Update'>
    <input type='submit' name='discard' value='Discard'>
</form>
</body>
</html>