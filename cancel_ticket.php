
<?php
include "db_connect.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ticket_id = $_POST["ticket_id"];

    // Check whether ticket exists
    $check = $conn->prepare(
        "SELECT ticket_id, status FROM tickets WHERE ticket_id = ?"
    );

    $check->bind_param("i", $ticket_id);
    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows == 0) {

        $message = "Ticket not found!";
        $messageType = "error";

    } else {

        $ticket = $result->fetch_assoc();

        if ($ticket["status"] == "Cancelled") {

            $message = "Ticket is already cancelled!";
            $messageType = "error";

        } elseif ($ticket["status"] != "Booked") {

            $message = "Only booked tickets can be cancelled!";
            $messageType = "error";

        } else {

            // Cancel ticket using stored procedure
            $stmt = $conn->prepare("CALL cancel_ticket(?)");

            $stmt->bind_param("i", $ticket_id);

            if ($stmt->execute()) {

                $message = "Ticket cancelled successfully!";
                $messageType = "success";

            } else {

                $message = "Cancellation failed!";
                $messageType = "error";
            }

            $stmt->close();

            // Clear remaining results
            while ($conn->more_results()) {
                $conn->next_result();
            }
        }
    }

    $check->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cancel Ticket</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .back-btn {
            display: block;
            width: 120px;
            margin: 25px auto;
            text-align: center;
        }

    </style>

</head>

<body>

<header>

    <h1>Railway Reservation System</h1>

    <p>Cancel Ticket</p>

</header>


<div class="form-box">

    <h2>Cancel Ticket</h2>


    <?php if ($message != "") { ?>

        <p class="<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </p>

    <?php } ?>


    <form method="POST">

        <label>Ticket ID</label>

        <input
            type="number"
            name="ticket_id"
            placeholder="Enter Ticket ID"
            required
        >

        <button type="submit">
            Cancel Ticket
        </button>

    </form>

</div>


<a href="index.php" class="back-btn">
    Home
</a>


</body>

</html>
```
