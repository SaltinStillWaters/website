<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hero_ranking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve data
$sql = "SELECT * FROM heroes ORDER BY rank";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Rankings</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Hero Rankings</h1>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Rank</th>
                <th>Hero</th>
                <th>Pick Rate (%)</th>
                <th>Win Rate (%)</th>
                <th>Ban Rate (%)</th>
                <th>Counter Hero</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['rank']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['pick_rate']}</td>
                            <td>{$row['win_rate']}</td>
                            <td>{$row['ban_rate']}</td>
                            <td>{$row['counter_hero']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No data available</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
