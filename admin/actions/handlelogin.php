<?php
session_start();
if ( file_exists( '../config.php' ) ) {
	include '../config.php';
}

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

	$email    = $_POST['email'] ?? '';
	$password = $_POST['password'] ?? '';


	// Validation
	if ( empty( $email ) || empty( $password ) ) {
		echo json_encode( [
			'type'  => 'error',
			'icon'  => 'bx bxs-x-circle',
			'title' => 'Error',
			'text'  => 'Email or Password cannot be empty.'
		] );
		exit;
	}

	$query  = "SELECT * FROM `student-staff-integration-admin`";
	$result = $conn->query( $query );

	if ( $result->num_rows > 0 ) {
		while ( $row = $result->fetch_assoc() ) {
			if ( $email === $row['email'] && $password === $row['password'] ) {
				echo json_encode( [
					'type'     => 'success',
					'icon'     => 'bx bxs-check-circle',
					'title'    => 'Success',
					'text'     => 'Login Successful...!',
					'redirect' => 'templates/HomePage/admin.php'
				] );
				$_SESSION['logged_in'] = true;
				exit;
			}
		}
		echo json_encode( [
			'type'  => 'error',
			'icon'  => 'bx bxs-x-circle',
			'title' => 'Error',
			'text'  => 'Invalid Email or Password.'
		] );
	} else {
		echo json_encode( [
			'type'  => 'error',
			'icon'  => 'bx bxs-x-circle',
			'title' => 'Error',
			'text'  => 'No users found.'
		] );
	}
}