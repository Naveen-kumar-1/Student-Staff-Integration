<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Dashboard</title>
	<link rel="stylesheet" href="assets/style.css">
	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
	<!-- Add this to the head section of your HTML file -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMwI5f8rr6R8/BnQ2Dz/WxbgCeEytp47b1Mupp0" crossorigin="anonymous">

</head>
<body>
<div class="notification"></div>
	<h1>Welcome Admin <i class='bx bx-crown'></i>



	</h1>
<div class="navbar">
	<button onclick="showContent('admin')">Edit Admin</button>

	<button onclick="showContent('students')">Edit Students</button>
	<button onclick="showContent('staffs')">Edit Staffs</button>
</div>

<div class="content">

	<div id="admin" class="content-div admin" style="display: flex;">
		<div class="container">
			<h2>Update Admin Email & Password</h2>

			<form id="adminForm" onsubmit="handleSubmit(event)">
				<div class="input-group">
					<label for="email">Email:</label>
					<div class="email-container">
					<input type="email" id="email" name="email" placeholder="Enter admin email" required>
					</div>
				</div>
				<div class="input-group">
					<label for="password">Password:</label>
					<div class="password-container">
						<input type="password" id="password" name="password" placeholder="Enter new password" required>
						<i id="togglePassword" class="bx bxs-low-vision"></i>
					</div>
				</div>
				<button type="submit" class="btn">Save</button>
			</form>


		</div>
	</div>
	<div id="students" class="content-div" style="display: none;">Edit Students Content</div>
	<div id="staffs" class="content-div" style="display: none;">Edit Staffs Content</div>
</div>

<script src="assets/script.js"></script>
</body>
</html>
