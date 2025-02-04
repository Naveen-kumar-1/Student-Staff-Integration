<?php
session_start();

// Include database connection if file exists
if ( file_exists( '../config.php' ) ) {
	include '../config.php';
} else {
	// Return error if connection file is missing
	echo json_encode( [
		'type'    => 'error',
		'message' => 'Database connection file not found.'
	] );
	exit;
}

// Check if password is set in POST request
if ( isset( $_POST['password'] ) ) {
	$password = $_POST['password'];

	// Validate the password length and content (e.g., letters and numbers)
	if ( ! preg_match( '/^[A-Za-z0-9]{8,}$/', $password ) ) {
		echo json_encode( [
			'type'    => 'error',
			'message' => 'Password does not meet the requirements.'
		] );
		exit;
	}


	// Store the hashed password in the database
	// You would also want to identify the user, e.g., using their user ID or email
	$userId = 1; // Replace with actual user ID or session-based identifier

	// Check if database connection is successful
	if ( $conn->connect_error ) {
		echo json_encode( [
			'type'    => 'error',
			'message' => 'Database connection failed: ' . $conn->connect_error
		] );
		exit;
	}

	// Prepare SQL statement to update the password
	$sql  = "UPDATE `student-staff-integration-admin` SET password = ? WHERE id = ?";
	$stmt = $conn->prepare( $sql );

	// Check if SQL statement preparation is successful
	if ( $stmt === false ) {
		echo json_encode( [
			'type'    => 'error',
			'message' => 'Failed to prepare the SQL statement: ' . $conn->error
		] );
		exit;
	}

	// Bind parameters (hashed password and user email)
	if ( $stmt->bind_param( 'si', $password, $userId ) ) {
		// Execute the query
		if ( $stmt->execute() ) {
			echo json_encode( [
				'type'     => 'success',
				'message'  => 'Password updated successfully.',
				'redirect' => '../index.php'
			] );
		} else {
			echo json_encode( [
				'type'    => 'error',
				'message' => 'Failed to execute the query.'
			] );
		}
	} else {
		echo json_encode( [
			'type'    => 'error',
			'message' => 'Failed to bind parameters.'
		] );
	}

	// Close the statement
	$stmt->close();
} else {
	// If password is not set in POST request
	echo json_encode( [
		'type'    => 'error',
		'message' => 'No password provided.'
	] );
}
?>