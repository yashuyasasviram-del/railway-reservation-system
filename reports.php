
<?php
include "db_connect.php";


// 1. Most Booked Trains
$mostBooked = $conn->query("
    SELECT 
        tr.train_id,
        tr.train_name,
        COUNT(t.ticket_id) AS total_bookings
    FROM trains tr
    JOIN tickets t
        ON tr.train_id = t.train_id
    WHERE t.status = 'Booked'
    GROUP BY tr.train_id, tr.train_name
    ORDER BY total_bookings DESC
");


// 2. Frequent Passengers
$frequentPassengers = $conn->query("
    SELECT
        p.passenger_id,
        p.passenger_name,
        COUNT(t.ticket_id) AS total_bookings
    FROM passengers p
    JOIN tickets t
        ON p.passenger_id = t.passenger_id
    WHERE t.status = 'Booked'
    GROUP BY p.passenger_id, p.passenger_name
    ORDER BY total_bookings DESC
");


// 3. Recent Cancellations
$recentCancellations = $conn->query("
    SELECT
        c.log_id,
        c.ticket_id,
        p.passenger_name,
        tr.train_name,
        c.cancelled_at
    FROM cancellation_log c
    JOIN tickets t
        ON c.ticket_id = t.ticket_id
    JOIN passengers p
        ON t.passenger_id = p.passenger_id
    JOIN trains tr
        ON t.train_id = tr.train_id
    ORDER BY c.cancelled_at DESC
");


// 4. Seat Occupancy
$seatOccupancy = $conn->query("
    SELECT
        train_id,
        train_name,
        occupied_seats
    FROM seat_occupancy_per_train
    ORDER BY occupied_seats DESC, train_id ASC
");


// 5. Active Passenger Bookings
$activeBookings = $conn->query("
    SELECT
        ticket_id,
        passenger_name,
        train_name,
        source,
        destination,
        journey_date,
        seat_no,
        status
    FROM active_passenger_bookings
    ORDER BY ticket_id DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reports & Analytics</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .report-container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
        }

        .report-box {
            background: white;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        .report-box h2 {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #f2f2f2;
        }

        .back-btn {
            display: block;
            width: 140px;
            margin: 25px auto;
            text-align: center;
        }

    </style>

</head>


<body>

<header>

    <h1>Railway Reservation System</h1>

    <p>Reports & Analytics</p>

</header>


<div class="report-container">


    <!-- Most Booked Trains -->

    <div class="report-box">

        <h2>1. Most Booked Trains</h2>

        <table>

            <tr>
                <th>Train ID</th>
                <th>Train Name</th>
                <th>Total Bookings</th>
            </tr>

            <?php while ($row = $mostBooked->fetch_assoc()) { ?>

            <tr>
                <td><?php echo $row["train_id"]; ?></td>
                <td><?php echo $row["train_name"]; ?></td>
                <td><?php echo $row["total_bookings"]; ?></td>
            </tr>

            <?php } ?>

        </table>

    </div>


    <!-- Frequent Passengers -->

    <div class="report-box">

        <h2>2. Frequent Passengers</h2>

        <table>

            <tr>
                <th>Passenger ID</th>
                <th>Passenger Name</th>
                <th>Total Bookings</th>
            </tr>

            <?php while ($row = $frequentPassengers->fetch_assoc()) { ?>

            <tr>
                <td><?php echo $row["passenger_id"]; ?></td>
                <td><?php echo $row["passenger_name"]; ?></td>
                <td><?php echo $row["total_bookings"]; ?></td>
            </tr>

            <?php } ?>

        </table>

    </div>


    <!-- Recent Cancellations -->

    <div class="report-box">

        <h2>3. Recent Cancellations</h2>

        <table>

            <tr>
                <th>Log ID</th>
                <th>Ticket ID</th>
                <th>Passenger</th>
                <th>Train</th>
                <th>Cancelled At</th>
            </tr>

            <?php while ($row = $recentCancellations->fetch_assoc()) { ?>

            <tr>
                <td><?php echo $row["log_id"]; ?></td>
                <td><?php echo $row["ticket_id"]; ?></td>
                <td><?php echo $row["passenger_name"]; ?></td>
                <td><?php echo $row["train_name"]; ?></td>
                <td><?php echo $row["cancelled_at"]; ?></td>
            </tr>

            <?php } ?>

        </table>

    </div>


    <!-- Seat Occupancy -->

    <div class="report-box">

        <h2>4. Seat Occupancy Per Train</h2>

        <table>

            <tr>
                <th>Train ID</th>
                <th>Train Name</th>
                <th>Occupied Seats</th>
            </tr>

            <?php while ($row = $seatOccupancy->fetch_assoc()) { ?>

            <tr>
                <td><?php echo $row["train_id"]; ?></td>
                <td><?php echo $row["train_name"]; ?></td>
                <td><?php echo $row["occupied_seats"]; ?></td>
            </tr>

            <?php } ?>

        </table>

    </div>


    <!-- Active Passenger Bookings -->

    <div class="report-box">

        <h2>5. Active Passenger Bookings</h2>

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

            <?php while ($row = $activeBookings->fetch_assoc()) { ?>

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

            <?php } ?>

        </table>

    </div>


</div>


<a href="index.php" class="back-btn">
    Back to Home
</a>


</body>

</html>
```
