<?php

include "db_connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $passenger_id = $_POST["passenger_id"];
    $train_id = $_POST["train_id"];
    $journey_date = $_POST["journey_date"];
    $seat_no = $_POST["seat_no"];

    $status = "Booked";

    $sql = "INSERT INTO tickets
            (passenger_id, train_id, journey_date, seat_no, status)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iisss",
        $passenger_id,
        $train_id,
        $journey_date,
        $seat_no,
        $status
    );

    if ($stmt->execute()) {

        $message = "Ticket booked successfully!";

    } else {

        $message = "Error: " . $stmt->error;

    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Book Ticket</title>
</head>

<body>

<h1>Book Ticket</h1>

<form method="POST">

    <label>Passenger ID:</label>

    <input type="number"
           name="passenger_id"
           required>

    <br><br>

    <label>Train ID:</label>

    <input type="number"
           name="train_id"
           required>

    <br><br>

    <label>Journey Date:</label>

    <input type="date"
           name="journey_date"
           required>

    <br><br>

    <label>Seat Number:</label>

    <input type="text"
           name="seat_no"
           placeholder="S1-25"
           required>

    <br><br>

    <button type="submit">
        Book Ticket
    </button>

</form>

<br>

<?php echo $message; ?>

<br><br>

<a href="tickets.php">View Tickets</a>

<br><br>

<a href="index.php">Back to Home</a>

</body>

</html>