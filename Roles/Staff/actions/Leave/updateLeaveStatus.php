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
$checkQuery = "SELECT student_id FROM leave_requests WHERE id = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Leave request not found']);
    exit;
}

$leaveRequest = $result->fetch_assoc();
$studentId = $leaveRequest['student_id'];

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
        // If approved, check and remove student attendance
        if (strtolower($status) === 'approved') {
            $attendanceQuery = "SELECT id, attendance_data FROM student_attendance WHERE JSON_CONTAINS_PATH(attendance_data, 'one', ?)";

            $jsonPath = '$."' . $studentId . '"'; // Correct JSON path
            $stmt = $conn->prepare($attendanceQuery);
            $stmt->bind_param("s", $jsonPath);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $attendanceId = $row['id'];
                    $attendanceData = json_decode($row['attendance_data'], true);

                    if (isset($attendanceData[$studentId])) {
                        unset($attendanceData[$studentId]); // Remove student attendance

                        // Convert back to JSON (Ensure empty object is stored as `{}`)
                        $updatedAttendanceJson = empty($attendanceData) ? '{}' : json_encode($attendanceData, JSON_UNESCAPED_UNICODE);

                        // Update database with modified JSON
                        $updateAttendanceQuery = "UPDATE student_attendance SET attendance_data = ? WHERE id = ?";
                        $stmt2 = $conn->prepare($updateAttendanceQuery);
                        $stmt2->bind_param("si", $updatedAttendanceJson, $attendanceId);
                        $stmt2->execute();
                        $stmt2->close();
                    }
                }
            }
        }

        echo json_encode(['success' => true, 'message' => 'Leave request status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update leave request']);
    }
}

// Close connections
$stmt->close();
$conn->close();
?>
