<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: /Student-Staff-Integration/');
    exit;
}
$student_id = $_SESSION['student_id'];

// Database connection
if(file_exists('../../config.php')) {
    include('../../config.php');
}

// Query student data based on student ID
$sql = "SELECT * FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    die("Student not found.");
}

// Function to get site URL dynamically
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
    <link rel="stylesheet" href="assets/css/style.css"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Student</title>
</head>
<body>
<div class="student-container">
    <div class="student-box">
        <div class="student-top-bar">
            <div class="student-img">
                <?php if (!empty($student['image_url'])): ?>
                    <img src="<?php echo getSiteUrl() . '/Student-Staff-Integration/' . $student['image_url']; ?>"
                         alt="Student Image"/>
                <?php else: ?>
                    <img src="assets/student-images/student1.webp"
                         alt="Default Student Image"/> <!-- Default image if no student image -->
                <?php endif; ?>
            </div>
            <div class="student-content">
                <h1 class="student-name"><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></h1>
                <p class="student-class"><?php echo $student['class'] ?></p>
            </div>
            <a href="actions/logout/logout.php" class="logout"><i class='bx bx-power-off'></i></a>
        </div>

        <div class="body-content">
            <div class="side-bar">
                <ul class="nav-links">
                    <li class="nav-link" onclick="showContent('basic-student-info')">Basic Info</li>
                    <li class="nav-link" onclick="showContent('assignment')">Assignment<span
                                id="assignment-notification" class="notification-badge" style="display: none"></span>
                    </li>
                    <li class="nav-link" onclick="showContent('notice')">Notice<span id="notice-notification"
                                                                                     class="notification-badge"
                                                                                     style="display: none">0</span>
                    </li>
                    <li class="nav-link" onclick="showContent('attendance')">Attendance</li>
                    <li class="nav-link" onclick="showContent('apply-leave')">Apply Leave
                    </li>
                </ul>
            </div>
        </div>

        <!-- Content for each section -->
        <div class="display-content-box" id="basic-student-info" style="display: block;">
            <?php
            if (file_exists('pages/basicStudentInfo.php')) {
                include 'pages/basicStudentInfo.php';
            } else {
                echo "Basic Info not found.";
            }
            ?>
        </div>

        <div class="display-content-box" id="assignment" style="display: none;">
            <?php
            if (file_exists('pages/Assignment.php')) {
                include 'pages/Assignment.php';
            } else {
                echo "Assignment snot found.";
            }
            ?>
        </div>


        <div class="display-content-box" id="notice" style="display: none;">
            <?php
            if (file_exists('pages/Notice.php')) {
                include 'pages/Notice.php';
            } else {
                echo "Notice not found.";
            }
            ?>
        </div>


        <div class="display-content-box" id="apply-leave" style="display: none;">


            <?php if (file_exists('pages/LeaveApply.php')) {
                include 'pages/LeaveApply.php';
            } else {
                echo "Leave not found";
            } ?>

        </div>


    </div>
</div>
</div>
<?php $conn->close(); ?>

<div class="popup-form-container" id="popup-form">
</div>
<script>
    const noticeCount = <?php echo $noticeCount; ?>;
    const noticeNotificationBadge = document.getElementById('notice-notification');
    if (noticeCount > 0) {
        noticeNotificationBadge.textContent = noticeCount; // Show count in badge
        noticeNotificationBadge.style.display = 'inline-block'; // Make sure it's visible
    } else {
        noticeNotificationBadge.style.display = 'none'; // Hide the badge if no notices
    }

</script>
<script src="assets/js/script.js"></script>

</body>
</html>
