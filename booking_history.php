<?php
include "db_connect.php";

$result = $conn->query("
    SELECT 
        t.ticket_id,
        p.passenger_name,
        tr.train_name,
        tr.source,
        tr.destination,
        t.journey_date,
        t.seat_no,
        t.status
    FROM tickets t
    JOIN passengers p ON t.passenger_id = p.passenger_id
    JOIN trains tr ON t.train_id = tr.train_id
    ORDER BY t.ticket_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking History</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <h1>Railway Reservation System</h1>
    <p>Booking History</p>
</header>

<div class="container">

    <h2>All Booking History</h2>

    <table>
        <tr>
            <th>Ticket ID</th>
            <th>Passenger</th>
            <th>Train</th>
            <th>Source</th>
            <th>Destination</th>
            <th>Journey Date</th>
            <th>Seat No</th>
            <th>Status</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {
        ?>

        <tr>
            <td><?php echo $row["ticket_id"]; ?></td>
            <td><?php echo $row["passenger_name"]; ?></td>
            <td><?php echo $row["train_name"]; ?></td>
            <td><?php echo $row["source"]; ?></td>
            <td><?php echo $row["destination"]; ?></td>
            <td><?php echo $row["journey_date"]; ?></td>
            <td><?php echo $row["seat_no"]; ?></td>
            <td><?php echo $row["status"]; ?></td>
        </tr>

        <?php
            }

        } else {
        ?>

        <tr>
            <td colspan="8">No booking history found.</td>
        </tr>

        <?php } ?>

    </table>

    <br>

    <a href="index.php" class="back-btn">Back to Home</a>

</div>

</body>
</html>