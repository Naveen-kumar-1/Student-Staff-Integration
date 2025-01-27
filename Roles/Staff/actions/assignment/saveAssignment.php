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

// Check if the assignments table exists, if not, create it
$tableCheckQuery = "CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id VARCHAR(50) NOT NULL,
    class VARCHAR(50) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    due_date DATE NOT NULL,
    details TEXT NOT NULL,
    FOREIGN KEY (staff_id) REFERENCES staffs(staff_id)
)";

if ( $conn->query( $tableCheckQuery ) === false ) {
	echo json_encode( [ 'success' => false, 'message' => 'Error creating table: ' . $conn->error ] );
	exit;
}

// Check if data is sent via POST
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	// Get form data
	$assignment_class    = $_POST['assignment_class'];
	$assignment_subject  = $_POST['assignment_subject'];
	$assignment_due_date = $_POST['assignment_due_date'];
	$assignment_details  = $_POST['assignment_details'];

	// Validate required fields
	if ( empty( $assignment_class ) || empty( $assignment_subject ) || empty( $assignment_due_date ) || empty( $assignment_details ) ) {
		echo json_encode( [ 'success' => false, 'message' => 'All fields are required' ] );
		exit;
	}

	// Prepare and execute the SQL query to insert data into assignments table
	$stmt = $conn->prepare( "INSERT INTO assignments (staff_id, class, subject, due_date, details) VALUES (?, ?, ?, ?, ?)" );
	$stmt->bind_param( "sssss", $staff_id, $assignment_class, $assignment_subject, $assignment_due_date, $assignment_details );

	if ( $stmt->execute() ) {
		echo json_encode( [ 'success' => true, 'message' => 'Assignment saved successfully', "redirect" => true ] );
	} else {
		echo json_encode( [ 'success' => false, 'message' => 'Error saving assignment: ' . $stmt->error ] );
	}

	// Close the statement
	$stmt->close();
}

// Close the connection
$conn->close();
?>
