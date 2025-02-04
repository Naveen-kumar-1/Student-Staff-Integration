<?php
// Database connection details
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "student_staff_integration";

// Create a connection
$conn = new mysqli( $servername, $username, $password, $dbname );

// Check connection
if ( $conn->connect_error ) {
	echo json_encode( [ 'success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error ] );
	exit;
}