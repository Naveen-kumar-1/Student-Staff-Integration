<?php


if(file_exists('../../../../vendor/autoload.php')){
	require '../../../../vendor/autoload.php';
}
// Create connection
if(file_exists('../../../../config.php')) {
	include('../../../../config.php');
}

// Check connection
if ( $conn->connect_error ) {
	echo json_encode( [ 'success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error ] );
	exit;
}
$tableCheckQuery = "SHOW TABLES LIKE 'students'";
$result = $conn->query( $tableCheckQuery );

if ( $result->num_rows == 0 ) {
	echo json_encode( [ 'success' => false, 'message' => 'The students table does not exist in the database.' ] );
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

	// Fetch students in the relevant class for email sending
	$studentQuery = "SELECT email FROM students WHERE class = ?";
	$stmt_students = $conn->prepare( $studentQuery );
	$stmt_students->bind_param( "s", $class );
	$stmt_students->execute();
	$result_students = $stmt_students->get_result();

	// Prepare email subject and body for the updated notice
	$subject = "Updated Notice: $title";
	$body = "
		<p>Dear Student,</p>
		<p>A notice has been updated for your class <strong>$class</strong>.</p>
		<p><strong>Title:</strong> $title</p>
		<p><strong>Details:</strong> $details</p>
		<p><strong>Date:</strong> $date</p>
		<p>Best Regards,<br>Your School</p>
	";
	$altBody = "Dear Student, A notice has been updated for your class $class. Title: $title, Details: $details, Date: $date. Best Regards, Your School.";

	// Send email to each student in the class
	while ( $student = $result_students->fetch_assoc() ) {
		$email = $student['email'];
		if(class_exists(\SSIP\EmailHelper\sendEmail::class)) {
			\SSIP\EmailHelper\sendEmail::sendEmailToTheRecipient( $email, $subject, $body, $altBody );
		}
	}

	// After sending emails, update the notice in the database
	$stmt_update = $conn->prepare( "UPDATE notices SET title = ?, details = ?, date = ?, class = ? WHERE id = ?" );
	$stmt_update->bind_param( "ssssi", $title, $details, $date, $class, $id );

	if ( $stmt_update->execute() ) {
		echo json_encode( [ 'success' => true, 'message' => 'Notice updated and emails sent successfully' ] );
	} else {
		echo json_encode( [ 'success' => false, 'message' => 'Error updating notice: ' . $stmt_update->error ] );
	}

	$stmt_students->close();
	$stmt_update->close();
} else {
	// Invalid request method
	echo json_encode( [ 'success' => false, 'message' => 'Invalid request method' ] );
}

// Close the database connection
$conn->close();
?>
