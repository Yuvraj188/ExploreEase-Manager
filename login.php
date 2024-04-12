<?php

// Sample user data from array
$usersArray = array(
    'sahil@admin.com' => array('password' => 'sahil', 'role' => 'admin'),
    'user@somaiya.edu' => array('password' => 'user', 'role' => 'user')
);

$db_hostname = "localhost:3306";
$db_username = "root";                         //Replace wtih your username
$db_password = "";
$db_name = "tours";                           //Replace with your database name

$connection = new mysqli($db_hostname, $db_username, $db_password, $db_name);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredEmail = $_POST['email'];
    $enteredPassword = $_POST['password'];

    // Check the array first
    if (array_key_exists($enteredEmail, $usersArray)) {
        $user = $usersArray[$enteredEmail];

        // Check if the entered password is correct
        if ($enteredPassword == $user['password']) {
            // Authentication successful
            session_start();
            $_SESSION['user'] = array('email' => $enteredEmail, 'role' => $user['role']);

            // Redirect to the appropriate dashboard based on the user's role
            if ($user['role'] == 'admin') {
                header('Location: admin_dashboard.php');
            } elseif ($user['role'] == 'user') {
                header('Location: user_dashboard.php');
            }

            exit();
        }
    }

    // If not found in array, check the database
    $select_query = "SELECT * FROM users WHERE email = '$enteredEmail' AND password = '$enteredPassword'";
    $result = $connection->query($select_query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Authentication successful
        session_start();
        $_SESSION['user'] = array('email' => $enteredEmail, 'role' => $user['role']);

        // Redirect to the appropriate dashboard based on the user's role
        if ($user['role'] == 'admin') {
            header('Location: admin_dashboard.php');
        } elseif ($user['role'] == 'user') {
            header('Location: user_dashboard.php');
        }

        exit();
    }

    // Authentication failed
    echo "Invalid email or password. Please try again.";
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Custom Styles -->
    <style>
        body {
            background-color: GhostWhite;
        }

        .navbar {
            background-color: DimGray;
            color: White;
        }

        .navbar-brand {
            color: White;
        }

        .navbar-light .navbar-toggler-icon {
            background-color: White;
        }

        .container {
            margin-top: 50px;
        }

        .form-container {
            background-color: White;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="adventures.html"><i class="fas fa-hiking"></i> ExploreSafar</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.html">Go to home page</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="adventures.html">Adventures</a>
                </li>
            </ul>
        </div>
        <div class="icons">
            <div id="search-btn" class="fas fa-search"></div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-container">
                    <h2 class="text-center mb-4">Log in</h2>
                    <!-- Add your login form here -->
                    <form method="post" action="login.php">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Log in</button>
                    </form>
                    <p class="mt-3">Don't have an account? <a href="signup.php">Sign up here</a>.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
