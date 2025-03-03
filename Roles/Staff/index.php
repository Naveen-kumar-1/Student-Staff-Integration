<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header('Location: /Student-Staff-Integration/');
    exit;
}
$staff_id = $_SESSION['staff_id'];

// Database connection
if(file_exists('../../config.php')) {
	include('../../config.php');
}
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query staff data based on staff ID
$sql = "SELECT * FROM staffs WHERE staff_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $staff = $result->fetch_assoc();
} else {
    die("Staff not found.");
}
function getSiteUrl()
{
    // Check if the site uses HTTPS or HTTP
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

    // Get the server name (e.g., localhost or example.com)
    $host = $_SERVER['HTTP_HOST'];

    // Get the path to the current script (this will include the script file)
    $script = $_SERVER['SCRIPT_NAME'];

    // Remove the script filename from the path to get the base URL
    $path = dirname($script);

    // Combine the components to form the full site URL
    $siteUrl = $protocol . $host;

    // Return the URL (you can also trim trailing slashes if needed)
    return rtrim($siteUrl, '/');
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
                <?php if (!empty($staff['image_url'])): ?>
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
                        <span id="leave-notification" class="notification-badge"></span>
                    </li>

                </ul>
            </div>
            <div class="displayed-content">
                <!-- Basic Info Section -->
                <div class="display-content-box" id="basic-staff-info" style="display: block">
                    <?php if(file_exists('pages/basicStaffInfo.php')){
                        include 'pages/basicStaffInfo.php';
                    } ?>
                 </div>

                <!-- Assignment Section -->
                <div class="display-content-box" id="assignment" style="display: none;">
	                <?php if(file_exists('pages/assignment.php')){
		                include 'pages/assignment.php';
	                } ?>
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
	                <?php if(file_exists('pages/notice.php')){
		                include 'pages/notice.php';
	                } ?>
                </div>


                <div class="display-content-box" id="attendance">
	                <?php if(file_exists('pages/attendance.php')){
		                include 'pages/attendance.php';
	                } ?>
                </div>
                <!--Leave req-->

                <div class="display-content-box" id="leave-approve">

	                <?php if(file_exists('pages/leaveApprove.php')){
		                include 'pages/leaveApprove.php';
	                } ?>
                    </div>
                </div>

                <!-- JavaScript for the custom popup functionality -->
                <script>

                </script>


                <div id="deleteFileModal" class="modal">
                    <div class="modal-content">
                        <h3>Are you sure you want to delete this file?</h3>
                        <div class="modal-actions">
                            <button id="confirmSinglePDFDelete" onclick="deleteSingfileFunction()" class="btn btn-danger">Yes, Delete this</button>
                            <button id="cancelSinglePDFDelete" class="btn btn-secondary">Cancel</button>
                        </div>
                    </div>

                </div>

</body>
</html>
