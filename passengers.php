<?php
include "db_connect.php";

$message = "";

if (isset($_POST['add_passenger'])) {

    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];

    $sql = "INSERT INTO passengers
            (passenger_name, age, gender, phone)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("siss", $name, $age, $gender, $phone);

        if ($stmt->execute()) {
            $message = "Passenger added successfully!";
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
<html>

<head>

    <title>Passenger Details</title>

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
            width: 450px;
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

        input,
        select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
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
            cursor: pointer;
            font-size: 16px;
        }

        .message {
            text-align: center;
            color: green;
            font-weight: bold;
            margin: 15px;
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
            width: 90%;
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

<h1>Passenger Details</h1>

<?php

if ($message != "") {
    echo "<div class='message'>$message</div>";
}

?>

<div class="box">

    <form method="POST">

        <label>Passenger Name</label>

        <input
            type="text"
            name="name"
            placeholder="Enter passenger name"
            required
        >

        <label>Age</label>

        <input
            type="number"
            name="age"
            placeholder="Enter age"
            required
        >

        <label>Gender</label>

        <select name="gender" required>

            <option value="">Select Gender</option>

            <option value="Male">Male</option>

            <option value="Female">Female</option>

            <option value="Other">Other</option>

        </select>

        <label>Phone</label>

        <input
            type="text"
            name="phone"
            placeholder="Enter phone number"
            required
        >

        <button type="submit" name="add_passenger">
            Add Passenger
        </button>

    </form>

</div>


<h2>Saved Passenger Details</h2>

<table>

    <tr>

        <th>Passenger ID</th>
        <th>Name</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Phone</th>

    </tr>

<?php

$sql = "SELECT * FROM passengers ORDER BY passenger_id DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        echo "<tr>";

        echo "<td>" . htmlspecialchars($row['passenger_id']) . "</td>";

        echo "<td>" . htmlspecialchars($row['passenger_name']) . "</td>";

        echo "<td>" . htmlspecialchars($row['age']) . "</td>";

        echo "<td>" . htmlspecialchars($row['gender']) . "</td>";

        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";

        echo "</tr>";
    }

} else {

    echo "<tr>";

    echo "<td colspan='5'>No passenger details found</td>";

    echo "</tr>";
}

?>

</table>


<a class="back" href="index.php">
    Back to Home
</a>

</body>

</html>