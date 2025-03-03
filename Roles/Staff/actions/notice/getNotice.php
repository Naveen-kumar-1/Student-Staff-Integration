<?php
if(file_exists('../../../../config.php')) {
	include('../../../../config.php');
}

// Check connection
if ( $conn->connect_error ) {
	echo json_encode( [ 'success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error ] );
	exit;
}

// Get the JSON input from the request body
$data = json_decode( file_get_contents( "php://input" ), true );

// Check if 'id' is set in the request body
if ( isset( $data['id'] ) ) {
	$id = $data['id'];

	$stmt = $conn->prepare( "SELECT * FROM notices WHERE id = ?" );
	$stmt->bind_param( "i", $id );
	$stmt->execute();
	$result = $stmt->get_result();

	if ( $result->num_rows > 0 ) {
		$notice = $result->fetch_assoc();
		echo json_encode( [ 'success' => true, 'notice' => $notice ] );
	} else {
		echo json_encode( [ 'success' => false, 'message' => 'Notice not found' ] );
	}

	$stmt->close();
} else {
	echo json_encode( [ 'success' => false, 'message' => 'ID parameter is missing' ] );
}
$conn->close();
?>
