<?php

namespace SSIP\EmailHelper;
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ( file_exists( '../vendor/autoload.php' ) ) {
	require '../vendor/autoload.php';
}

class sendEmail {

	public static function sendEmail( $email, $action ) {
		if($action === 'otp') {
			$otp             = rand( 100000, 999999 );
			$_SESSION['otp'] = $otp;
			$subject         = 'Your OTP Code';
			$body            = "<p>Your OTP code is <strong>$otp</strong>. This code is valid for 10 minutes.</p>";
			$alterBody       = "Your OTP code is $otp. This code is valid for 10 minutes.";
			return self::sendEmailToTheRecipient( $email, $subject, $body, $alterBody );

		} elseif ($action === 'assignment') {
			$assignment_details  = $_SESSION['assignment_details'];
			$assignment_class    = $_SESSION['assignment_class'];
			$assignment_subject  = $_SESSION['assignment_subject'];
			$assignment_due_date = $_SESSION['assignment_due_date'];

			// Build the subject for the new assignment
			$subject = "New Assignment: $assignment_subject Due on $assignment_due_date";

			// Build the HTML body for the new assignment email
			$body = "
			<p>Dear Student,</p>
			<p>A new assignment has been added for your class <strong>$assignment_class</strong>.</p>
			<p><strong>Subject:</strong> $assignment_subject</p>
			<p><strong>Due Date:</strong> $assignment_due_date</p>
			<p><strong>Details:</strong></p>
			<p>$assignment_details</p>
			<p>Please make sure to complete and submit it by the due date.</p>
			<p>Best Regards,<br>By Our Department</p>
		";

			// Plain-text version of the email (alternative body)
			$altBody = "
			Dear Student,

			A new assignment has been added for your class $assignment_class.

			Subject: $assignment_subject
			Due Date: $assignment_due_date
			Details: $assignment_details

			Please make sure to complete and submit it by the due date.

			Best Regards,
			By Our Department
		";

			return self::sendEmailToTheRecipient($email, $subject, $body, $altBody);

		} elseif ($action === 'update_assignment') {
			$assignment_details  = $_SESSION['assignment_data']['assignment_details'];
			$assignment_class    = $_SESSION['assignment_data']['assignment_class'];
			$assignment_subject  = $_SESSION['assignment_data']['assignment_subject'];
			$assignment_due_date = $_SESSION['assignment_data']['assignment_due_date'];

			// Build the subject for the updated assignment
			$subject = "Updated Assignment: $assignment_subject Due on $assignment_due_date";

			// Build the HTML body for the updated assignment email
			$body = "
			<p>Dear Student,</p>
			<p>An assignment has been updated for your class <strong>$assignment_class</strong>.</p>
			<p><strong>Subject:</strong> $assignment_subject</p>
			<p><strong>Due Date:</strong> $assignment_due_date</p>
			<p><strong>Updated Details:</strong></p>
			<p>$assignment_details</p>
			<p>Please make sure to review the changes and complete the assignment by the due date.</p>
			<p>Best Regards,<br>Your School</p>
		";

			// Plain-text version of the email (alternative body)
			$altBody = "
			Dear Student,

			An assignment has been updated for your class $assignment_class.

			Subject: $assignment_subject
			Due Date: $assignment_due_date
			Updated Details: $assignment_details

			Please make sure to review the changes and complete the assignment by the due date.

			Best Regards,
			By Our Department
		";

			return self::sendEmailToTheRecipient($email, $subject, $body, $altBody);
		}

		// Step 3: Send OTP via email using PHPMailer
	}

	public  static function sendEmailToTheRecipient( $to,$subject,$message,$alternative ) {
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
			 $mail->setFrom( 'codemavericknk@gmail.com', 'Student Staff Integration Portal' );
			 $mail->addAddress( $to );

			 // Content
			 $mail->isHTML( true );
			 $mail->Subject = $subject;
			 $mail->Body    =$message;
			 $mail->AltBody = $alternative;

			 // Send the email
			 $mail->send();

			 return true; // Successfully sent OTP
		 } catch ( Exception $e ) {
			 // Log the error message for debugging
			 error_log( "Mailer Error: {$mail->ErrorInfo}" );

			 return "Error: Mailer Error: {$mail->ErrorInfo}";
		 }
	}
}