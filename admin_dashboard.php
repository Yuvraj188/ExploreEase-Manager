<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    // Redirect to the login page if not logged in or not an admin
    header("Location: login.php");
    exit();
}

$db_hostname = "localhost:3306";
$db_username = "root";           //Replace with your username
$db_password = "";
$db_name = "tours";             //Replace with your database name

$connection = new mysqli($db_hostname, $db_username, $db_password, $db_name);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// CRUD operations
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["updateStatus"])) {
        $id_to_update = $_POST["updateStatus"];

        // Check if 'status' column exists in the adventure_bookings table
        if ($connection->query("DESCRIBE adventure_bookings status")->num_rows > 0) {
            $new_status = isset($_POST["status"]) ? $_POST["status"] : '';
            $reject_reason = isset($_POST["rejectReason"]) ? $_POST["rejectReason"] : '';

            $update_status_query = "UPDATE adventure_bookings SET status = '$new_status', rejectReason = '$reject_reason' WHERE id = $id_to_update";
            $connection->query($update_status_query);
        } else {
            echo "Error: 'status' column does not exist in the adventure_bookings table.";
        }
    }
}

// Search filter
$search_term = isset($_POST['search']) ? $_POST['search'] : '';
$search_condition = empty($search_term) ? '' : "WHERE name LIKE '%$search_term%' OR email LIKE '%$search_term%' OR phone LIKE '%$search_term%' OR location LIKE '%$search_term%' OR adventureName LIKE '%$search_term' OR participantCount LIKE '%$search_term%' OR bookingDate LIKE '%$search_term%' OR status LIKE '%$search_term%'";

// Fetch adventure bookings with search filter
$select_query = "SELECT * FROM adventure_bookings $search_condition";
$result = $connection->query($select_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .table th,
        .table td {
            text-align: center;
        }

        .btn-sm {
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="mb-5">Admin Dashboard</h2>

        <div class="row">
            <div class="col-12">
                <a href="index.html" class="btn btn-danger float-right">Logout</a>
            </div>
        </div>
        <!-- Search form -->
        <form method="post" class="mb-3">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Search by name, email, phone, location, participants, booking date, or status" value="<?= $search_term ?>">
                </div>
                <div class="form-group col-md-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Adventure Name</th>
                        <th>Total Participants</th>
                        <th>Notes</th>
                        <th>Document</th>
                        <th>Booking Date</th>
                        <th>Status</th>
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
                            <td>
                                <form method="post">
                                    <input type="hidden" name="updateStatus" value="<?= $row['id'] ?>">
                                    <div class="form-group">
                                        <select class="form-control" name="status" required>
                                            <option value="Pending" <?= ($row['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                            <option value="Accepted" <?= ($row['status'] == 'Accepted') ? 'selected' : '' ?>>Accepted</option>
                                            <option value="Resubmit" <?= ($row['status'] == 'Resubmit') ? 'selected' : '' ?>>Resubmit</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="rejectReason" placeholder="Reject Reason">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
