<?php
header('Content-Type: application/json'); // JSON response
if(file_exists('../../../../vendor/autoload.php')){
    require '../../../../vendor/autoload.php';
}
// Database connection
if(file_exists('../../../../config.php')) {
    include('../../../../config.php');
}

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

// Fetch student's email
$studentEmailQuery = "SELECT email FROM students WHERE student_id = ?";
$stmt = $conn->prepare($studentEmailQuery);
$stmt->bind_param("s", $studentId);
$stmt->execute();
$emailResult = $stmt->get_result();

if ($emailResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Student email not found']);
    exit;
}

$studentEmail = $emailResult->fetch_assoc()['email'];

// Email content based on status
$subject = "Your Leave Request Status Update";
$body = "";
$altBody = "Your leave request has been updated.";
if (strtolower($status) === 'approved') {
    $body = "
        <p>Dear Student,</p>
        <p>Your leave request has been <strong>approved</strong>.</p>
        <p>Best regards,<br>College Administration</p>
    ";
    $altBody = "Your leave request has been approved.";
} elseif (strtolower($status) === 'rejected') {
    $body = "
        <p>Dear Student,</p>
        <p>Your leave request has been <strong>rejected</strong>.</p>
        <p>Best regards,<br>College Administration</p>
    ";
    $altBody = "Your leave request has been rejected.";
}

// Using your custom Gmail email sending function if class exists
if (class_exists(\SSIP\EmailHelper\sendEmail::class)) {
    \SSIP\EmailHelper\sendEmail::sendEmailToTheRecipient($studentEmail, $subject, $body, $altBody);
} else {
    echo json_encode(['success' => false, 'message' => 'Email sending function not available']);
    exit;
}

// Proceed with updating the leave request status
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
