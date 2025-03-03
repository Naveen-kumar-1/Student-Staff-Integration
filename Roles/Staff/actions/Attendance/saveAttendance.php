<?php
// Database connection
if(file_exists('../../../../config.php')) {
	include('../../../../config.php');
}
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the `student_attendance` table if it doesn't exist
$tableCreationQuery = "CREATE TABLE IF NOT EXISTS student_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class VARCHAR(100) NOT NULL,
    attendance_date DATE NOT NULL,
    attendance_data JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_attendance (class, attendance_date)
)";

if ($conn->query($tableCreationQuery) === TRUE) {
    // Table is ready, no output here, just proceed
} else {
    echo json_encode(["status" => "error", "message" => "Error creating table: " . $conn->error]);
    exit;
}

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Read input JSON
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['class'], $data['date'], $data['students'])) {
        echo json_encode(["status" => "error", "message" => "Invalid request data."]);
        exit;
    }

    $date = $data['date'];
    $class = $data['class'];
    $attendance_data = [];

    // Prepare the data for insertion (as JSON)
    foreach ($data['students'] as $student) {
        $attendance_data[$student['student_id']] = [
            'student_name' => $student['student_name'],
            'class' => $student['class'],
            'attendance' => $student['attendance']
        ];
    }

    // Convert the attendance data to JSON format
    $attendance_json = json_encode($attendance_data, JSON_UNESCAPED_UNICODE);

    // Check for JSON encoding errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["status" => "error", "message" => "JSON encoding failed", "error" => json_last_error_msg()]);
        exit;
    }

    // Check if the attendance for this class and date already exists
    $checkQuery = "SELECT * FROM student_attendance WHERE class = ? AND attendance_date = ?";
    $stmt_check = $conn->prepare($checkQuery);
    $stmt_check->bind_param("ss", $class, $date);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Record exists, so update it
        $updateQuery = "UPDATE student_attendance SET attendance_data = ?, created_at = CURRENT_TIMESTAMP WHERE class = ? AND attendance_date = ?";
        $stmt_update = $conn->prepare($updateQuery);
        $stmt_update->bind_param("sss", $attendance_json, $class, $date);
        if ($stmt_update->execute() && $stmt_update->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "Attendance updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "No changes detected or update failed.", "error" => $stmt_update->error]);
        }
    } else {
        // Record doesn't exist, so insert it
        $insertQuery = "INSERT INTO student_attendance (class, attendance_date, attendance_data) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($insertQuery);
        $stmt_insert->bind_param("sss", $class, $date, $attendance_json);

        if ($stmt_insert->execute()) {
            echo json_encode(["status" => "success", "message" => "Attendance saved successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error saving attendance.", "error" => $stmt_insert->error]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
exit;
