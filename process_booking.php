<?php
$servername = "localhost:3306";           
$username = "root";            //Replace wtih your username
$password = "";
$dbname = "tours";            //Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sqlCreateTable = "CREATE TABLE IF NOT EXISTS adventure_bookings (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    location VARCHAR(50) NOT NULL,
    participantCount INT(3) NOT NULL,
    notes TEXT,
    document VARCHAR(255),
    bookingDate DATE,
    status VARCHAR(20) DEFAULT 'Pending', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sqlCreateTable) !== TRUE) {
    echo "Error creating table: " . $conn->error;
}

// Process the adventure booking form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $location = $_POST["location"];
    $participantCount = $_POST["participantCountConfirmation"];
    $notes = $_POST["notes"];
    $bookingDate = $_POST["bookingDate"];
    $adventureName = $_POST['adventureName'];

    // Check if the document is uploaded
    if (!empty($_FILES["document"]["name"])) {
        // File upload handling
        $allowedExtensions = ["jpg", "jpeg", "png", "pdf"];
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["document"]["name"]);
        $fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file type is allowed
        if (in_array($fileExtension, $allowedExtensions)) {
            move_uploaded_file($_FILES["document"]["tmp_name"], $targetFile);
            $document = $targetFile;
        } else {

            echo '<script>
                    alert("Error: Only JPG, JPEG, PNG, and PDF files are allowed.");
                    window.location.href = document.referrer;
                 </script>';
            exit();
        }
    } else {
        // Display a modal if the document is not uploaded
        echo '<script>
                alert("Please upload a document for ID proof.");
                window.location.href = document.referrer;
             </script>';
        exit();
    }
    
    $sql = "INSERT INTO adventure_bookings (name, email, phone, location, adventureName, participantCount, notes, document, bookingDate) VALUES ('$name', '$email', '$phone', '$location','$adventureName', '$participantCount', '$notes', '$document', '$bookingDate')";

    if ($conn->query($sql) === TRUE) {
        echo "Booking successful!<br><br>";
        // Add a button to redirect to adventures page
    echo '<a href="adventures.html" class="btn btn-primary">Explore more Adventures</a>'; 
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
