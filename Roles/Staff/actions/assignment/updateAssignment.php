<?php
// Set response content type to JSON
header( 'Content-Type: application/json' );
session_start();

// Check if the staff is logged in
if ( ! isset( $_SESSION['staff_id'] ) ) {
	echo json_encode( [ 'success' => false, 'message' => 'Staff not logged in' ] );
	exit;
}

// Get the logged-in staff ID
$staff_id = $_SESSION['staff_id'];

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

// Check if data is sent via POST
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	// Get form data
	$assignment_id       = $_POST['assignment_id'];
	$assignment_class    = $_POST['assignment_class'];
	$assignment_subject  = $_POST['assignment_subject'];
	$assignment_due_date = $_POST['assignment_due_date'];
	$assignment_details  = $_POST['assignment_details'];

	// Validate required fields
	if ( empty( $assignment_class ) || empty( $assignment_subject ) || empty( $assignment_due_date ) || empty( $assignment_details ) ) {
		echo json_encode( [ 'success' => false, 'message' => 'All fields are required' ] );
		exit;
	}

	// Prepare and execute the SQL query to update data in assignments table
	$stmt = $conn->prepare( "UPDATE assignments SET class = ?, subject = ?, due_date = ?, details = ? WHERE id = ? AND staff_id = ?" );
	$stmt->bind_param( "ssssss", $assignment_class, $assignment_subject, $assignment_due_date, $assignment_details, $assignment_id, $staff_id );

	if ( $stmt->execute() ) {
		echo json_encode( [ 'success' => true, 'message' => 'Assignment updated successfully', "redirect" => true ] );
	} else {
		echo json_encode( [ 'success' => false, 'message' => 'Error updating assignment: ' . $stmt->error ] );
	}

	// Close the statement
	$stmt->close();
}

// Close the connection
$conn->close();
?>
