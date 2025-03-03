<?php
header( 'Content-Type: application/json' );
session_start();

// Check if staff is logged in
if ( ! isset( $_SESSION['staff_id'] ) ) {
	echo json_encode( [ 'success' => false, 'message' => 'Staff not logged in' ] );
	exit;
}

// Get assignment ID from the POST request
$data          = json_decode( file_get_contents( "php://input" ), true );
$assignment_id = $data['assignment_id'];

// Database connection
if(file_exists('../../../../config.php')) {
	include('../../../../config.php');
}

// Check connection
if ( $conn->connect_error ) {
	echo json_encode( [ 'success' => false, 'message' => 'Database connection failed' ] );
	exit;
}

// Get the list of students who submitted the assignment
$sql  = "SELECT submitted_students FROM assignments WHERE id = ?";
$stmt = $conn->prepare( $sql );
$stmt->bind_param( "i", $assignment_id );
$stmt->execute();
$result = $stmt->get_result();

if ( $result->num_rows > 0 ) {
	$row = $result->fetch_assoc();

	$submittedStudents = json_decode( $row['submitted_students'], true );


	// Return the list of students
	echo json_encode( [ 'success' => true, 'students' => $submittedStudents, 'assignment_id' => $assignment_id ] );
} else {
	echo json_encode( [ 'success' => false, 'message' => 'Assignment not found' ] );
}

$stmt->close();
$conn->close();
?>
