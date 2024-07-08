<?php
require_once('../backend/db/DB_ranking.php');
function generateTable($table)
{
    $ctr = 1;

    foreach ($table as $row)
    {
        echo "<tr>";
        echo "<td> $ctr </td>"; 
        echo "<td> <img src='../resources/ranking/" . $row[0] . ".png' alt=''>" . $row[0] . "</td>";
        echo "<td> $row[1] </td> <td> $row[2] </td> <td> $row[3] </td>";
        echo "</tr>";
        ++$ctr;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Convert | Export html Table to CSV & EXCEL File</title>
    <link rel="stylesheet" type="text/css" href="../css/base/base.css">
    <link rel="stylesheet" type="text/css" href="../css/layout/header.css">
    <link rel="stylesheet" type="text/css" href="../css/pages/rankings.css">
</head>

<body>
    <header>
            <a href="welcome.php" class="logo">ml companion</a>
            <ul>
                <li><a href="#">Strategy Guides</a></li>
                <li><a href="rankings.php">Hero Rankings</a></li>
                <li><a href="#">Counter Picking</a></li>
                <li><a href='forum.php'>Forums</a></li>
                <div class="logout">                
                    <li><a href="logout.php">Log out</a></li>
                </div>
            </ul>
        </header>

    <main class="table" id="customers_table">

        <section class="table__header">
            <h1>Hero Ranking</h1>
            <div class="input-group">
                <input type="search" placeholder="Search...">
                <img src="../resources/ranking/search.png" alt="">
            </div>
            <div class="export__file">
                <label for="export-file" class="export__file-btn" title="Export File"></label>
                <input type="checkbox" id="export-file">
                <div class="export__file-options">
                    <label>Export As &nbsp; &#10140;</label>
                    <label for="export-file" id="toPDF">PDF <img src="../resources/ranking/pdf.png" alt=""></label>
                    <label for="export-file" id="toJSON">JSON <img src="../resources/ranking/json.png" alt=""></label>
                    <label for="export-file" id="toCSV">CSV <img src="../resources/ranking/csv.png" alt=""></label>
                    <label for="export-file" id="toEXCEL">EXCEL <img src="../resources/ranking/excel.png" alt=""></label>
                </div>
            </div>
        </section>

        <section class="table__body">
            <table>

                <thead>
                    <tr>
                        <th> # </th>
                        <th> Hero <span class="icon-arrow">&UpArrow;</span></th>
                        <th> Win Rate <span class="icon-arrow">&UpArrow;</span></th>
                        <th> Pick Rate <span class="icon-arrow">&UpArrow;</span></th>
                        <th> Ban Rate <span class="icon-arrow">&UpArrow;</span></th>
                    </tr>
                </thead>

                <tbody>
<?php

    generateTable(DB_ranking::getTable());

?>
                </tbody>
            </table>
        </section>

    </main>


    <script src="../backend/js/ranking.js"></script>

</body>

</html>

<?php



exit();
require_once('../backend/db/DB_ranking.php');

var_dump(DB_ranking::getTable());