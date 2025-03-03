<?php

// Database connection
if(file_exists('../../../../config.php')) {
	include('../../../../config.php');
}
if(file_exists('../../../../vendor/autoload.php')){
	require '../../../../vendor/autoload.php';
}



// Collect form data
$first_name    = $_POST['first_name'];
$last_name     = $_POST['last_name'];
$student_id    = $_POST['student_id'];
$assignment_id = $_POST['assignment_id'];

// Handle file upload
if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] == 0) {
	$file     = $_FILES['assignment_file'];
	$file_tmp = $file['tmp_name'];

	// Check file size (max 10MB example)
	if ($file['size'] > 10485760) {
		echo json_encode(['success' => false, 'error' => 'File is too large']);
		exit;
	}

	// Get assignment details from database to determine the folder structure and new file name
	$assignment_query = "SELECT class, subject, due_date FROM assignments WHERE id = ?";
	$stmt = $conn->prepare($assignment_query);
	$stmt->bind_param('i', $assignment_id);

	if (!$stmt->execute()) {
		echo json_encode(['success' => false, 'error' => 'Failed to execute query']);
		exit;
	}

	$assignment_result = $stmt->get_result();
	$assignment = $assignment_result->fetch_assoc();

	if ($assignment) {
		// Create the folder path: uploads/{class}/{subject}/{due_date}/
		$folder_path = "../../../../uploads/assignment/" . $assignment['class'] . "/" . $assignment['subject'] . "/" . $assignment['due_date'];

		// Create folder if it doesn't exist
		if (!is_dir($folder_path)) {
			mkdir($folder_path, 0777, true);
		}

		// Construct the new file name: {subject}_{first_name}_{last_name}_{student_id}.pdf
		$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION); // Get file extension
		$new_file_name  = $assignment['subject'] . "_" . $first_name . "_" . $last_name . "_" . $student_id . "." . $file_extension;

		// Define the new file path
		$file_path = $folder_path . "/" . $new_file_name;

		if (file_exists($file_path)) {
			echo json_encode(['success' => false, 'error' => 'File already exists']);

		}

		// Move the file to the folder
		if (move_uploaded_file($file_tmp, $file_path)) {
			// File uploaded successfully

			// Get the staff email for the class and subject
			$staff_query = "SELECT email FROM staffs WHERE class_adviser = ?";
			$stmt_staff  = $conn->prepare($staff_query);
			$stmt_staff->bind_param('s', $assignment['class']);

			if (!$stmt_staff->execute()) {
				echo json_encode(['success' => false, 'error' => 'Failed to execute query to get staff email']);
				exit;
			}

			$result_staff = $stmt_staff->get_result();
			$staff        = $result_staff->fetch_assoc();

			if ($staff) {
				// Prepare email subject and body for staff
				$subject = "New Assignment Submission: " . $assignment['subject'];
				$body = "
                    <p>Dear Staff,</p>
                    <p>A student has submitted their assignment for the class <strong>" . $assignment['class'] . "</strong> in the subject <strong>" . $assignment['subject'] . "</strong>.</p>
                    <p><strong>Student Name:</strong> $first_name $last_name</p>
                    <p><strong>Student ID:</strong> $student_id</p>
                    <p><strong>Due Date:</strong> " . $assignment['due_date'] . "</p>
                    <p>Best Regards,<br>By Our Department</p>
                ";
				$altBody = "Dear Staff, A student has submitted their assignment for the class " . $assignment['class'] . " in the subject " . $assignment['subject'] . ". Student Name: $first_name $last_name, Student ID: $student_id, Due Date: " . $assignment['due_date'] . ". Best Regards, By Our Department";

				// Send email to the staff using the email helper
				if (class_exists('\SSIP\EmailHelper\sendEmail')) {
					try {
						\SSIP\EmailHelper\sendEmail::sendEmailToTheRecipient($staff['email'], $subject, $body, $altBody);
					} catch (Exception $e) {
						echo json_encode(['success' => false, 'error' => 'Email sending failed: ' . $e->getMessage()]);
						exit;
					}
				} else {
					echo json_encode(['success' => false, 'error' => 'EmailHelper class not found']);
					exit;
				}
			} else {
				echo json_encode(['success' => false, 'error' => 'Staff not found for the class and subject']);
				exit;
			}

			// Prepare data for updating the database with submission
			$submitted_student = [
				'student_id'   => $student_id,
				'first_name'   => $first_name,
				'last_name'    => $last_name,
				'file_name'    => $new_file_name,
				'class'        => $assignment['class'],
				'subject'      => $assignment['subject'],
				'due_date'     => $assignment['due_date'],
				'status'       => 'submitted',
				'submitted_at' => date('Y-m-d H:i:s')
			];

			// Check if the 'submitted_students' column exists, and add it if not
			$check_column_query = "SHOW COLUMNS FROM `assignments` LIKE 'submitted_students'";
			$check_result       = $conn->query($check_column_query);

			if ($check_result->num_rows == 0) {
				// If the column does not exist, alter the table to add it
				$alter_table_query = "ALTER TABLE `assignments` ADD `submitted_students` JSON NULL";
				if (!$conn->query($alter_table_query)) {
					echo json_encode(['success' => false, 'error' => 'Error adding column: ' . $conn->error]);
					exit;
				}
			}

			// Get the existing submitted_students data
			$submitted_query = "SELECT submitted_students FROM assignments WHERE id = ?";
			$stmt = $conn->prepare($submitted_query);
			$stmt->bind_param('i', $assignment_id);

			if (!$stmt->execute()) {
				echo json_encode(['success' => false, 'error' => 'Failed to execute query']);
				exit;
			}

			$result = $stmt->get_result();
			$assignment_data = $result->fetch_assoc();

			// Get the existing submitted_students array (or initialize it if empty)
			$submitted_students = json_decode($assignment_data['submitted_students'], true) ?? [];

			// Use the student ID as the index directly in the array
			$submitted_students[$student_id] = $submitted_student;

			// Update the assignment table with the new list of submitted students
			$submitted_students_json = json_encode($submitted_students);
			$update_query            = "UPDATE assignments SET submitted_students = ? WHERE id = ?";
			$stmt                    = $conn->prepare($update_query);
			$stmt->bind_param('si', $submitted_students_json, $assignment_id);

			if ($stmt->execute()) {
				echo json_encode(['success' => true, 'redirect' => true]);
			} else {
				echo json_encode(['success' => false, 'error' => 'Database update failed']);
			}
		} else {
			echo json_encode(['success' => false, 'error' => 'Error moving the file to destination']);
		}
	} else {
		echo json_encode(['success' => false, 'error' => 'Assignment not found']);
	}
} else {
	echo json_encode(['success' => false, 'error' => 'File not uploaded or invalid file']);
}
?>
