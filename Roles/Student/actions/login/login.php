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
$role       = isset( $_POST['role'] ) ? $_POST['role'] : '';
$student_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : '';
$password   = isset( $_POST['password'] ) ? $_POST['password'] : '';

// Check if role is 'student' and if all necessary data is provided
if ( $role === 'student' && ! empty( $student_id ) && ! empty( $password ) ) {
	// Query to check if student exists with the provided student_id
	$sql  = "SELECT * FROM students WHERE student_id = ?";
	$stmt = $conn->prepare( $sql );

	if ( ! $stmt ) {
		die( "Error preparing statement: " . $conn->error );
	}

	$stmt->bind_param( "s", $student_id );
	$stmt->execute();
	$result = $stmt->get_result();

	if ( $result->num_rows > 0 ) {
		// Student found, get the hashed password and other details
		$student = $result->fetch_assoc();

		// Verify the password with the stored hash
		if ( $password === $student['password'] ) {

			// Password is correct, start the session and store student info
			$_SESSION['student_id']        = $student['student_id'];
			$_SESSION['first_name']        = $student['first_name'];
			$_SESSION['last_name']         = $student['last_name'];
			$_SESSION['email']             = $student['email'];
			$_SESSION['student_logged_in'] = true;

			// Respond with success and redirect to student index page
			echo json_encode( [
				"success"  => true,
				"message"  => "Login successful!",
				"redirect" => "Roles/Student/"
			] );
		} else {
			// Incorrect password
			echo json_encode( [
				"success" => false,
				"message" => "Invalid credentials! Please check your password."
			] );
		}
	} else {
		// No student found with the given student ID
		echo json_encode( [
			"success" => false,
			"message" => "Student ID not found!"
		] );
	}

	// Close the statement
	$stmt->close();
} else {
	// Invalid request or missing fields
	echo json_encode( [
		"success" => false,
		"message" => "Please provide valid student ID and password."
	] );
}

// Close the connection
$conn->close();
?>
