<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure the vendor/autoload.php exists
if ( file_exists( '../../vendor/autoload.php' ) ) {
	require_once '../../vendor/autoload.php';
}

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	if ( file_exists( "../config.php" ) ) {
		include '../config.php';
	}

	$query  = "SELECT * FROM `student-staff-integration-admin`";
	$result = $conn->query( $query );

	if ( $result->num_rows > 0 ) {
		$otpSent = false;
		while ( $row = $result->fetch_assoc() ) {
			$email = $row['email'];

			try {
				// Correctly check if the class exists and send the email
				if ( class_exists( '\SSIP\EmailHelper\sendEmail' ) ) {
					$sendEmail = \SSIP\EmailHelper\sendEmail::sendEmail( $email,'otp' );

					if ( $sendEmail ) {
						echo json_encode( [
							'type'  => 'success',
							'icon'  => 'bx bxs-check-circle',
							'title' => 'Success',
							'text'  => 'OTP has been sent to your email!',
						] );
						exit; // Stop processing after sending OTP successfully
					}
				} else {
					throw new Exception( "Email sending class not found." );
				}
			} catch ( Exception $e ) {
				echo json_encode( [
					'type'  => 'error',
					'icon'  => 'bx bxs-error',
					'title' => 'Error',
					'text'  => "OTP could not be sent. Mailer Error: " . $e->getMessage(),
				] );
				exit;
			}
		}
	} else {
		echo json_encode( [
			'type'  => 'error',
			'icon'  => 'bx bxs-error',
			'title' => 'Error',
			'text'  => 'No records found in the database.',
		] );
	}
}
?>