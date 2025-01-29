<?php
session_start();
if ( ! isset( $_SESSION['staff_id'] ) ) {
	header( 'Location: /Student-Staff-Integration/' );
	exit;
}
$staff_id = $_SESSION['staff_id'];

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "student_staff_integration";

$conn = new mysqli( $servername, $username, $password, $dbname );

if ( $conn->connect_error ) {
	die( "Connection failed: " . $conn->connect_error );
}

// Query staff data based on staff ID
$sql  = "SELECT * FROM staffs WHERE staff_id = ?";
$stmt = $conn->prepare( $sql );
$stmt->bind_param( "s", $staff_id );
$stmt->execute();
$result = $stmt->get_result();
if ( $result->num_rows > 0 ) {
	$staff = $result->fetch_assoc();
} else {
	die( "Staff not found." );
}
function getSiteUrl() {
	// Check if the site uses HTTPS or HTTP
	$protocol = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) ? "https://" : "http://";

	// Get the server name (e.g., localhost or example.com)
	$host = $_SERVER['HTTP_HOST'];

	// Get the path to the current script (this will include the script file)
	$script = $_SERVER['SCRIPT_NAME'];

	// Remove the script filename from the path to get the base URL
	$path = dirname( $script );

	// Combine the components to form the full site URL
	$siteUrl = $protocol . $host;

	// Return the URL (you can also trim trailing slashes if needed)
	return rtrim( $siteUrl, '/' );
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="assets/js/script.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Staff</title>
</head>
<body>
<div class="staff-container">
    <!-- Custom Confirmation Popup -->
    <div id="deleteConfirmationModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3>Are you sure you want to delete this assignment?</h3>
            <div class="modal-buttons">
                <button id="confirmDeleteBtn">Yes, Delete</button>
                <button id="cancelDeleteBtn">Cancel</button>
            </div>
        </div>
    </div>

    <div class="staff-box">
        <div class="staff-top-bar">
            <div class="staff-img">
				<?php if ( ! empty( $staff['image_url'] ) ): ?>
                    <img src="<?php echo getSiteUrl() . '/Student-Staff-Integration/' . $staff['image_url']; ?>"
                         alt="Staff Image"/>
				<?php else: ?>
                    <img src="assets/staff-images/staff1.jpg"
                         alt="Default Staff Image"/> <!-- Default image if no staff image -->
				<?php endif; ?>

            </div>
            <div class="staff-content">
                <h1 class="staff-name"><?php echo $staff['first_name'] . ' ' . $staff['last_name']; ?></h1>
                <p class="staff-position"><?php echo $staff['position'] ?></p>
            </div>
            <a href="actions/logout/logout.php" class="logout"><i class='bx bx-power-off'></i></a>
        </div>
        <div class="body-content">
            <div class="side-bar">
                <ul class="nav-links">
                    <li class="nav-link" onclick="showContent('basic-staff-info')">Basic Info</li>
                    <li class="nav-link" onclick="showContent('assignment')">Assignment</li>
                    <li class="nav-link" onclick="showContent('notice')">Notice</li>
                    <li class="nav-link" onclick="showContent('attendance')">Attendance</li>
                    <li class="nav-link" onclick="showContent('leave-approve')">
                        Leave Approve
                        <span id="leave-notification" class="notification-badge">0</span>
                    </li>

                </ul>
            </div>
            <div class="displayed-content">
                <!-- Basic Info Section -->
                <div class="display-content-box" id="basic-staff-info" style="display: block">
                    <h2>Basic Information</h2>
                    <div class="info-grid">
                        <div class="info-row">
                            <strong>Name:</strong>
                            <span><?php echo $staff['first_name'] . ' ' . $staff['last_name']; ?></span>
                        </div>
                        <div class="info-row">
                            <strong>Email:</strong> <span><?php echo $staff['email'] ?></span>
                        </div>
                        <div class="info-row">
                            <strong>Address:</strong> <span><?php echo $staff['address'] ?></span>
                        </div>
                        <div class="info-row">
                            <strong>Phone:</strong> <span><?php echo $staff['phone'] ?></span>
                        </div>
                        <div class="info-row">
                            <strong>Gender:</strong> <span><?php echo $staff['gender'] ?></span>
                        </div>
                        <div class="info-row">
                            <strong>Blood Group:</strong> <span><?php echo $staff['blood_group'] ?></span>
                        </div>
                        <div class="info-row">
                            <strong>Aadhaar:</strong> <span><?php echo $staff['adhaar'] ?></span>
                        </div>
                        <div class="info-row">
                            <strong>Position:</strong> <span><?php echo $staff['position'] ?></span>
                        </div>
                        <div class="info-row">
                            <strong>Staff ID:</strong> <span><?php echo $staff['staff_id'] ?></span>
                        </div>
                    </div>
                </div>

                <!-- Assignment Section -->
                <div class="display-content-box" id="assignment" style="display: none;">
                    <h2>Assignments</h2>
                    <button class="create-btn" onclick="showAssignmentForm('create')">+ Create Assignment</button>

					<?php
                    $table = "assignments";
                    $sql_check_table = "SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ? AND table_name = ?";
                    $stmt_check = $conn->prepare($sql_check_table);
                    $database_name = 'student_staff_integration';
                    $stmt_check->bind_param("ss", $database_name, $table); // $database_name should be your database name
                    $stmt_check->execute();
                    $result_check = $stmt_check->get_result();
                    $row_check = $result_check->fetch_assoc();

                    if ($row_check['count'] > 0) {
					$sql  = "SELECT * FROM assignments WHERE staff_id = ?";
					$stmt = $conn->prepare( $sql );
					$stmt->bind_param( "s", $staff_id );
					$stmt->execute();
					$result = $stmt->get_result();

					if ( $result->num_rows > 0 ) :
						while ( $row = $result->fetch_assoc() ) :
							?>
                            <!-- Assignment List -->
                            <div class="assignment-list">
                                <div class="assignment-item">
                                    <p>Class: <?php echo $row['class']; ?></p>
                                    <p>Subject: <?php echo $row['subject']; ?></p>
                                    <p>Due Date: <?php echo $row['due_date']; ?></p>
                                    <p>Content: <?php echo $row['details']; ?></p>
                                    <div class="assignment-actions">

                                        <span class="edit-btn"
                                              onclick="showAssignmentForm('edit', <?php echo $row['id']; ?>)">
                            <i class='bx bx-edit'></i>
                        </span>
                                        <span class="delete-btn"
                                              onclick="deleteAssignment(
                                              <?php echo $row['id']; ?>,
                                                      '<?php echo $row['class']; ?>',
                                                      '<?php echo $row['subject']; ?>',
                                                      '<?php echo $row['due_date']; ?>',
                                                      )">
                <i class='bx bxs-trash'></i>
            </span>


                                        <!-- Button with eye icon -->
										<?php if ( isset( $row['submitted_students'] ) && ! empty( trim( $row['submitted_students'] ) ) ) : ?>
                                            <span class="display-submitted-student"
                                                  onclick="showSubmittedStudentList(<?php echo $row['id']; ?>)">
    <i class="fas fa-eye"></i>
</span>
										<?php
                                        endif; ?>

                                        <!-- Modal Popup for Submitted Students -->
                                        <!-- Modal to display submitted students -->
                                        <div id="submittedStudentModal" class="modal">
                                            <div class="modal-content">
                                                <span class="close"
                                                      onclick="document.getElementById('submittedStudentModal').style.display='none'">&times;</span>
                                                <h2>Submitted Students</h2>
                                                <table id="submittedStudentTable" class="table">
                                                    <!-- Table content will be dynamically generated here -->
                                                </table>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
						<?php endwhile; endif; } ?>

                    <!-- Assignment Form -->
                    <div class="assignment-form" style="display: none;">
                        <h3 id="form-title">Create/Edit Assignment</h3>


                        <form id="assignmentForm">
                            <!-- Hidden input for assignment ID -->
                            <input type="hidden" id="assignment-id" name="assignment_id">

                            <!-- Class Selection -->
                            <label for="assignment-class">Class:</label>
                            <select id="assignment-class" name="assignment_class" required>
                                <optgroup label="UG">
                                    <option value="">-- Select Class --</option>
                                    <option value="first-ug">UG First Year</option>
                                    <option value="second-ug">UG Second Year</option>
                                    <option value="third-ug">UG Third Year</option>
                                </optgroup>
                                <optgroup label="PG">
                                    <option value="first-pg">PG First Year</option>
                                    <option value="second-pg">PG Second Year</option>
                                </optgroup>
                            </select>
                            <span class="error-message" id="assignment-class-error"></span>
                            <br>

                            <!-- Subject Input -->
                            <label for="assignment-subject">Subject:</label>
                            <input type="text" id="assignment-subject" name="assignment_subject" placeholder="Enter Subject" required>
                            <span class="error-message" id="assignment-subject-error"></span>
                            <br>

                            <!-- Due Date Input -->
                            <label for="assignment-due-date">Due Date:</label>
                            <input type="date" id="assignment-due-date" name="assignment_due_date" required>
                            <span class="error-message" id="assignment-due-date-error"></span>
                            <br>

                            <!-- Assignment Details -->
                            <label for="assignment-details">Assignment:</label>
                            <textarea id="assignment-details" name="assignment_details" rows="5" placeholder="Enter assignment details" required></textarea>
                            <span class="error-message" id="assignment-details-error"></span>
                            <br>

                            <!-- Buttons -->
                            <button type="submit">Save</button>
                            <button type="button" onclick="hideAssignmentForm()">Cancel</button>
                        </form>

                        <div id="response"></div>
                    </div>
                </div>

                <div id="confirmationModal" class="modal">
                    <div class="modal-content">
                        <h2>Are you sure you want to delete this notice?</h2>
                        <button id="confirmNoticeDeleteBtn" class="modal-btn confirm-btn">Yes, Delete</button>
                        <button id="cancelNoticeDeleteBtn" class="modal-btn cancel-btn">Cancel</button>
                    </div>
                </div>
                <!-- Notice Section -->
                <div class="display-content-box" id="notice" style="display: none;">
                    <h2>Notices</h2>
                    <button class="create-btn" onclick="showNoticeForm('create')">+ Create Notice</button>

                    <!-- Notice List -->
                    <div class="notice-list" id="notice-list">
						<?php

						if ( isset( $_SESSION['staff_id'] ) ) {
							$staff_id = $_SESSION['staff_id'];
                        $table = "notices";
                        $sql_check_table = "SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ? AND table_name = ?";
                        $stmt_check = $conn->prepare($sql_check_table);
                        $database_name = 'student_staff_integration';// Replace with your database name
                        $stmt_check->bind_param("ss", $database_name, $table);
                        $stmt_check->execute();
                        $result_check = $stmt_check->get_result();
                        $row_check = $result_check->fetch_assoc();

                        if ($row_check['count'] > 0) {
							// Retrieve notices for the current staff
							$sql  = "SELECT * FROM notices WHERE staff_id = ?";
							$stmt = $conn->prepare( $sql );
							$stmt->bind_param( "i", $staff_id );
							$stmt->execute();
							$result = $stmt->get_result();

							if ( $result->num_rows > 0 ):
								while ( $row = $result->fetch_assoc() ):
									?>
                                    <div class="notice-item" data-id="<?php echo $row['id']; ?>">
                                        <p><strong>Title:</strong> <span
                                                    class="notice-title"><?php echo htmlspecialchars( $row['title'] ); ?></span>
                                        </p>
                                        <p><strong>Details:</strong> <span
                                                    class="notice-details"><?php echo htmlspecialchars( $row['details'] ); ?></span>
                                        </p>
                                        <p><strong>Date:</strong> <span
                                                    class="notice-date"><?php echo htmlspecialchars( $row['date'] ); ?></span>
                                        </p>
                                        <p><strong>Class:</strong> <span
                                                    class="notice-class"><?php echo htmlspecialchars( $row['class'] ); ?></span>
                                        </p>

                                        <!-- Actions for Edit and Delete -->
                                        <div class="notice-actions">
                    <span class="edit-btn" onclick="showNoticeForm('edit', <?php echo $row['id']; ?>)">
                        <i class='bx bx-edit'></i>
                    </span>
                                            <span class="delete-btn" onclick="deleteNotice(<?php echo $row['id']; ?>)">
                        <i class='bx bxs-trash'></i>
                    </span>
                                        </div>
                                    </div>
								<?php endwhile; endif;
						} ?>
                    </div>
                    <div class="notice-form" style="display: none;">
                        <h3 id="edit-form-title">Create Notice</h3>

                        <input type="hidden" id="notice-id" name="notice-id">

                        <label for="notice-class">Class:</label>
                        <select id="notice-class" name="notice_class">
                            <option value="first-ug">UG First Year</option>
                            <option value="second-ug">UG Second Year</option>
                            <option value="third-ug">UG Third Year</option>
                            <option value="first-pg">PG First Year</option>
                            <option value="second-pg">PG Second Year</option>
                        </select>
                        <span class="error-message" id="notice-class-error"></span><br>

                        <label for="notice-title">Title:</label>
                        <input type="text" id="notice-title" placeholder="Notice Title">
                        <span class="error-message" id="notice-title-error"></span><br>

                        <label for="notice-details">Details:</label>
                        <textarea id="notice-details" rows="5" placeholder="Enter notice details here"></textarea>
                        <span class="error-message" id="notice-details-error"></span><br>

                        <label for="notice-date">Date:</label>
                        <input type="date" id="notice-date">
                        <span class="error-message" id="notice-date-error"></span><br>

                        <button onclick="saveNotice()">Save</button>
                        <button onclick="cancelNoticeForm()">Cancel</button>
                    </div>
                </div>
<?php } ?>

                <div class="display-content-box" id="attendance">
                    <h2>Maintain Attendance</h2>
                    <ul class="class-links">
                        <li class="class-link" onclick="openClass('ug-first-year')">UG First Year</li>
                        <li class="class-link" onclick="openClass('ug-second-year')">UG Second Year</li>
                        <li class="class-link" onclick="openClass('ug-third-year')">UG Third Year</li>
                        <li class="class-link" onclick="openClass('pg-first-year')">PG First Year</li>
                        <li class="class-link" onclick="openClass('pg-second-year')">PG Second Year</li>
                    </ul>

                    <div class="classes" id="ug-first-year" style="display: block">UG First Year</div>
                    <div class="classes" id="ug-second-year" style="display: none">UG Second Year</div>
                    <div class="classes" id="ug-third-year" style="display: none">UG Third Year</div>
                    <div class="classes" id="pg-first-year" style="display: none">PG First Year</div>
                    <div class="classes" id="pg-second-year" style="display: none">PG Second Year</div>
                </div>

                <div class="display-content-box" id="leave-approve">
                    <h2>Approve Leave Request</h2>
                    <ul class="class-links">
                        <li class="class-link active" onclick="openLeaveStatus('all')"><i class='bx bx-infinite'></i>All
                        </li>
                        <li class="class-link pending" onclick="openLeaveStatus('pending')">
                            <i class='bx bxs-circle-three-quarter'></i> Pending Approvals
                        </li>
                        <li class="class-link approved" onclick="openLeaveStatus('approved')">
                            <i class='bx bxs-check-circle'></i> Approved
                        </li>
                        <li class="class-link declined" onclick="openLeaveStatus('declined')">
                            <i class='bx bxs-x-circle'></i> Declined
                        </li>
                        <li class="class-link trash" onclick="openLeaveStatus('trash')">
                            <i class='bx bxs-trash'></i> Trash
                        </li>
                    </ul>

                    <!-- Request boxes for each status -->
                    <div class="request-box" id="all" style="display: block;">
                        <div class="approve-leave-box" data-status="trash">
                            <p>Student Name: Anna Brown</p>
                            <p>Class: UG Third Year</p>
                            <p>Date: 24/01/2025</p>
                            <p>Status: Trash <i class="status-icon bx bxs-trash"></i></p>
                            <p>Reason: Not required anymore</p>
                            <div class="approve-leave-actions">
                                <div class="custom-select">
                                    <div class="select-box" onclick="toggleDropdown(this)">
        <span class="selected-option">
            <i class="bx bxs-trash"></i> Trash
        </span>
                                        <i class="bx bx-chevron-down arrow"></i>
                                    </div>
                                    <div class="options-container">
                                        <div class="option" data-value="approved" onclick="selectOption(this)">
                                            <i class="bx bxs-check-circle"></i> Approved
                                        </div>
                                        <div class="option" data-value="declined" onclick="selectOption(this)">
                                            <i class="bx bxs-x-circle"></i> Declined
                                        </div>
                                        <div class="option" data-value="trash" onclick="selectOption(this)">
                                            <i class="bx bxs-trash"></i> Trash
                                        </div>
                                    </div>
                                </div>

                                <button class="save-status-btn" onclick="saveLeaveStatus(this)">
                                    <i class="bx bxs-save"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="request-box" id="pending" style="display: none;">Display if pending</div>
                    <div class="request-box" id="approved" style="display: none;">Display if approved</div>
                    <div class="request-box" id="declined" style="display: none;">Display if declined</div>
                    <div class="request-box" id="trash" style="display: none;">Display Trash</div>
                </div>


            </div>
        </div>
    </div>
    <div id="deleteFileModal" class="modal">
        <div class="modal-content">
            <h3>Are you sure you want to delete this file?</h3>
            <div class="modal-actions">
                <button id="confirmDelete" class="btn btn-danger">Yes, Delete</button>
                <button id="cancelDelete" class="btn btn-secondary">Cancel</button>
            </div>
        </div>
    </div>

</body>
</html>
