<?php
session_start();
// Get the raw POST data from the request
$inputData = file_get_contents( "php://input" );

// Decode the JSON data into an associative array
$data = json_decode( $inputData, true );

// Check if the OTP is received
if ( isset( $data['otp'] ) ) {
	$otp_post = $data['otp'];
	if ( $_SESSION['otp'] == $otp_post ) {

		echo json_encode( [
			'type'     => 'success',
			'title'    => 'Success',
			'text'     => 'OTP verified successfully',
			'redirect' => '../../actions/changePassword.php'
		] );


	} else {

		echo json_encode( [
			'type'  => 'error',
			'title' => 'Error',
			'text'  => 'No user found with the provided email.'
		] );
	}
} else {
	// OTP not sent in request
	echo json_encode( [
		'type'  => 'error',
		'title' => 'Error',
		'text'  => 'OTP is missing in the request.'
	] );
}