<?php

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

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	if ( isset( $_POST['id'] ) ) {
		$id   = $_POST['id'];
		$stmt = $conn->prepare( "DELETE FROM notices WHERE id = ?" );
		$stmt->bind_param( "i", $id );
		if ( $stmt->execute() ) {
			echo json_encode( [ 'success' => true, 'message' => 'Notice deleted successfully', 'redirect' => true ] );
		} else {
			echo json_encode( [ 'success' => false, 'message' => 'Error deleting notice' ] );
		}
		$stmt->close();
	}
}
