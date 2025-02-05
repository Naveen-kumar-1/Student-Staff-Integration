<?php
session_start();



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteAllStaff') {
	// Step 1: Connect to the database
	if (file_exists('../../../config.php')) {
		include '../../../config.php';
	}

	// Step 2: Query to get all staff image URLs
	$query = "SELECT image_url FROM staffs";
	$result = mysqli_query($conn, $query);

	if (mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$imageUrl = $row['image_url'];

			// Step 3: Build the image file path
			$imagePath = $_SERVER['DOCUMENT_ROOT'] . '/Student-Staff-Integration/uploads/staff_images/' . basename($imageUrl);

			// Step 4: Delete the image file if it exists
			if (file_exists($imagePath)) {
				unlink($imagePath);  // Delete the image file
			}
		}
	}

	// Step 5: Disable foreign key checks and attempt to delete the table
	mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

	$deleteQuery = "DROP TABLE IF EXISTS staffs";  // Add IF EXISTS for safety
	if (mysqli_query($conn, $deleteQuery)) {
		// Step 6: Check if the staff images directory is empty and remove it
		$dirPath = $_SERVER['DOCUMENT_ROOT'] . '/Student-Staff-Integration/uploads/staff_images/';

		// Check if the directory is empty
		if (is_dir($dirPath) && count(scandir($dirPath)) == 2) {
			rmdir($dirPath);  // Remove the directory if it's empty
		}
		$uploadsFolder = $_SERVER['DOCUMENT_ROOT'] . "/Student-Staff-Integration/uploads";

		// Check if the assignment folder exists and is empty
		if (is_dir($uploadsFolder)) {
			$files = array_diff(scandir($uploadsFolder), [ '.', '..' ]); // Exclude . and .. from the list
			if (empty($files)) {
				// Assignment folder is empty, delete it
				rmdir($uploadsFolder);
			}
		}
		echo json_encode(['status' => 'success', 'message' => 'All staff records and images deleted successfully!']);
	} else {
		// Capture and display any error
		echo json_encode(['status' => 'error', 'message' => 'Failed to delete staff records! Error: ' . mysqli_error($conn)]);
	}

	// Step 7: Enable foreign key checks again
	mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

	mysqli_close($conn); // Close the database connection
}
