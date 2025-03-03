<?php
session_start(); // Ensure session is started only once

if(file_exists('../../../../vendor/autoload.php')){
	require '../../../../vendor/autoload.php';
}
if(file_exists('../../../../config.php')) {
	include('../../../../config.php');
}

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

// Check if the 'students' table exists
$tableCheckQuery = "SHOW TABLES LIKE 'students'";
$result = $conn->query( $tableCheckQuery );

if ( $result->num_rows == 0 ) {
	echo json_encode( [ 'success' => false, 'message' => 'The students table does not exist in the database.' ] );
	exit;
}

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

// Handle POST request for creating a new notice
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

	// Validate input
	if ( empty( $title ) || empty( $details ) || empty( $date ) || empty( $class ) ) {
		echo json_encode( [ 'success' => false, 'message' => 'All fields are required' ] );
		exit;
	}

	// Fetch all students in the same class for email sending
	$studentQuery = "SELECT email FROM students WHERE class = ?";
	$stmt_students = $conn->prepare( $studentQuery );
	$stmt_students->bind_param( "s", $class );
	$stmt_students->execute();
	$result_students = $stmt_students->get_result();

	// Define email subject and body for new notice
	$subject = "New Notice: $title";
	$body = "
		<p>Dear Student,</p>
		<p>A new notice has been posted for your class <strong>$class</strong>.</p>
		<p><strong>Title:</strong> $title</p>
		<p><strong>Details:</strong> $details</p>
		<p><strong>Date:</strong> $date</p>
		<p>Best Regards,<br>By Our Department</p>
	";
	$altBody = "Dear Student, A new notice has been posted for your class $class. Title: $title, Details: $details, Date: $date. Best Regards, By Our Department.";

	// Send email to each student in the class
	while ( $student = $result_students->fetch_assoc() ) {
		$email = $student['email'];
			if(class_exists(\SSIP\EmailHelper\sendEmail::class)) {
				\SSIP\EmailHelper\sendEmail::sendEmailToTheRecipient( $email, $subject, $body, $altBody );
			}
	}

	// Insert new notice into the database after sending emails
	$stmt = $conn->prepare( "INSERT INTO notices (title, details, date, class, staff_id) VALUES (?, ?, ?, ?, ?)" );
	$stmt->bind_param( "ssssi", $title, $details, $date, $class, $staff_id );

	// Execute the statement to insert the notice
	if ( $stmt->execute() ) {
		echo json_encode( [ 'success' => true, 'message' => 'Emails sent and notice saved successfully' ] );
	} else {
		echo json_encode( [ 'success' => false, 'message' => 'Error saving notice: ' . $stmt->error ] );
	}

	$stmt->close();
	$stmt_students->close();
}

$conn->close();
