<?php
// database.php
$host = 'localhost';   // Database host (for local: localhost)
$dbname = 'portfolio_project';  // The database name you created
$username = 'root';    // Default username for MySQL in XAMPP/WAMP/MAMP
$password = '';        // Default password for MySQL (empty for XAMPP/WAMP/MAMP)

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Connected to the database successfully!<br>"; // Debugging
}
?>


