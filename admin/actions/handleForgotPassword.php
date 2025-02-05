<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use SSIP\EmailHelper\sendEmail;

if (file_exists('../../vendor/autoload.php')) {
	require '../../vendor/autoload.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Validate email
	$email = $_POST['forget-pass-email'];

	if ($email) {
		if (file_exists('../config.php')) {
			include '../config.php';
		}

		// Modify query to check for the email directly
		$query  = "SELECT * FROM `student-staff-integration-admin` WHERE email = '$email'";
		$result = $conn->query($query);
		if ($result && $result->num_rows > 0) {
			try {
				// Email found, proceed to send OTP
				$sendEmail = sendEmail::sendEmail($email, 'otp');

				if ($sendEmail) {
					echo json_encode([
						'type'     => 'success',
						'icon'     => 'bx bxs-check-circle',
						'title'    => 'Success',
						'text'     => 'OTP has been sent to your email!',
						'redirect' => 'templates/VerifyOtp/otpVerificationPage.html',
					]);
					exit;
				}
			} catch (Exception $e) {
				// Error sending the email
				echo json_encode([
					'type'  => 'error',
					'icon'  => 'bx bxs-error',
					'title' => 'Error',
					'text'  => "OTP could not be sent. Mailer Error: " . $e->getMessage()
				]);
				exit;
			}
		} else {
			// Email not found in the database
			echo json_encode([
				'type'  => 'error',
				'icon'  => 'bx bxs-x-circle',
				'title' => 'Error',
				'text'  => 'Email not found in the database.'
			]);
		}
	} else {
		// Invalid email address provided
		echo json_encode([
			'type'  => 'error',
			'icon'  => 'bx bxs-x-circle',
			'title' => 'Invalid Email',
			'text'  => 'Please provide a valid email address.'
		]);
	}
} else {
	// Invalid request method
	echo json_encode([
		'type'  => 'error',
		'icon'  => 'bx bxs-error',
		'title' => 'Error',
		'text'  => 'Invalid request method.'
	]);
}
exit;
