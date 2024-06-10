<?php
$servername = "192.168.5.8:33006";
$username = "root";
$password = "dbrootpass";
$dbname = "my_gas_app";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
