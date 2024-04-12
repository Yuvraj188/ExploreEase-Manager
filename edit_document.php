<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'user') {
    // Redirect to the login page if not logged in or not a regular user
    header("Location: login.php");
    exit();
}

$db_hostname = "localhost:3306";
$db_username = "root";              //Replace wtih your username
$db_password = "";
$db_name = "tours";                 //Replace with your database name

$connection = new mysqli($db_hostname, $db_username, $db_password, $db_name);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Get user email from the session
$user_email = $_SESSION['user']['email'];

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $booking_id = $_POST['booking_id'];

    // Check if a file is selected
    if (isset($_FILES['new_document']) && $_FILES['new_document']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['new_document']['name']);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['new_document']['tmp_name'], $target_file)) {
            // Update the database with the new document path
            $update_query = "UPDATE adventure_bookings SET document = '$target_file' WHERE id = $booking_id";
            $connection->query($update_query);
            
            // Redirect back to user dashboard
            header("Location: user_dashboard.php");
            exit();
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "No file selected.";
    }
}

if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    // Fetch booking details
    $select_query = "SELECT * FROM adventure_bookings WHERE id = $booking_id AND email = '$user_email'";
    $result = $connection->query($select_query);

    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
    } else {
        echo "Booking not found.";
        exit();
    }
} else {
    echo "Booking ID not provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Edit Document</h2>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
            
            <div class="form-group">
                <label for="new_document">Select New Document:</label>
                <input type="file" class="form-control-file" id="new_document" name="new_document" accept=".pdf, .doc, .docx">
            </div>

            <button type="submit" class="btn btn-primary">Upload Document</button>
        </form>

        <br>
        <a href="user_dashboard.php">Back to User Dashboard</a>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
