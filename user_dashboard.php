<?php
session_start();

// Check if the user clicks the logout button
if (isset($_GET['logout'])) {
    // Destroy the session and redirect to the login page
    session_destroy();
    header("Location: login.php");
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'user') {
    // Redirect to the login page if not logged in or not a regular user
    header("Location: login.php");
    exit();
}

$db_hostname = "localhost:3306";
$db_username = "root";
$db_password = "";
$db_name = "tours";

$connection = new mysqli($db_hostname, $db_username, $db_password, $db_name);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Get user email from the session
$user_email = $_SESSION['user']['email'];

// Fetch all bookings for the logged-in user
$select_query = "SELECT * FROM adventure_bookings WHERE email = '$user_email'";
$result = $connection->query($select_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">User Dashboard</h2>

        <!-- Logout button -->
        <div class="float-right col-12">
            <a href="index.html" class="btn btn-danger">Logout</a>
        </div>

        <div class="row">
            <div class="col-12">
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Location</th>
                            <th>Adventure Name</th>
                            <th>No. of Participants</th>
                            <th>Notes</th>
                            <th>Document</th>
                            <th>Booking Date</th>
                            <th>Status</th>
                            <th>Reject Reason</th>
                            <th>Action</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td><?= $row['phone'] ?></td>
                                <td><?= $row['location'] ?></td>
                                <td><?= $row['adventureName'] ?></td>
                                <td><?= $row['participantCount'] ?></td>
                                <td><?= $row['notes'] ?></td>
                                <td>
                                    <?php if (!empty($row['document'])) : ?>
                                        <a href="<?= $row['document'] ?>" target="_blank">View Document</a>
                                    <?php else : ?>
                                        No Document
                                    <?php endif; ?>
                                </td>
                                <td><?= $row['bookingDate'] ?></td>
                                <td><?= $row['status'] ?></td>
                                <td><?= $row['rejectReason'] ?></td>
                                <td>
                                    <?php if ($row['status'] === 'Resubmit') : ?>
                                        <a href="edit_document.php?id=<?= $row['id'] ?>">Edit Document</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </div>
</body>

</html>
