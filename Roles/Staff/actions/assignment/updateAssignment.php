<?php
// Set response content type to JSON
header( 'Content-Type: application/json' );
session_start();

if(file_exists('../../../../vendor/autoload.php')){
	require '../../../../vendor/autoload.php';
}
// Check if the staff is logged in
if ( ! isset( $_SESSION['staff_id'] ) ) {
	echo json_encode( [ 'success' => false, 'message' => 'Staff not logged in' ] );
	exit;
}

// Get the logged-in staff ID
$staff_id = $_SESSION['staff_id'];

// Database connection details
if(file_exists('../../../../config.php')) {
	include('../../../../config.php');
}

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
	$_SESSION['assignment_data'] = [
		'assignment_id'       => $assignment_id,
		'assignment_class'    => $assignment_class,
		'assignment_subject'  => $assignment_subject,
		'assignment_due_date' => $assignment_due_date,
		'assignment_details'  => $assignment_details,
		'staff_id'            => $staff_id
	];
	// Validate required fields
	if ( empty( $assignment_class ) || empty( $assignment_subject ) || empty( $assignment_due_date ) || empty( $assignment_details ) ) {
		echo json_encode( [ 'success' => false, 'message' => 'All fields are required' ] );
		exit;
	}

	// Retrieve student emails for the specified class
	$emailQuery = $conn->prepare("SELECT email FROM students WHERE class = ?");
	$emailQuery->bind_param("s", $assignment_class);
	$emailQuery->execute();
	$emailResult = $emailQuery->get_result();

	// Check if any students exist in the specified class
	if ($emailResult->num_rows > 0) {
		$emails = [];
		while ($row = $emailResult->fetch_assoc()) {
			$emails[] = $row['email'];
		}

		// Send an email notification to each student about the updated assignment
		foreach ($emails as $email) {
			if (class_exists(\SSIP\EmailHelper\sendEmail::class)) {
				$response = \SSIP\EmailHelper\sendEmail::sendEmail($email, 'update_assignment');
				if (!$response) {
					echo json_encode([ 'success' => false, 'message' => 'Error sending notification to ' . $email ]);
					exit;
				}
			}
		}

		// If emails are successfully sent, proceed to update the assignment in the database
		$assignmentData = $_SESSION['assignment_data'];
		$stmt = $conn->prepare( "UPDATE assignments SET class = ?, subject = ?, due_date = ?, details = ? WHERE id = ? AND staff_id = ?" );
		$stmt->bind_param( "ssssss", $assignmentData['assignment_class'], $assignmentData['assignment_subject'], $assignmentData['assignment_due_date'], $assignmentData['assignment_details'], $assignmentData['assignment_id'], $assignmentData['staff_id'] );

		if ( $stmt->execute() ) {
			// Clear session data after successful update
			unset($_SESSION['assignment_data']);

			echo json_encode( [
				'success' => true,
				'message' => 'Assignment updated successfully and notifications sent to students.',
				'redirect' => true
			] );
		} else {
			echo json_encode( [ 'success' => false, 'message' => 'Error updating assignment: ' . $stmt->error ] );
		}

		// Close the statement
		$stmt->close();

	} else {
		// No students found in the class
		echo json_encode([
			'success' => false,
			'message' => 'No students found in the specified class.'
		]);
	}
}

// Close the connection
$conn->close();
?>
