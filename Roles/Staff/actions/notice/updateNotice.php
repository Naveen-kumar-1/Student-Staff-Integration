<?php

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "student_staff_integration";

// Create connection
$conn = new mysqli( $servername, $username, $password, $dbname );

// Check connection
if ( $conn->connect_error ) {
	echo json_encode( [ 'success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error ] );
	exit;
}

// Ensure the request method is POST
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	// Get the JSON data from the request
	$data = json_decode( file_get_contents( "php://input" ), true );

	// Check if required fields are set
	if ( ! isset( $data['id'] ) || ! isset( $data['title'] ) || ! isset( $data['details'] ) || ! isset( $data['date'] ) || ! isset( $data['class'] ) ) {
		echo json_encode( [ 'success' => false, 'message' => 'Missing required fields' ] );
		exit;
	}

	// Sanitize and assign variables
	$id      = $conn->real_escape_string( $data['id'] );
	$title   = $conn->real_escape_string( $data['title'] );
	$details = $conn->real_escape_string( $data['details'] );
	$date    = $conn->real_escape_string( $data['date'] );
	$class   = $conn->real_escape_string( $data['class'] );

	// Prepare and execute the update query
	$stmt = $conn->prepare( "UPDATE notices SET title = ?, details = ?, date = ?, class = ? WHERE id = ?" );
	$stmt->bind_param( "ssssi", $title, $details, $date, $class, $id );

	if ( $stmt->execute() ) {
		echo json_encode( [ 'success' => true, 'message' => 'Notice updated successfully' ] );
	} else {
		echo json_encode( [ 'success' => false, 'message' => 'Error updating notice: ' . $stmt->error ] );
	}

	$stmt->close();
} else {
	// Invalid request method
	echo json_encode( [ 'success' => false, 'message' => 'Invalid request method' ] );
}

// Close the database connection
$conn->close();
?>
