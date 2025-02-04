<?php
if(file_exists('../../../config.php')){
	include '../../../config.php';
}

if (isset($_POST['student_id'])) {
	$student_id = $_POST['student_id'];

	// Get the student details including the image URL
	$query = "SELECT image_url FROM students WHERE student_id = '$student_id'";
	$result = mysqli_query($conn, $query);

	if ($result) {
		$student = mysqli_fetch_assoc($result);
		$image_url = $student['image_url'];

		// Delete the student record from the database
		$delete_query = "DELETE FROM students WHERE student_id = '$student_id'";
		if (mysqli_query($conn, $delete_query)) {

			// Construct the full image path
				$image_path = $_SERVER['DOCUMENT_ROOT'] . '/Student-Staff-Integration/' . $image_url;

			// Check if the file exists, then delete the image
			if (file_exists($image_path)) {
				unlink($image_path); // Deletes the image from the server
			}
			$studentImagesFolder = $_SERVER['DOCUMENT_ROOT'] . "/Student-Staff-Integration/uploads/student_images/";

// Check if the assignment folder exists and is empty
			if (is_dir($studentImagesFolder)) {
				$files = array_diff( scandir( $studentImagesFolder ), [ '.', '..' ] ); // Exclude . and .. from the list
				if ( empty( $files ) ) {
					// Assignment folder is empty, delete it
					rmdir( $studentImagesFolder );

				}
			}
			$uploadsFolder = $_SERVER['DOCUMENT_ROOT'] . "/Student-Staff-Integration/uploads";

// Check if the assignment folder exists and is empty
			if (is_dir($uploadsFolder)) {
				$files = array_diff( scandir( $uploadsFolder ), [ '.', '..' ] ); // Exclude . and .. from the list
				if ( empty( $files ) ) {
					// Assignment folder is empty, delete it
					rmdir( $uploadsFolder );

				}
			}
			// Return a success message
			echo json_encode(['status' => 'success']);
		} else {
			// Return an error message if deletion fails
			echo json_encode(['status' => 'error', 'message' => 'Failed to delete student']);
		}
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Student not found']);
	}
} else {
	echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
