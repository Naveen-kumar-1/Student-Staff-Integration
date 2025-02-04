
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title>Carreer-Compass | Admin</title>
		<link rel="stylesheet" href="assets/css/style.css"/>
		<link
			href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
			rel="stylesheet"
		/>
	</head>
	<body>
	<div class="student-staff-integration-admin">
		<div class="notification"></div>
		<div class="form-box">
			<form id="login-form">
				<h1 class="login-form-title">Login</h1>
				<div class="input-fields">
					<div class="input-box">
						<i class="bx bx-envelope"></i>
						<input
							type="email"
							placeholder="Enter your email.."
							name="email"
							class="email"
							required
						/>
					</div>
				</div>
				<div class="input-fields">
					<div class="input-box">
						<i class='bx bx-key'></i>
						<input
							type="password"
							placeholder="Enter your password.."
							name="password"
							class="password"
							id="password"
							required
						/>
						<i class='bx bx-low-vision password-bx' id="password-bx" style="cursor: pointer;"></i>
					</div>
				</div>
				<div class="forgot-pass">
    <span style="font-size: 14px; text-align:center; width:100%;" class="forgot-pass">Forgot password ?
      <span style="color: darkblue; text-decoration:underline;cursor:pointer" class="forgot-pass" id="forgot-pass"
            name="forget-pass">Click here</span>
    </span>
				</div>
				<div class="login-btn">
					<button class="login-sumbit-btn" type="submit" name="login-btn">Login</button>
				</div>
			</form>


			<form id="forgot-pass-form" class="forgot-password" action="">
				<i class='bx bx-left-arrow-alt' id="back-to-login" title="Back to Login Form"></i>
				<h1 class="login-form-title">Forgot Password...<i class='bx bx-dizzy'></i></h1>
				<p style="text-align: center; font-size:14px;color:#333;">Dont'worry...<i class='bx bx-wink-tongue'></i>
				</p>
				<div class="input-fields">
					<div class="input-box">
						<i class="bx bx-envelope"></i>
						<input
							type="forger-pass-email"
							placeholder="Enter you email.."
							name="forget-pass-email"
							class="email"
							required
						/>
					</div>
				</div>
				<div class="login-btn ">
					<button class="login-sumbit-btn" type="submit">Sent OPT</button>
				</div>
			</form>
		</div>

	</div>
	</div>
	<script src="assets/js/script.js"></script>

	</body>
	</html>
<?php