<?php
session_start(); // Start the session to manage login state

// Database connection
if(file_exists('../../../../config.php')) {
	include('../../../../config.php');
}

// Check connection
if ( $conn->connect_error ) {
	die( "Connection failed: " . $conn->connect_error );
}

// Get POST data from the AJAX request
$role     = isset( $_POST['role'] ) ? $_POST['role'] : '';
$staff_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : '';
$password = isset( $_POST['password'] ) ? $_POST['password'] : '';

// Check if role is 'staff' and if all necessary data is provided
if ( $role === 'staff' && ! empty( $staff_id ) && ! empty( $password ) ) {
	// Query to check if staff exists with the provided staff_id
	$sql  = "SELECT * FROM staffs WHERE staff_id = ?";
	$stmt = $conn->prepare( $sql );

	if ( ! $stmt ) {
		die( "Error preparing statement: " . $conn->error );
	}

	$stmt->bind_param( "s", $staff_id );
	$stmt->execute();
	$result = $stmt->get_result();

	if ( $result->num_rows > 0 ) {
		// Staff found, get the hashed password and other details
		$staff = $result->fetch_assoc();

		// Verify the password with the stored hash
		if ( $password === $staff['password'] ) {

			// Password is correct, start the session and store staff info
			$_SESSION['staff_id']        = $staff['staff_id'];
			$_SESSION['first_name']      = $staff['first_name'];
			$_SESSION['last_name']       = $staff['last_name'];
			$_SESSION['email']           = $staff['email'];
			$_SESSION['staff_logged_in'] = true;

			// Respond with success and redirect to staff index page
			echo json_encode( [
				"success"  => true,
				"message"  => "Login successful!",
				"redirect" => "Roles/Staff/"
			] );
		} else {
			// Incorrect password
			echo json_encode( [
				"success" => false,
				"message" => "Invalid credentials! Please check your password."
			] );
		}
	} else {
		// No staff found with the given staff ID
		echo json_encode( [
			"success" => false,
			"message" => "Staff ID not found!"
		] );
	}

	// Close the statement
	$stmt->close();
} else {
	// Invalid request or missing fields
	echo json_encode( [
		"success" => false,
		"message" => "Please provide valid staff ID and password."
	] );
}

// Close the connection
$conn->close();
?>
