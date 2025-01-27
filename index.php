<?php
session_start(); // Start the session

// If the user is already logged in as staff, redirect to the staff page
if ( isset( $_SESSION['staff_logged_in'] ) && $_SESSION['staff_logged_in'] === true ) {
	header( "Location: Roles/Staff/" );
	exit();
}

// If the user is already logged in as a student, redirect to the student page
if ( isset( $_SESSION['student_logged_in'] ) && $_SESSION['student_logged_in'] === true ) {
	header( "Location: Roles/Student/" );
	exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Student Staff Integration Portal</title>
</head>
<body>
<div class="sstp-container">
    <div class="sst-box">
        <div class="sst-content">
            <div class="sst-index-box">
                <h1>Welcome to our Department</h1>
                <p>We are delighted to have you here. Our department strives to foster excellence in education,
                    research, and innovation, creating a nurturing environment for both students and staff.</p>
                <div class="sst-box-btn">
                    <button class="sign-up" onclick="showSignUp()">Sign up</button>
                    <button class="login" onclick="showLogin()">Log in</button>
                </div>
            </div>

            <!-- Login Form -->
            <div class="sst-login-form" id="login-form" style="display: none;">
                <h2>Login</h2>
                <label for="login-role">Role:</label>
                <select id="login-role" onchange="showLoginForm()">
                    <option value="">Select Role</option>
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                </select>
                <div id="login-credentials"></div>

                <button onclick="submitLogin()">Submit</button>
                </form>
            </div>

            <!-- Signup Form -->
            <div class="sst-signup-form" id="signup-form" style="display: none;">
                <h2>Sign Up</h2>


                <label for="signup-role">Role:</label>
                <select id="signup-role" onchange="showSignupForm()">
                    <option value="">Select Role</option>
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                </select>

                <div id="signup-credentials"></div>
                <button onclick="submitSignUp()">Sign Up</button>
            </div>
        </div>
        <img src="assets/images/admin-banner-2.gif" alt="Banner">
    </div>
</div>

<script src="assets/js/script.js"></script>
</body>
</html>
