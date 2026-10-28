<?php
include "db_connect.php";

$message = "";

/* =========================
   ADD TRAIN
========================= */

if (isset($_POST['add_train'])) {

    $train_name = trim($_POST['train_name']);
    $source = trim($_POST['source']);
    $destination = trim($_POST['destination']);
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];

    $sql = "INSERT INTO trains
            (train_name, source, destination, departure_time, arrival_time)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "sssss",
            $train_name,
            $source,
            $destination,
            $departure_time,
            $arrival_time
        );

        if ($stmt->execute()) {
            $message = "Train added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Train Details</title>

<style>

* {
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f2f6ff;
    margin: 0;
    padding: 30px;
}

h1 {
    text-align: center;
    color: #1565c0;
}

.box {
    width: 500px;
    max-width: 100%;
    margin: 25px auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px #ccc;
}

label {
    display: block;
    font-weight: bold;
    margin-top: 15px;
    margin-bottom: 7px;
}

input {
    width: 100%;
    padding: 11px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 15px;
}

button {
    width: 100%;
    padding: 12px;
    margin-top: 22px;
    background: #1565c0;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background: #0d47a1;
}

.message {
    text-align: center;
    color: green;
    font-weight: bold;
    margin: 15px;
}

.error {
    text-align: center;
    color: red;
    font-weight: bold;
    margin: 15px;
}

h2 {
    text-align: center;
    color: #1565c0;
    margin-top: 40px;
}

.table-container {
    overflow-x: auto;
}

table {
    margin: 20px auto;
    background: white;
    border-collapse: collapse;
    width: 100%;
    min-width: 750px;
}

th {
    background: #1565c0;
    color: white;
}

th,
td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: center;
}

tr:nth-child(even) {
    background: #f8f8f8;
}

.back {
    display: block;
    width: fit-content;
    margin: 35px auto 0;
    background: #1565c0;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
}

.back:hover {
    background: #0d47a1;
}

</style>

</head>

<body>

<h1>Train Details</h1>


<?php

if ($message != "") {

    if (strpos($message, "successfully") !== false) {

        echo "<div class='message'>" .
             htmlspecialchars($message) .
             "</div>";

    } else {

        echo "<div class='error'>" .
             htmlspecialchars($message) .
             "</div>";
    }
}

?>


<!-- =========================
     ADD TRAIN FORM
========================= -->

<div class="box">

<form method="POST">

    <label>Train Name</label>

    <input
        type="text"
        name="train_name"
        placeholder="Enter train name"
        required
    >


    <label>Source</label>

    <input
        type="text"
        name="source"
        placeholder="Enter source station"
        required
    >


    <label>Destination</label>

    <input
        type="text"
        name="destination"
        placeholder="Enter destination station"
        required
    >


    <label>Departure Time</label>

    <input
        type="datetime-local"
        name="departure_time"
        required
    >


    <label>Arrival Time</label>

    <input
        type="datetime-local"
        name="arrival_time"
        required
    >


    <button type="submit" name="add_train">
        Add Train
    </button>

</form>

</div>


<!-- =========================
     AVAILABLE TRAINS
========================= -->

<h2>Available Trains</h2>

<div class="table-container">

<table>

<tr>

    <th>Train Number</th>

    <th>Train Name</th>

    <th>Source</th>

    <th>Destination</th>

    <th>Departure</th>

    <th>Arrival</th>

</tr>


<?php

$sql = "SELECT
            train_id,
            train_name,
            source,
            destination,
            departure_time,
            arrival_time
        FROM trains
        ORDER BY train_id";

$result = $conn->query($sql);


if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        echo "<tr>";

        echo "<td>" .
             htmlspecialchars($row['train_id']) .
             "</td>";

        echo "<td>" .
             htmlspecialchars($row['train_name']) .
             "</td>";

        echo "<td>" .
             htmlspecialchars($row['source']) .
             "</td>";

        echo "<td>" .
             htmlspecialchars($row['destination']) .
             "</td>";

        echo "<td>" .
             htmlspecialchars($row['departure_time']) .
             "</td>";

        echo "<td>" .
             htmlspecialchars($row['arrival_time']) .
             "</td>";

        echo "</tr>";
    }

} else {

    echo "<tr>";

    echo "<td colspan='6'>
            No train details found
          </td>";

    echo "</tr>";
}

?>

</table>

</div>


<!-- BACK TO HOME -->

<a class="back" href="index.php">
    Back to Home
</a>


</body>

</html>