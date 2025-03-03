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

// Get `staff_id` from POST request
$staff_id = isset($_POST['staff_id']) ? $_POST['staff_id'] : '';

if (empty($staff_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request. Staff ID is required.']);
    exit;
}

// Delete all leave requests for this staff
$sql = "DELETE FROM leave_requests WHERE staff_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staff_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'All leave requests deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete leave requests.']);
}

$stmt->close();
$conn->close();
?>
