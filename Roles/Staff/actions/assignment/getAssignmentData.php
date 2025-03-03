<?php
header( 'Content-Type: application/json' );
session_start();

// Check if staff is logged in
if ( ! isset( $_SESSION['staff_id'] ) ) {
	echo json_encode( [ 'success' => false, 'message' => 'Staff not logged in' ] );
	exit;
}

$staff_id = $_SESSION['staff_id'];

// Database connection details
if(file_exists('../../../../config.php')) {
	include('../../../../config.php');
}

if ( $conn->connect_error ) {
	echo json_encode( [ 'success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error ] );
	exit;
}

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	$data          = json_decode( file_get_contents( 'php://input' ), true );
	$assignment_id = $data['id'];

	// Fetch assignment data
	$stmt = $conn->prepare( "SELECT * FROM assignments WHERE id = ? AND staff_id = ?" );
	$stmt->bind_param( "is", $assignment_id, $staff_id );
	$stmt->execute();
	$result = $stmt->get_result();

	if ( $result->num_rows > 0 ) {
		$assignment = $result->fetch_assoc();
		echo json_encode( [ 'success' => true, 'assignment' => $assignment ] );
	} else {
		echo json_encode( [ 'success' => false, 'message' => 'Assignment not found' ] );
	}

	$stmt->close();
}

$conn->close();
?>
