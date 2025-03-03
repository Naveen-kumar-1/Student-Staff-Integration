<?php
// Set response content type to JSON
header('Content-Type: application/json');
session_start();
if(file_exists('../../../../vendor/autoload.php')){
	require '../../../../vendor/autoload.php';
}
// Check if the staff is logged in
if (!isset($_SESSION['staff_id'])) {
	echo json_encode(['success' => false, 'message' => 'Staff not logged in']);
	exit;
}

// Get the logged-in staff ID
$staff_id = $_SESSION['staff_id'];

// Database connection details
if(file_exists('../../../../config.php')) {
	include('../../../../config.php');
}
// Check connection
if ($conn->connect_error) {
	echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
	exit;
}


// Check if the assignments table exists, if not, create it
$tableCheckQuery = "CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id VARCHAR(50) NOT NULL,
    class VARCHAR(50) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    due_date DATE NOT NULL,
    details TEXT NOT NULL,
    FOREIGN KEY (staff_id) REFERENCES staffs(staff_id)
)";

if ($conn->query($tableCheckQuery) === false) {
	echo json_encode(['success' => false, 'message' => 'Error creating table: ' . $conn->error]);
	exit;
}

// Check if data is sent via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Get form data
	$assignment_class    = $_POST['assignment_class'];
	$assignment_subject  = $_POST['assignment_subject'];
	$assignment_due_date = $_POST['assignment_due_date'];
	$assignment_details  = $_POST['assignment_details'];
	$_SESSION['assignment_data'] = $assignment_details;
	$_SESSION['assignment_class'] = $assignment_class;
	$_SESSION['assignment_subject'] = $assignment_subject;
	$_SESSION['assignment_due_date'] = $assignment_due_date;
	// Validate required fields
	if (empty($assignment_class) || empty($assignment_subject) || empty($assignment_due_date) || empty($assignment_details)) {
		echo json_encode(['success' => false, 'message' => 'All fields are required']);
		exit;
	}

	// Check if the students table exists
	$tableCheckQuery = "SHOW TABLES LIKE 'students'";
	$tableCheckResult = $conn->query($tableCheckQuery);

	if ($tableCheckResult->num_rows === 0) {
		echo json_encode([
			'success' => false,
			'message' => 'Students table not found in the database.'
		]);
		exit;
	}

	// Query to get students' emails in the specific class
	$emailQuery = $conn->prepare("SELECT email FROM students WHERE class = ?");
	$emailQuery->bind_param("s", $assignment_class);
	$emailQuery->execute();
	$emailResult = $emailQuery->get_result();

	// Check if any students exist in the specified class
	if ($emailResult->num_rows > 0) {
		$emails = [];
		while ($row = $emailResult->fetch_assoc()) {
			$emails[] = $row['email'];
		}
		// Send an email notification to each student
		$emailSuccess = true;
		foreach ($emails as $email) {

			if (class_exists(\SSIP\EmailHelper\sendEmail::class)) {
				$response = \SSIP\EmailHelper\sendEmail::sendEmail($email, 'assignment');

				if (!$response) {
					$emailSuccess = false;
					break;
				}
			}
		}

		// If all emails were sent successfully, insert assignment data
		if ($emailSuccess) {
			$stmt = $conn->prepare("INSERT INTO assignments (staff_id, class, subject, due_date, details) VALUES (?, ?, ?, ?, ?)");
			$stmt->bind_param("sssss", $staff_id, $assignment_class, $assignment_subject, $assignment_due_date, $assignment_details);

			if ($stmt->execute()) {
				echo json_encode([
					'success' => true,
					'message' => 'Assignment saved and notifications sent to students.'
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Error saving assignment: ' . $stmt->error
				]);
			}

			// Close the statement
			$stmt->close();
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error sending email notifications.'
			]);
		}
	} else {
		// No students found in the class
		echo json_encode([
			'success' => false,
			'message' => 'No students found in the specified class.'
		]);
	}

	// Close the email query
	$emailQuery->close();
}

// Close the connection
$conn->close();
?>
