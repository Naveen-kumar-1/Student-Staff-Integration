<?php
// Include database connection if file exists
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

// Fetch admin details
$sql = "SELECT * FROM `student-staff-integration-admin` WHERE id = 1"; // Assuming the admin has ID 1
$result = $conn->query($sql);

// Return the details as JSON
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	echo json_encode(["success" => true, "email" => $row["email"], "password" => $row["password"]]);
} else {
	echo json_encode(["success" => false, "message" => "Admin not found"]);
}

$conn->close();
?>
