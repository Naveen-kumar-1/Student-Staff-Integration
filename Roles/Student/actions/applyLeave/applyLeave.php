<?php
header('Content-Type: application/json'); // JSON response

// Database connection
$host = "localhost";
$user = "root";  // Change if needed
$pass = "";      // Change if needed
$dbname = "student_staff_integration";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Create table if it doesn't exist (includes status column)
$tableQuery = "CREATE TABLE IF NOT EXISTS leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    student_id VARCHAR(50) NOT NULL,
    student_class VARCHAR(50) NOT NULL,
    leave_date DATE NOT NULL,
    leave_reason TEXT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($tableQuery);

// Get form data
$id = $_POST['id'] ?? null; // ID for updating
$student_name = $_POST['student_name'] ?? '';
$student_id = $_POST['student_id'] ?? '';
$student_class = $_POST['student_class'] ?? '';
$leave_date = $_POST['leave_date'] ?? '';
$leave_reason = $_POST['leave_reason'] ?? '';

// Validate inputs


// **Update Request**
if (!empty($id)) {
    if (empty($leave_date) || empty($leave_reason)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    // Check if the leave request exists and is still "Pending"
    $checkQuery = "SELECT status FROM leave_requests WHERE id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $leave = $result->fetch_assoc();

    if (!$leave) {
        echo json_encode(['success' => false, 'message' => 'Leave request not found']);
        exit;
    }

    if ($leave['status'] !== 'Pending') {
        echo json_encode(['success' => false, 'message' => 'Only pending requests can be updated']);
        exit;
    }

    // Update the leave request
    $updateQuery = "UPDATE leave_requests SET leave_date = ?, leave_reason = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssi", $leave_date, $leave_reason, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Leave request updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update leave request']);
    }
} // **Insert Request**
else {
    if (empty($student_name) || empty($student_id) || empty($student_class) || empty($leave_date) || empty($leave_reason)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    $stmt = $conn->prepare("INSERT INTO leave_requests (student_name, student_id, student_class, leave_date, leave_reason, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("sssss", $student_name, $student_id, $student_class, $leave_date, $leave_reason);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Leave request submitted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to apply leave']);
    }
}

$stmt->close();
$conn->close();
?>
