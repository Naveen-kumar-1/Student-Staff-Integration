<?php
$host = 'mysql';  // Change 'localhost' to the MySQL service name in Docker Compose
$user = 'root';
$password = 'root_password';  // The same password you set in your docker-compose.yml
$db = 'student_staff_integration';

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
?>
