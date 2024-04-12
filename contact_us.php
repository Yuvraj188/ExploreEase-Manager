<?php
// Database connection details
$db_hostname = "localhost:3306";
$db_username = "root";              //Replace wtih your username
$db_password = "";
$db_name = "tours";                 //Replace with your database name

// Create a connection to MySQL server
$connection = new mysqli($db_hostname, $db_username, $db_password);

// Check the connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Create the database if it doesn't exist
$create_db_query = "CREATE DATABASE IF NOT EXISTS $db_name";
if ($connection->query($create_db_query) === TRUE) {
    echo "";
} else {
    die("Error creating database: " . $connection->error);
}

// Select the database
$connection->select_db($db_name);

// Create the table if it doesn't exist
$create_table_query = "CREATE TABLE IF NOT EXISTS contact_us (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    location VARCHAR(255) NOT NULL,
    notes TEXT,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($connection->query($create_table_query) === TRUE) {
    echo "";
} else {
    die("Error creating table: " . $connection->error);
}

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$location = $_POST['location'];
$notes = $_POST['notes'];

// Insert the form data into the database
$insert_query = "INSERT INTO contact_us (name, email, phone, location, notes) VALUES ('$name', '$email', '$phone', '$location', '$notes')";

if ($connection->query($insert_query) === TRUE) {
    echo"<h2>Thank You for your repsonse! You form has been submitted.</h2>";
    echo"<h2>We'll get at it shortly.</h2>";
} else {
    echo "Error: " . $insert_query . "<br>" . $connection->error;
}

// Close the database connection
$connection->close();
?>
