<?php
if (file_exists('../../../config.php')) {
	include '../../../config.php';
}

// Function to get the site URL dynamically
function getSiteUrl()
{
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$host = $_SERVER['HTTP_HOST'];

	return rtrim($protocol . $host, '/');
}

// Handle the delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteStaff' && isset($_POST['staff_id'])) {
	$staffId = mysqli_real_escape_string($conn, $_POST['staff_id']); // Secure the staff ID input

	// Query to fetch the staff record and image details
	$query = "SELECT image_url FROM staffs WHERE staff_id = '$staffId'";
	$result = mysqli_query($conn, $query);

	if (mysqli_num_rows($result) > 0) {

		// Fetch the staff data
		$row = mysqli_fetch_assoc($result);
		$imageUrl = $row['image_url'];

		// File path for the image (using absolute file path on the server)
		$imagePath = $_SERVER['DOCUMENT_ROOT'] . '/Student-Staff-Integration/uploads/staff_images/' . basename($imageUrl);
		if (file_exists($imagePath)) {
			unlink($imagePath);
		}
		$staffImagesFolder = $_SERVER['DOCUMENT_ROOT'] . "/Student-Staff-Integration/uploads/staff_images/";

// Check if the assignment folder exists and is empty
		if (is_dir($staffImagesFolder)) {
			$files = array_diff( scandir( $staffImagesFolder ), [ '.', '..' ] ); // Exclude . and .. from the list
			if ( empty( $files ) ) {
				// Assignment folder is empty, delete it
				rmdir( $staffImagesFolder );

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
		// Delete the staff record from the database
		$deleteQuery = "DELETE FROM staffs WHERE staff_id = '$staffId'";
		if (mysqli_query($conn, $deleteQuery)) {
			// If the record is deleted, delete the associated image file


			echo json_encode(['status' => 'success', 'message' => 'Staff record deleted successfully!']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Failed to delete staff record!']);
		}
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Staff not found!']);
	}

	mysqli_close($conn); // Close the database connection
}
?>
