<?php
// Database connection
if(file_exists('../../../../config.php')) {
	include('../../../../config.php');
}

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Create tables if they don't exist
$sqlCreateTables = "
    CREATE TABLE IF NOT EXISTS `staffs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `first_name` VARCHAR(255) NOT NULL,
        `last_name` VARCHAR(255) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `adhaar` VARCHAR(20) NOT NULL,
        `gender` ENUM('male', 'female', 'other') NOT NULL,
        `staff_id` VARCHAR(50) NOT NULL UNIQUE,
        `email` VARCHAR(255) NOT NULL UNIQUE,
        `phone` VARCHAR(20) NOT NULL,
        `blood_group` VARCHAR(5) NOT NULL,
        `class_adviser` VARCHAR(50) NOT NULL,
        `position` VARCHAR(100) DEFAULT '',
        `image_url` VARCHAR(255) DEFAULT '',
        `address` TEXT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
";
$conn->query($sqlCreateTables);

// Get the POST data
$role = isset($_POST['role']) ? $_POST['role'] : '';
$first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
$last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
$adhaar = isset($_POST['adhaar']) ? $_POST['adhaar'] : '';
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
$staff_id = isset($_POST['staff_id']) ? $_POST['staff_id'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
$blood_group = isset($_POST['blood_group']) ? $_POST['blood_group'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$position = $_POST['position'] ?? 'Assistant Professor';
$address = isset($_POST['address']) ? $_POST['address'] : '';
$class_adviser = isset($_POST['staff_class']) ? $_POST['staff_class'] : '';

// Handle image upload for staff
$image_url = '';  // Default empty image URL

// Data validation
if (empty($first_name) || empty($last_name) || empty($adhaar) || empty($gender) || empty($staff_id) || empty($email) || empty($phone) || empty($blood_group) || empty($password) || empty($address) || empty($class_adviser)) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

// Check if email or staff_id already exists
$sql = "SELECT * FROM staffs WHERE email = ? OR staff_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]);
    exit;
}

$stmt->bind_param("ss", $email, $staff_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email or staff ID already exists."]);
    exit;
}

if ($role == 'staff') {
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == 0) {
        // Define the upload directory
        $uploadDir = '../../../../uploads/staff_images/'; // Relative to root

        // Ensure the directory exists, if not create it
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                echo json_encode(["success" => false, "message" => "Failed to create the directory."]);
                exit;
            }
        }

        // Get the file extension and generate a new image name based on staff ID
        $imageExtension = pathinfo($_FILES['image_url']['name'], PATHINFO_EXTENSION);
        $imageName = $staff_id . '.' . $imageExtension;
        $imagePath = $uploadDir . $imageName;

        // Validate file size and type
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if ($_FILES['image_url']['size'] > $maxFileSize) {
            echo json_encode(["success" => false, "message" => "File size exceeds the 5MB limit."]);
            exit;
        }

        if (!in_array($_FILES['image_url']['type'], $allowedMimeTypes)) {
            echo json_encode([
                "success" => false,
                "message" => "Invalid file type. Only JPEG, PNG, and GIF are allowed."
            ]);
            exit;
        }

        // Try to move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES['image_url']['tmp_name'], $imagePath)) {
            $errorMessage = error_get_last();
            echo json_encode([
                "success" => false,
                "message" => "Error uploading the image.",
                "error_details" => $errorMessage
            ]);
            exit;
        }

        // Image uploaded successfully, store the relative path
        $image_url = 'uploads/staff_images/' . $imageName;  // Correct relative URL for frontend
    }
}

// Prepare the insert query
$sql = "INSERT INTO `staffs` (`first_name`, `last_name`, `password`, `adhaar`, `gender`, `staff_id`, `email`, `phone`, `blood_group`, `position`, `image_url`, `address`,`class_adviser`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error preparing insert statement: " . $conn->error]);
    exit;
}

// Bind the parameters and execute
$stmt->bind_param("sssssssssssss", $first_name, $last_name, $password, $adhaar, $gender, $staff_id, $email, $phone, $blood_group, $position, $image_url, $address, $class_adviser);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Sign-up successful!", "redirect" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error executing insert: " . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
