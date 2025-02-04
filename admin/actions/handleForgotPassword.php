<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ( file_exists( '../../vendor/autoload.php' ) ) {
	require '../../vendor/autoload.php';

}
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	// Validate email
	$email = $_POST['forget-pass-email'];

	if ( $email ) {
		if ( file_exists( '../config.php' ) ) {
			include '../config.php';
		}

		$query  = "SELECT * FROM `student-staff-integration-admin`";
		$result = $conn->query( $query );

		if ( $result->num_rows > 0 ) {
			$otpSent = false;
			while ( $row = $result->fetch_assoc() ) {
				if ( $email === $row['email'] ) {
					try {
						class_exists( \SSIP\EmailHelper\sendEmail::class );
						{
							$sendEmail = \SSIP\EmailHelper\sendEmail::sendEmail( $email );
						}

						if ( $sendEmail ) {
							echo json_encode( [
								'type'     => 'success',
								'icon'     => 'bx bxs-check-circle',
								'title'    => 'Success',
								'text'     => 'OTP has been sent to your email!',
								'redirect' => 'templates/VerifyOtp/otpVerificationPage.html',
							] );
							exit;
						}
					} catch ( Exception $e ) {

						echo json_encode( [
							'type'  => 'error',
							'icon'  => 'bx bxs-error',
							'title' => 'Error',
							'text'  => "OTP could not be sent. Mailer Error"
						] );
						exit;
					}
				}

			}
			// If no match found
			echo json_encode( [
				'type'  => 'error',
				'icon'  => 'bx bxs-x-circle',
				'title' => 'Error',
				'text'  => 'Email not found in the database.'
			] );
		} else {
			echo json_encode( [
				'type'  => 'error',
				'icon'  => 'bx bxs-x-circle',
				'title' => 'Database Error',
				'text'  => 'No users found in the database.'
			] );
		}
	} else {
		echo json_encode( [
			'type'  => 'error',
			'icon'  => 'bx bxs-x-circle',
			'title' => 'Invalid Email',
			'text'  => 'Please provide a valid email address.'
		] );
	}
} else {
	echo json_encode( [
		'type'  => 'error',
		'icon'  => 'bx bxs-error',
		'title' => 'Error',
		'text'  => 'Invalid request method.'
	] );
}
exit;