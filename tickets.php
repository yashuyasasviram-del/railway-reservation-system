<?php
include "db_connect.php";

$message = "";
$message_type = "";

if (isset($_POST['book_ticket'])) {

    $passenger_name = trim($_POST['passenger_name']);
    $train_number = trim($_POST['train_number']);
    $journey_date = $_POST['journey_date'];
    $seat_no = trim($_POST['seat_no']);

    /* Find passenger */
    $sql = "SELECT passenger_id, passenger_name
            FROM passengers
            WHERE passenger_name = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $passenger_name);
    $stmt->execute();

    $passenger_result = $stmt->get_result();

    /* Find train */
    $sql2 = "SELECT train_id, train_name, source, destination
             FROM trains
             WHERE train_id = ?";

    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $train_number);
    $stmt2->execute();

    $train_result = $stmt2->get_result();

    if ($passenger_result->num_rows == 0) {

        $message = "Passenger not found! Please enter the correct name.";
        $message_type = "error";

    } elseif ($train_result->num_rows == 0) {

        $message = "Train number not found! Please enter a valid train number.";
        $message_type = "error";

    } else {

        $passenger = $passenger_result->fetch_assoc();
        $train = $train_result->fetch_assoc();

        $passenger_id = $passenger['passenger_id'];
        $train_id = $train['train_id'];

        /* Book ticket using stored procedure */

        try {

            $stmt3 = $conn->prepare(
                "CALL book_ticket(?, ?, ?, ?)"
            );

            $stmt3->bind_param(
                "iiss",
                $passenger_id,
                $train_id,
                $journey_date,
                $seat_no
            );

            if ($stmt3->execute()) {

                $message = "Ticket booked successfully!";
                $message_type = "success";

            }

            $stmt3->close();

            /* Clear remaining results from CALL */
            while ($conn->more_results()) {
                $conn->next_result();
            }

        } catch (mysqli_sql_exception $e) {

            if (
                strpos(
                    $e->getMessage(),
                    "Seat is already booked"
                ) !== false
            ) {

                $message =
                    "❌ Seat is already booked for this train and journey date!";

                $message_type = "error";

            } else {

                $message =
                    "❌ Booking failed: " . $e->getMessage();

                $message_type = "error";
            }
        }
    }

    $stmt->close();
    $stmt2->close();
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Ticket Details</title>

<style>

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
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 5px;
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
    font-weight: bold;
    margin: 15px auto;
    padding: 12px;
    width: 500px;
    border-radius: 5px;
}

.success {
    color: green;
    background: #e8f5e9;
}

.error {
    color: red;
    background: #ffebee;
}

h2 {
    text-align: center;
    color: #1565c0;
    margin-top: 40px;
}

table {
    margin: 20px auto;
    background: white;
    border-collapse: collapse;
    width: 100%;
}

th {
    background: #1565c0;
    color: white;
}

th, td {
    border: 1px solid #ccc;
    padding: 9px;
    text-align: center;
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

</style>

</head>

<body>

<h1>Ticket Details</h1>

<?php

if ($message != "") {

    echo "<div class='message $message_type'>"
        . htmlspecialchars($message)
        . "</div>";
}

?>

<div class="box">

<form method="POST">

<label>Passenger Name</label>

<input
    type="text"
    name="passenger_name"
    placeholder="Enter passenger name"
    required
>

<label>Train Number</label>

<input
    type="number"
    name="train_number"
    placeholder="Enter train number"
    required
>

<label>Journey Date</label>

<input
    type="date"
    name="journey_date"
    required
>

<label>Seat Number</label>

<input
    type="text"
    name="seat_no"
    placeholder="Enter seat number"
    required
>

<button type="submit" name="book_ticket">
    Book Ticket
</button>

</form>

</div>

<!-- SAVED TICKET DETAILS -->

<h2>Booked Ticket Details</h2>

<table>

<tr>
    <th>Ticket ID</th>
    <th>Passenger</th>
    <th>Train No.</th>
    <th>Train Name</th>
    <th>From</th>
    <th>To</th>
    <th>Journey Date</th>
    <th>Seat</th>
    <th>Status</th>
</tr>

<?php

$sql = "
SELECT
    t.ticket_id,
    p.passenger_name,
    tr.train_id,
    tr.train_name,
    tr.source,
    tr.destination,
    t.journey_date,
    t.seat_no,
    t.status
FROM tickets t
INNER JOIN passengers p
    ON t.passenger_id = p.passenger_id
INNER JOIN trains tr
    ON t.train_id = tr.train_id
ORDER BY t.ticket_id DESC
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        echo "<tr>";

        echo "<td>" .
            htmlspecialchars($row['ticket_id']) .
            "</td>";

        echo "<td>" .
            htmlspecialchars($row['passenger_name']) .
            "</td>";

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
            htmlspecialchars($row['journey_date']) .
            "</td>";

        echo "<td>" .
            htmlspecialchars($row['seat_no']) .
            "</td>";

        echo "<td>" .
            htmlspecialchars($row['status']) .
            "</td>";

        echo "</tr>";
    }

} else {

    echo "<tr>";

    echo "<td colspan='9'>
            No ticket details found
          </td>";

    echo "</tr>";
}

?>

</table>

<a class="back" href="index.php">
    Back to Home
</a>

</body>

</html>