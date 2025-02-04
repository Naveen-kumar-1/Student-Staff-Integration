<?php
session_start();
if(!isset($_SESSION['logged_in'])){
	header("location: ../../");
}
function getSiteUrl()
{
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$host = $_SERVER['HTTP_HOST'];

	return rtrim($protocol . $host, '/');
}

  ?>
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
    <div class="student-container" id="students" style="display:none;">
        <button class="delete-all-btn" id="deleteAllBtn">Delete All Students</button>
        <div id="deleteModal" class="modal">
            <div class="modal-content">
                <h2>Are you sure you want to delete all students?</h2>
                <div class="modal-buttons">
                    <button class="modal-btn" id="confirmDeleteBtn">Yes, Delete</button>
                    <button class="cancel-btn" id="cancelDeleteBtn">Cancel</button>
                </div>
                <p class="success-message" id="successMessage"></p>
            </div>
        </div>
        <input type="text" id="searchInput" placeholder="Search students..." style="margin-bottom: 20px; padding: 10px; width: 100%;">
		<?php
		if (file_exists('../../config.php')) {
			include '../../config.php';
		}

		// Check if the students table exists
		$tableCheckQuery = "SHOW TABLES LIKE 'students'";
		$tableCheckResult = mysqli_query($conn, $tableCheckQuery);

		if (mysqli_num_rows($tableCheckResult) > 0) {
			// The table exists, proceed with querying the student data
			$query = "SELECT first_name, last_name, password, adhaar, gender, student_id, email, phone, blood_group, class, image_url, address, created_at FROM students";
			$result = mysqli_query($conn, $query);

			if (mysqli_num_rows($result) > 0) {
				// Loop through each student record
				while ($row = mysqli_fetch_assoc($result)) {
					?>

                    <div class="student-card" data-name="<?php echo $row['first_name'] . ' ' . $row['last_name']; ?>"
                         data-id="<?php echo $row['student_id']; ?>"
                         data-class="<?php echo $row['class']; ?>"
                         data-email="<?php echo $row['email']; ?>"
                         data-phone="<?php echo $row['phone']; ?>">

                        <div class="card-header">
                            <img src="<?php echo getSiteUrl() . '/Student-Staff-Integration/' . $row['image_url']; ?>" alt="Student Image" class="student-image">
                            <div class="card-info">
                                <h2 class="student-name"><?php echo $row['first_name'] . " " . $row['last_name']; ?></h2>
                                <p class="student-id">ID: <?php echo $row['student_id']; ?></p>
                                <p class="student-class">Class: <?php echo $row['class']; ?></p>
                            </div>
                        </div>
                        <div class="card-body">
                            <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
                            <p><strong>Phone:</strong> <?php echo $row['phone']; ?></p>
                            <p><strong>Gender:</strong> <?php echo $row['gender']; ?></p>
                            <p><strong>Adhaar:</strong> <?php echo $row['adhaar']; ?></p>
                            <p><strong>Address:</strong> <?php echo $row['address']; ?></p>
                            <p><strong>Blood Group:</strong> <?php echo $row['blood_group']; ?></p>
                            <p><strong>Created At:</strong> <?php echo $row['created_at']; ?></p>
                        </div>
                        <div class="card-actions">
                            <button class="delete-btn">Delete</button>
                        </div>
                    </div>

					<?php
				}
			} else {
				echo "No Student details found!";
			}
		} else {
			// The table doesn't exist
			echo "<p>Student table not found in the database!</p>";
		}
		?>


    <!-- Custom Delete Confirmation Popup -->
		<div id="deleteConfirmationPopup" class="modal">
			<div class="modal-content">
				<h2>Delete Student</h2>
				<p>Are you sure you want to delete this student?</p>
				<div class="modal-buttons">
					<button id="confirmDelete" class="confirm-btn">Yes, Delete</button>
					<button id="cancelDelete" class="cancel-btn">Cancel</button>
				</div>
			</div>
		</div>

	</div>

<div class="staff-container" id="staffs" style="display:none; ">
    <button class="delete-all-btn" id="deleteAllStaffBtn">Delete All Staff</button>
    <!-- Custom Delete All Staff Confirmation Popup -->
    <div id="staffalldeletemodal" class="modal">
        <div class="modal-content">
            <h2>Are you sure you want to delete all staff records and their images?</h2>
            <div class="modal-buttons">
                <button class="modal-btn" id="confirmDeleteAllStaffBtn">Yes, Delete</button>
                <button class="cancel-btn" id="cancelDeleteAllStaffBtn">Cancel</button>
            </div>
            <p class="success-message" id="deleteAllSuccessMessage"></p>
        </div>
    </div>

    <input type="text" id="searchInputStaff" placeholder="Search staff..." style="margin-bottom: 20px; padding: 10px; width: 100%;">

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Are you sure you want to delete all staff?</h2>
            <div class="modal-buttons">
                <button class="modal-btn" id="confirmDeleteBtn">Yes, Delete</button>
                <button class="cancel-btn" id="cancelDeleteBtn">Cancel</button>
            </div>
            <p class="success-message" id="successMessage"></p>
        </div>
    </div>

	<?php
	if (file_exists('../../config.php')) {
		include '../../config.php';
	}

	// Check if the staff table exists
	$tableCheckQuery = "SHOW TABLES LIKE 'staffs'";
	$tableCheckResult = mysqli_query($conn, $tableCheckQuery);

	if (mysqli_num_rows($tableCheckResult) > 0) {
		// The table exists, proceed with querying the staff data
		$query = "SELECT id, first_name, last_name, staff_id, email, phone, gender, blood_group, class_adviser, position, image_url, address, created_at FROM staffs";
		$result = mysqli_query($conn, $query);

		if (mysqli_num_rows($result) > 0) {
			// Loop through each staff record
			while ($row = mysqli_fetch_assoc($result)) {
				?>

                <div class="staff-card" data-name="<?php echo $row['first_name'] . ' ' . $row['last_name']; ?>"
                     data-id="<?php echo $row['staff_id']; ?>"
                     data-position="<?php echo $row['position']; ?>"
                     data-email="<?php echo $row['email']; ?>"
                     data-phone="<?php echo $row['phone']; ?>">

                    <div class="card-header">
                        <img src="<?php echo getSiteUrl() . '/Student-Staff-Integration/' . $row['image_url']; ?>" alt="Staff Image" class="staff-image">
                        <div class="card-info">
                            <h2 class="staff-name"><?php echo $row['first_name'] . " " . $row['last_name']; ?></h2>
                            <p class="staff-id">ID: <?php echo $row['staff_id']; ?></p>
                            <p class="staff-position">Position: <?php echo $row['position']; ?></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
                        <p><strong>Phone:</strong> <?php echo $row['phone']; ?></p>
                        <p><strong>Gender:</strong> <?php echo $row['gender']; ?></p>
                        <p><strong>Blood Group:</strong> <?php echo $row['blood_group']; ?></p>
                        <p><strong>Class Adviser:</strong> <?php echo $row['class_adviser']; ?></p>
                        <p><strong>Address:</strong> <?php echo $row['address']; ?></p>
                        <p><strong>Created At:</strong> <?php echo $row['created_at']; ?></p>
                    </div>
                    <div class="card-actions">
                        <button class="staff-delete-btn" id="staff-delete">Delete</button>

                    </div>
                </div>
                <div id="staffdeleteModal" class="modal">
                    <div class="modal-content">
                        <h2>Are you sure you want to delete this staff record?</h2>
                        <div class="modal-buttons">
                            <button class="modal-btn" id="staffconfirmDeleteBtn">Yes, Delete</button>
                            <button class="cancel-btn" id="staffcancelDeleteBtn">Cancel</button>
                        </div>
                        <p class="success-message" id="successMessage"></p>
                    </div>
                </div>

				<?php
			}
		} else {
			echo "No staff details found!";
		}
	} else {
		// The table doesn't exist
		echo "<p>Staff table not found in the database!</p>";
	}
	?>
</div>
</div>


<script src="assets/script.js"></script>
</body>
</html>
