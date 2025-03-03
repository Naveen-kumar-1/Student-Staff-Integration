<?php
header('Content-Type: application/json'); // Set response type to JSON

// Database connection

if(file_exists('../../../../vendor/autoload.php')){
    require '../../../../vendor/autoload.php';
}
if(file_exists('../../../../config.php')) {
    include('../../../../config.php');
}

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Ensure leave_requests table exists
$tableQuery = "CREATE TABLE IF NOT EXISTS leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    student_id VARCHAR(50) NOT NULL,
    student_class VARCHAR(50) NOT NULL,
    leave_date DATE NOT NULL,
    leave_reason TEXT NOT NULL,
    staff_id VARCHAR(50) NOT NULL,
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


// Find the staff responsible for the class
$staffQuery = "SELECT staff_id, email FROM staffs WHERE class_adviser = ?";
$stmt = $conn->prepare($staffQuery);
$stmt->bind_param("s", $student_class);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

if (!$staff) {
    echo json_encode(['success' => false, 'message' => 'No class adviser found for this class']);
    exit;
}

$staff_id = $staff['staff_id'];
$staff_email = $staff['email']; // Get the staff's email



// UPDATE REQUEST (If ID is provided)
if (!empty($id)) {

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
    $updateQuery = "UPDATE leave_requests SET leave_date = ?, leave_reason = ?, staff_id = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssi", $leave_date, $leave_reason, $staff_id, $id);

    if ($stmt->execute()) {
        // Send email notification for updated request
        $subject = "Updated Leave Request from " . $student_name;
        $body = "
            <p>Dear Staff,</p>
            <p>The leave request from student <strong>$student_name ($student_id)</strong> has been updated.</p>
            <p><strong>Class:</strong> $student_class</p>
            <p><strong>Leave Date:</strong> $leave_date</p>
            <p><strong>Reason:</strong> $leave_reason</p>
            <p>Best Regards,<br>$student_name ($student_id)</p>
        ";
        $altBody = "The leave request from student $student_name ($student_id) has been updated. Class: $student_class, Leave Date: $leave_date, Reason: $leave_reason.";
        if (class_exists('\SSIP\EmailHelper\sendEmail')) {
         $response =  \SSIP\EmailHelper\sendEmail::sendEmailToTheRecipient($staff_email, $subject, $body, $altBody);

         if($response) {

             echo json_encode( [ 'success' => true, 'message' => 'Leave request updated successfully!' ] );
         }}
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update leave request']);
    }
} else {
    if (empty($student_name) || empty($student_id) || empty($student_class) || empty($leave_date) || empty($leave_reason)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    // INSERT NEW LEAVE REQUEST
    $stmt = $conn->prepare("INSERT INTO leave_requests (student_name, student_id, student_class, leave_date, leave_reason, staff_id, status) 
                            VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("ssssss", $student_name, $student_id, $student_class, $leave_date, $leave_reason, $staff_id);

    if ($stmt->execute()) {
        // Send email notification for new leave request
        $subject = "New Leave Request from " . $student_name;
        $body = "
            <p>Dear Staff,</p>
            <p>A new leave request has been submitted by student <strong>$student_name ($student_id)</strong>.</p>
            <p><strong>Class:</strong> $student_class</p>
            <p><strong>Leave Date:</strong> $leave_date</p>
            <p><strong>Reason:</strong> $leave_reason</p>
            <p>Best Regards,<br>$student_name ($student_id)</p>
        ";
        $altBody = "A new leave request has been submitted by student $student_name ($student_id). Class: $student_class, Leave Date: $leave_date, Reason: $leave_reason.";

        if (class_exists('\SSIP\EmailHelper\sendEmail')) {
            $response =  \SSIP\EmailHelper\sendEmail::sendEmailToTheRecipient($staff_email, $subject, $body, $altBody);

            if($response) {

                echo json_encode( [ 'success' => true, 'message' => 'Leave request daved successfully!' ] );
            }}
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to apply leave']);
    }
}

$stmt->close();
$conn->close();
?>
