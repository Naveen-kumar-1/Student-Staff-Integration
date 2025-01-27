<?php
header( 'Content-Type: application/json' );

// Get the data from the AJAX request
$data = json_decode( file_get_contents( 'php://input' ), true );

$className    = $data['class_name'];
$subject      = $data['subject'];
$dueDate      = $data['due_date'];
$assignmentID = $data['assignment_id'];

// Directory where the assignment files are stored based on due_date
$assignmentDirectory = '../../../../uploads/assignment/' . $subject . '/' . $dueDate;

$response = [];

// Recursive function to delete a folder and its contents
function deleteDirectory( $dir ) {
	if ( ! is_dir( $dir ) ) {
		return false;
	}

	$files = array_diff( scandir( $dir ), [ '.', '..' ] );
	foreach ( $files as $file ) {
		$filePath = $dir . '/' . $file;
		if ( is_dir( $filePath ) ) {
			deleteDirectory( $filePath ); // Recursively delete subdirectories
		} else {
			unlink( $filePath ); // Delete files
		}
	}

	return rmdir( $dir ); // Delete the now-empty folder
}

// Check if the folder exists
if ( is_dir( $assignmentDirectory ) ) {
	// Attempt to delete the folder and its contents
	if ( deleteDirectory( $assignmentDirectory ) ) {
		$response['success'] = true;
		$response['message'] = 'Due date folder and all contents deleted successfully';

		// Optional: If you also want to delete the assignment record from the database
		$db = new mysqli( 'localhost', 'username', 'password', 'database' ); // Use correct credentials
		if ( $db->connect_error ) {
			$response['success'] = false;
			$response['message'] = 'Database connection failed';
		} else {
			$stmt = $db->prepare( "DELETE FROM assignments WHERE id = ?" );
			$stmt->bind_param( 'i', $assignmentID );

			if ( $stmt->execute() ) {
				$response['message'] .= ' and record removed from database';
			} else {
				$response['message'] .= ' but failed to remove record from database';
			}
			$stmt->close();
			$db->close();
		}
	} else {
		$response['success'] = false;
		$response['message'] = 'Failed to delete due date folder';
	}
} else {
	$response['success'] = false;
	$response['message'] = 'Due date folder not found';
}

// Return the JSON response
echo json_encode( $response );
?>
