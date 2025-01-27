<?php
session_start(); // Ensure session is started only once
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

// Retrieve staff_id from session
if ( ! isset( $_SESSION['staff_id'] ) ) {
	echo json_encode( [ 'success' => false, 'message' => 'User not logged in' ] );
	exit;
}
$staff_id = $_SESSION['staff_id'];

// Create the notices table if it doesn't exist
$tableQuery = "CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    details TEXT NOT NULL,
    date DATE NOT NULL,
    class VARCHAR(50) NOT NULL,
    staff_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ( ! $conn->query( $tableQuery ) ) {
	echo json_encode( [ 'success' => false, 'message' => 'Error creating notices table: ' . $conn->error ] );
	exit;
}

// Handle POST request for creating or updating a notice
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	// Get the raw POST data
	$data = json_decode( file_get_contents( 'php://input' ), true );

	// Check if the data is valid
	if ( ! $data ) {
		echo json_encode( [ 'success' => false, 'message' => 'Invalid JSON data' ] );
		exit;
	}

	$title   = $data['title'];
	$details = $data['details'];
	$date    = $data['date'];
	$class   = $data['class'];
	$id      = $data['id'] ?? null; // Handle both create and update

	// Validate input
	if ( empty( $title ) || empty( $details ) || empty( $date ) || empty( $class ) ) {
		echo json_encode( [ 'success' => false, 'message' => 'All fields are required' ] );
		exit;
	}

	// Insert or Update the notice
	if ( $id ) {
		// Update existing notice
		$stmt = $conn->prepare( "UPDATE notices SET title = ?, details = ?, date = ?, class = ?, staff_id = ? WHERE id = ?" );
		if ( $stmt === false ) {
			echo json_encode( [ 'success' => false, 'message' => 'SQL prepare error: ' . $conn->error ] );
			exit;
		}
		$stmt->bind_param( "ssssii", $title, $details, $date, $class, $staff_id, $id );
	} else {
		// Insert new notice
		$stmt = $conn->prepare( "INSERT INTO notices (title, details, date, class, staff_id) VALUES (?, ?, ?, ?, ?)" );
		if ( $stmt === false ) {
			echo json_encode( [ 'success' => false, 'message' => 'SQL prepare error: ' . $conn->error ] );
			exit;
		}
		$stmt->bind_param( "ssssi", $title, $details, $date, $class, $staff_id );
	}

	if ( $stmt->execute() ) {
		echo json_encode( [ 'success' => true, 'message' => 'Notice saved successfully' ] );
	} else {
		echo json_encode( [ 'success' => false, 'message' => 'Error saving notice: ' . $stmt->error ] );
	}

	$stmt->close();
}


$conn->close();
?>
