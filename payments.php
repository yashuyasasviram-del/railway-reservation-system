<?php
include "db_connect.php";

$message = "";
$message_type = "";

if (isset($_POST['make_payment'])) {

    $ticket_id = (int)$_POST['ticket_id'];
    $amount = (float)$_POST['amount'];
    $payment_mode = trim($_POST['payment_mode']);

    // Check whether ticket exists
    $check = $conn->prepare(
        "SELECT ticket_id FROM tickets WHERE ticket_id = ?"
    );

    $check->bind_param("i", $ticket_id);
    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows == 0) {

        $message = "Ticket ID not found!";
        $message_type = "error";

    } else {

        // Insert payment
        $sql = "INSERT INTO payments
                (ticket_id, amount, payment_mode, payment_date)
                VALUES (?, ?, ?, CURDATE())";

        $stmt = $conn->prepare($sql);

        if ($stmt) {

            $stmt->bind_param(
                "ids",
                $ticket_id,
                $amount,
                $payment_mode
            );

            if ($stmt->execute()) {

                $message = "Payment successful!";
                $message_type = "success";

            } else {

                $message = "Payment error: " . $stmt->error;
                $message_type = "error";
            }

            $stmt->close();

        } else {

            $message = "Database error: " . $conn->error;
            $message_type = "error";
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

<title>Payments</title>

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

.form-box {
    background: white;
    width: 450px;
    max-width: 100%;
    margin: 30px auto;
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

input,
select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    background: #1565c0;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    background: #0d47a1;
}

.message {
    text-align: center;
    font-weight: bold;
    margin: 20px;
}

.success {
    color: green;
}

.error {
    color: red;
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
    width: 90%;
    min-width: 650px;
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

</style>

</head>

<body>

<h1>Payment</h1>

<?php

if ($message != "") {

    echo "<div class='message $message_type'>"
        . htmlspecialchars($message)
        . "</div>";
}

?>

<div class="form-box">

<form method="POST">

    <label>Ticket ID</label>

    <input
        type="number"
        name="ticket_id"
        placeholder="Enter ticket ID"
        required
    >

    <label>Amount</label>

    <input
        type="number"
        name="amount"
        placeholder="Enter amount"
        min="1"
        step="0.01"
        required
    >

    <label>Payment Method</label>

    <select name="payment_mode" required>

        <option value="">Select Payment Method</option>

        <option value="UPI">UPI</option>

        <option value="Card">Card</option>

        <option value="Cash">Cash</option>

    </select>

    <button type="submit" name="make_payment">
        Make Payment
    </button>

</form>

</div>


<h2>Payment History</h2>

<div class="table-container">

<table>

<tr>
    <th>Payment ID</th>
    <th>Ticket ID</th>
    <th>Amount</th>
    <th>Payment Mode</th>
    <th>Payment Date</th>
</tr>

<?php

$sql = "SELECT
            payment_id,
            ticket_id,
            amount,
            payment_mode,
            payment_date
        FROM payments
        ORDER BY payment_id DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        echo "<tr>";

        echo "<td>" .
            htmlspecialchars($row['payment_id']) .
            "</td>";

        echo "<td>" .
            htmlspecialchars($row['ticket_id']) .
            "</td>";

        echo "<td>₹" .
            htmlspecialchars($row['amount']) .
            "</td>";

        echo "<td>" .
            htmlspecialchars($row['payment_mode']) .
            "</td>";

        echo "<td>" .
            htmlspecialchars($row['payment_date']) .
            "</td>";

        echo "</tr>";
    }

} else {

    echo "<tr>";

    echo "<td colspan='5'>No payment records found</td>";

    echo "</tr>";
}

?>

</table>

</div>


<a class="back" href="index.php">
    Back to Home
</a>

</body>

</html>