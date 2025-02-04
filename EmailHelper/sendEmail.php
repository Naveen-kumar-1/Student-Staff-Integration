<?php

namespace SSIP\EmailHelper;
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ( file_exists( '../vendor/autoload.php' ) ) {
	require '../vendor/autoload.php';
}

class sendEmail {

	public static function sendEmail( $email ) {

		$otp             = rand( 100000, 999999 );
		$_SESSION['otp'] = $otp;


		// Step 3: Send OTP via email using PHPMailer
		$mail = new PHPMailer( true );

		try {
			// Server settings
			$mail->isSMTP();
			$mail->Host       = 'smtp.gmail.com'; // Replace with your SMTP server
			$mail->SMTPAuth   = true;
			$mail->Username   = 'codemavericknk@gmail.com'; // Your email
			$mail->Password   = 'gegv ykno xjdl tvoe'; // Your email app password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			$mail->Port       = 587;

			// Recipients
			$mail->setFrom( 'codemavericknk@gmail.com', 'Career - Compass' );
			$mail->addAddress( $email );

			// Content
			$mail->isHTML( true );
			$mail->Subject = 'Your OTP Code';
			$mail->Body    = "<p>Your OTP code is <strong>$otp</strong>. This code is valid for 10 minutes.</p>";
			$mail->AltBody = "Your OTP code is $otp. This code is valid for 10 minutes.";

			// Send the email
			$mail->send();

			return true; // Successfully sent OTP
		} catch ( Exception $e ) {
			// Log the error message for debugging
			error_log( "Mailer Error: {$mail->ErrorInfo}" );

			return "Error: OTP could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	}
}