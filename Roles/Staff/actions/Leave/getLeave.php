<?php
header('Content-Type: application/json'); // JSON response

// Database connection
if(file_exists('../../../../config.php')) {
	include('../../../../config.php');
}

// Check connection
if ($conn->connect_error) {
	die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Get `student_id` from POST request
$student_id = isset($_POST['student_id']) ? $_POST['student_id'] : '';

if (empty($student_id)) {
	echo json_encode(['success' => false, 'message' => 'Invalid request. Student ID is required.']);
	exit;
}

// Query to fetch leave details for the specific student
$sql = "SELECT * FROM leave_requests WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();

// Get the result of the query
$result = $stmt->get_result();
$leave_details = $result->fetch_assoc();

if ($leave_details) {
	echo json_encode(['success' => true, 'leave_details' => $leave_details]);
} else {
	echo json_encode(['success' => false, 'message' => 'No leave details found for this student.']);
}

$stmt->close();
$conn->close();
?>
