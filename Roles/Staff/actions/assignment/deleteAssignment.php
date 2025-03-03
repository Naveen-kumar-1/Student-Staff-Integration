<?php
header('Content-Type: application/json');

// Get the data from the AJAX request
$data = json_decode(file_get_contents('php://input'), true);

// Validate required parameters
if (!isset($data['class_name'], $data['subject'], $data['due_date'], $data['assignment_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$className = basename($data['class_name']); // Prevent directory traversal
$subject = basename($data['subject']);
$dueDate = basename($data['due_date']);
$assignmentID = intval($data['assignment_id']); // Ensure it's an integer

// Directory where the assignment files are stored (absolute path)
$assignmentDirectory = $_SERVER['DOCUMENT_ROOT'] . "/Student-Staff-Integration/uploads/assignment/" . $className . "/" . $subject . "/" . $dueDate;

$response = ['success' => true, 'message' => '']; // Initialize response with success as true and empty message

// Function to delete a folder and its contents recursively
function deleteDirectory($dir)
{
    if (!is_dir($dir)) {
        return false;
    }

    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $filePath = $dir . DIRECTORY_SEPARATOR . $file;
        is_dir($filePath) ? deleteDirectory($filePath) : unlink($filePath);
    }

    return rmdir($dir);
}

$directoryDeleted = false;
$recordDeleted = false;

// Check if the assignment due date folder exists and delete it
if ($assignmentDirectory && is_dir($assignmentDirectory)) {
    if (deleteDirectory($assignmentDirectory)) {
        $directoryDeleted = true;
    } else {
        $response['success'] = false;
        $response['message'] = 'Failed to delete due date folder';
        echo json_encode($response);
        exit;
    }
}

// Database deletion logic
if(file_exists('../../../../config.php')) {
    include('../../../../config.php');
}

if ($conn->connect_error) {
    $response['success'] = false;
    $response['message'] = 'Database connection failed: ' . $conn->connect_error;
    echo json_encode($response);
    exit;
} else {
    // Check if the record exists in the database and delete it
    $stmt = $conn->prepare("DELETE FROM assignments WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $assignmentID);
        if ($stmt->execute()) {
            $recordDeleted = true;
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to remove record from database. Error: ' . $conn->error;
            $stmt->close();
            $conn->close();
            echo json_encode($response);
            exit;
        }
        $stmt->close();
    } else {
        $response['success'] = false;
        $response['message'] = 'Failed to prepare database query. Error: ' . $conn->error;
        $conn->close();
        echo json_encode($response);
        exit;
    }
    $conn->close();
}

// Now, check if the class folder is empty after deleting the due date folder
$classDirectory = $_SERVER['DOCUMENT_ROOT'] . "/Student-Staff-Integration/uploads/assignment/" . $className . "/" . $subject;
if (is_dir($classDirectory)) {
    $files = array_diff(scandir($classDirectory), ['.', '..']); // Exclude . and .. from the list
    // Debug output to check contents
    error_log(print_r($files, true)); // This will log the files in the directory to the error log

    if (empty($files)) {
        // Class folder is empty, delete it
        if (rmdir($classDirectory)) {
            $response['message'] .= ' and class folder deleted successfully';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to delete class folder';
            echo json_encode($response);
            exit;
        }
    }
}


// After deleting the class folder, check if the assignment folder is empty and delete it
// Now check if the class folder is empty and delete it if so
$classFolder = $_SERVER['DOCUMENT_ROOT'] . "/Student-Staff-Integration/uploads/assignment/" . $className;

// Check if the class folder exists and is empty
if (is_dir($classFolder)) {
    $files = array_diff(scandir($classFolder), ['.', '..']); // Exclude . and .. from the list
    if (empty($files)) {
        // Class folder is empty, delete it
        if (rmdir($classFolder)) {
            $response['message'] .= ' and class folder deleted successfully';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to delete class folder';
            echo json_encode($response);
            exit;
        }
    }
}
$assignmentFolder = $_SERVER['DOCUMENT_ROOT'] . "/Student-Staff-Integration/uploads/assignment/";

// Check if the assignment folder exists and is empty
if (is_dir($assignmentFolder)) {
    $files = array_diff(scandir($assignmentFolder), ['.', '..']); // Exclude . and .. from the list
    if (empty($files)) {
        // Assignment folder is empty, delete it
        if (rmdir($assignmentFolder)) {
            $response['message'] .= ' Assignment folder deleted successfully';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to delete assignment folder';
            echo json_encode($response);
            exit;
        }
    }
}
$uploadsFolder = $_SERVER['DOCUMENT_ROOT'] . "/Student-Staff-Integration/uploads";

// Check if the assignment folder exists and is empty
if (is_dir($uploadsFolder)) {
    $files = array_diff(scandir($uploadsFolder), ['.', '..']); // Exclude . and .. from the list
    if (empty($files)) {
        // Assignment folder is empty, delete it
        if (rmdir($uploadsFolder)) {
            $response['message'] .= ' Uploads folder deleted successfully';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to delete assignment folder';
            echo json_encode($response);
            exit;
        }
    }
}


// Prepare success message based on what was deleted
if ($directoryDeleted && $recordDeleted) {
    $response['message'] = 'Both due date folder and database record deleted successfully';
} elseif ($directoryDeleted) {
    $response['message'] = 'Due date folder deleted successfully';
} elseif ($recordDeleted) {
    $response['message'] = 'Database record deleted successfully';
} else {
    $response['success'] = false;
    $response['message'] = 'Neither the folder nor the database record were found';
}

// Return the JSON response
echo json_encode($response);
?>
