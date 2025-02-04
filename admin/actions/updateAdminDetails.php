<?php
if ( file_exists( '../config.php' ) ) {
	include '../config.php';
} else {
	// Return error if connection file is missing
	echo json_encode( [
		'type'    => 'error',
		'message' => 'Database connection file not found.'
	] );
	exit;
}

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Sanitize input data
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	$password = $_POST['password'];  // Ensure proper hashing for passwords in real scenarios

	// Update query
	$stmt = $conn->prepare("UPDATE `student-staff-integration-admin` SET email = ?, password = ? WHERE id = 1");  // Assuming the admin has an ID of 1
	$stmt->bind_param("ss", $email, $password);

	if ($stmt->execute()) {
		echo json_encode(["success" => true]);
	} else {
		echo json_encode(["success" => false]);
	}

	$stmt->close();
	$conn->close();
}
?>
