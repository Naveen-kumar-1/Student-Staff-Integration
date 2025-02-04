<?php
if (file_exists('../../../config.php')) {
	include '../../../config.php';
}

function getServerPath()
{
	// Get the document root and correct the path for student images folder
	return $_SERVER['DOCUMENT_ROOT'] . '/Student-Staff-Integration/uploads/student_images/';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteAll') {
	// Delete all records from the students table (you may want to use truncate or delete instead of drop)
	$deleteQuery = "DROP TABLE IF EXISTS students";
	if (mysqli_query($conn, $deleteQuery)) {

		// Path to student images folder
		$studentImagesFolder = getServerPath();

		// Delete all images inside the folder
		$files = glob($studentImagesFolder . '*'); // Get all file names
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file); // Delete the file
			}
		}

		// Optionally, remove the student_images folder itself if it exists
		if (is_dir($studentImagesFolder)) {
			rmdir($studentImagesFolder); // Remove the folder
		}
		$uploadsFolder = $_SERVER['DOCUMENT_ROOT'] . "/Student-Staff-Integration/uploads";
		if (is_dir($uploadsFolder)) {
			$files = array_diff( scandir( $uploadsFolder ), [ '.', '..' ] ); // Exclude . and .. from the list
			if ( empty( $files ) ) {
				// Assignment folder is empty, delete it
				rmdir( $uploadsFolder );

			}
		}

		echo 'All students deleted successfully';
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Failed to delete students']);
	}

	mysqli_close($conn); // Close the database connection
}
?>
