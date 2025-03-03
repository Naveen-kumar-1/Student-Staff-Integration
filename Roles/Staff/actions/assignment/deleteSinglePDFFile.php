<?php
header( 'Content-Type: application/json' );
session_start();
// Check if staff is logged in
if ( ! isset( $_SESSION['staff_id'] ) ) {
	echo json_encode( [ 'success' => false, 'message' => 'Staff not logged in' ] );
	exit;
}

// Get the necessary data from the POST request
$data          = json_decode( file_get_contents( "php://input" ), true );
$file_name     = $data['file_name'];
$class_name    = $data['class_name'];
$subject       = $data['subject'];
$due_date      = $data['due_date'];
$student_id    = $data['student_id'];
$assignment_id = $data['assignment_id']; // Assuming you send the assignment ID as well

// Define the base directory where the assignments are stored
$base_directory = "../../../../uploads/assignment/" . $class_name . "/" . $subject . "/" . $due_date . "/";
$file_path      = $base_directory . $file_name;

// Database connection
if(file_exists('../../../../config.php')) {
	include('../../../../config.php');
}

// Check connection
if ( $conn->connect_error ) {
	echo json_encode( [ 'success' => false, 'message' => 'Database connection failed' ] );
	exit;
}

// Check if the file exists
if ( ! file_exists( $file_path ) ) {
	echo json_encode( [ 'success' => false, 'message' => 'File not found' ] );
	exit;
}

// Attempt to delete the file
if ( unlink( $file_path ) ) {
	// File deleted, now remove the student data from the 'submitted_students' column
	$sql  = "SELECT submitted_students FROM assignments WHERE id = ?";
	$stmt = $conn->prepare( $sql );
	$stmt->bind_param( "i", $assignment_id );
	$stmt->execute();
	$result = $stmt->get_result();

	if ( $result->num_rows > 0 ) {
		$row               = $result->fetch_assoc();
		$submittedStudents = json_decode( $row['submitted_students'], true );

		// Remove the student from the submitted_students array
		if ( isset( $submittedStudents[ $student_id ] ) ) {
			unset( $submittedStudents[ $student_id ] );
		}

		// Re-index the array and encode it back to JSON
		$newSubmittedStudentsJson = json_encode( $submittedStudents );

		// Update the 'submitted_students' column with the new data
		$update_sql  = "UPDATE assignments SET submitted_students = ? WHERE id = ?";
		$update_stmt = $conn->prepare( $update_sql );
		$update_stmt->bind_param( "si", $newSubmittedStudentsJson, $assignment_id );

		if ( $update_stmt->execute() ) {
			echo json_encode( [ 'success' => true, 'message' => 'File and student data deleted' ] );
		} else {
			echo json_encode( [ 'success' => false, 'message' => 'Failed to update student data' ] );
		}

		$update_stmt->close();
	} else {
		echo json_encode( [ 'success' => false, 'message' => 'Assignment not found' ] );
	}

	$stmt->close();
} else {
	echo json_encode( [ 'success' => false, 'message' => 'Failed to delete file' ] );
}

$conn->close();
?>
