<?php
header('Content-Type: application/json'); // JSON response

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "student_staff_integration";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Get POST data
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if ($id == 0 || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Check if the leave request exists
$checkQuery = "SELECT * FROM leave_requests WHERE id = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Leave request not found']);
    exit;
}

// If status is "Trash", delete the row
if (strtolower($status) === 'trash') {
    $deleteQuery = "DELETE FROM leave_requests WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Leave request deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete leave request']);
    }
} else {
    // Update the status (Approved, Declined, Pending)
    $updateQuery = "UPDATE leave_requests SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Leave request status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update leave request']);
    }
}

$stmt->close();
$conn->close();
?>
