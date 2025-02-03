<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: /Student-Staff-Integration/');
    exit;
}
$student_id = $_SESSION['student_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_staff_integration";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
                                id="assignment-notification" class="notification-badge">0</span></li>
                    <li class="nav-link" onclick="showContent('notice')">Notice<span id="notice-notification"
                                                                                     class="notification-badge">0</span>
                    </li>
                    <li class="nav-link" onclick="showContent('attendance')">Attendance</li>
                    <li class="nav-link" onclick="showContent('apply-leave')">Apply Leave
                    </li>
                </ul>
            </div>
        </div>

        <!-- Content for each section -->
        <div class="display-content-box" id="basic-student-info" style="display: block;">
            <h2>Basic Information</h2>
            <div class="info-grid">
                <div class="info-row"><strong>Name:</strong>
                    <span><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></span></div>
                <div class="info-row"><strong>Email:</strong> <span><?php echo $student['email']; ?></span></div>
                <div class="info-row"><strong>Address:</strong> <span><?php echo $student['address']; ?></span></div>
                <div class="info-row"><strong>Phone:</strong> <span><?php echo $student['phone']; ?></span></div>
                <div class="info-row"><strong>Gender:</strong> <span><?php echo $student['gender']; ?></span></div>
                <div class="info-row"><strong>Blood Group:</strong> <span><?php echo $student['blood_group']; ?></span>
                </div>
                <div class="info-row"><strong>Aadhaar:</strong> <span><?php echo $student['adhaar']; ?></span></div>
                <div class="info-row"><strong>Class:</strong> <span><?php echo $student['class']; ?></span></div>
                <div class="info-row"><strong>Student ID:</strong> <span><?php echo $student['student_id']; ?></span>
                </div>
            </div>
        </div>

        <div class="display-content-box" id="assignment" style="display: none;">
            <h2>Assignments</h2>
            <div class="assignment-list">
                <?php
                // Initialize a variable to store the count of unsubmitted assignments
                $unsubmittedCount = 0;

                $sql = "SELECT * FROM assignments WHERE class = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $student['class']);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) :
                    while ($row = $result->fetch_assoc()) :

                        // Check if 'submitted_students' has data
                        if (!empty($row['submitted_students'])) {
                            // Decode the JSON data from 'submitted_students' column
                            $submittedStudents = json_decode($row['submitted_students'], true);
                            // Check if the student's data exists and if the status is submitted
                            $submitted = false;
                            if (isset($submittedStudents[$student['student_id']]) && $submittedStudents[$student['student_id']]['status'] === 'submitted') {
                                $submitted = true;
                            }

                            // If the student has not submitted, display the assignment with the submit button
                            if (!$submitted) {
                                $unsubmittedCount++;  // Increment count for unsubmitted assignments
                                ?>
                                <div class="assignment-item">
                                    <p><b>Class:</b> <?php echo $row['class']; ?></p>
                                    <p><b>Subject:</b> <?php echo $row['subject']; ?></p>
                                    <p><b>Due Date:</b> <?php echo $row['due_date']; ?></p>
                                    <p><b>Content:</b> <?php echo $row['details']; ?></p>

                                    <div class="assignment-actions">
                                        <button class="submit-btn"
                                                data-id="<?php echo $row['id']; ?>"
                                                data-first-name="<?php echo $student['first_name']; ?>"
                                                data-last-name="<?php echo $student['last_name']; ?>"
                                                data-student-id="<?php echo $student['student_id']; ?>">Submit
                                        </button>
                                    </div>
                                </div>
                                <?php
                            } else {
                                // If already submitted, show a message instead of the submit button
                                ?>
                                <div class="assignment-item">
                                    <p><b>Class:</b> <?php echo $row['class']; ?></p>
                                    <p><b>Subject:</b> <?php echo $row['subject']; ?></p>
                                    <p><b>Due Date:</b> <?php echo $row['due_date']; ?></p>
                                    <p><b>Content:</b> <?php echo $row['details']; ?></p>
                                    <p style="color: crimson"><b>Status:</b> Assignment already submitted</p>
                                </div>
                                <?php
                            }
                        } else {
                            // If 'submitted_students' is empty, display the assignment with the submit button
                            $unsubmittedCount++;  // Increment count for unsubmitted assignments
                            ?>
                            <div class="assignment-item">
                                <p><b>Class:</b> <?php echo $row['class']; ?></p>
                                <p><b>Subject:</b> <?php echo $row['subject']; ?></p>
                                <p><b>Due Date:</b> <?php echo $row['due_date']; ?></p>
                                <p><b>Content:</b> <?php echo $row['details']; ?></p>

                                <div class="assignment-actions">
                                    <button class="submit-btn"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-first-name="<?php echo $student['first_name']; ?>"
                                            data-last-name="<?php echo $student['last_name']; ?>"
                                            data-student-id="<?php echo $student['student_id']; ?>">Submit
                                    </button>
                                </div>
                            </div>
                            <?php
                        }
                    endwhile;
                else:
                    echo "No assignments found.";
                endif;
                ?>

                <!-- Show the unsubmitted count in the menu -->
                <script>
                    const unsubmittedCount = <?php echo $unsubmittedCount; ?>;
                    const notificationBadge = document.getElementById('assignment-notification');

                    if (unsubmittedCount > 0) {
                        notificationBadge.textContent = unsubmittedCount; // Show count in badge
                        notificationBadge.style.display = 'inline-block'; // Make sure it's visible
                    } else {
                        notificationBadge.style.display = 'none'; // Hide the badge if no unsubmitted assignments
                    }
                </script>
            </div>


        </div>


        <div class="display-content-box" id="notice" style="display: none;"><?php
            // Check if the 'notices' table exists
            $tableExists = $conn->query("SHOW TABLES LIKE 'notices'");

            if ($tableExists && $tableExists->num_rows > 0) {
                // Fetch notices for the student's class
                $sql = "SELECT * FROM notices WHERE class = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $student['class']);
                $stmt->execute();
                $result = $stmt->get_result();
                $noticeCount = $result->num_rows;
                if ($result->num_rows > 0) {
                    ?>
                    <div class="notice-container">
                        <h2>Notices</h2>
                        <table class="notice-table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Details</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $count = 1;
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['details']); ?></td>
                                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                                    <td>
                                        <!-- Add custom actions here if needed -->
                                        <button
                                                class="view-btn"
                                                data-id="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                data-title="<?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>"
                                                data-date="<?php echo htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8'); ?>"
                                                data-details="<?php echo htmlspecialchars($row['details'], ENT_QUOTES, 'UTF-8'); ?>"
                                        >
                                            View
                                        </button>

                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="notice-modal" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h3 id="notice-title"></h3>
                            <p><strong>Date:</strong> <span id="notice-date"></span></p>
                            <p id="notice-details"></p>
                        </div>
                    </div>
                    <?php
                } else {
                    echo "<p>No notices found for your class.</p>";
                }
            } else {
                echo "<p>'notices' table does not exist in the database.</p>";
            }
            ?>
        </div>
        <div class="display-content-box" id="attendance" style="display: none;">
            <h2>Monthly Attendance Rate</h2>

            <?php


            // Get the current month (YYYY-MM)
            $current_month = date('Y-m');

            // Fetch attendance data for the specific student for the entire month
            $sql = "SELECT * FROM student_attendance WHERE attendance_date LIKE ?";

            // Prepare the query
            $stmt = $conn->prepare($sql);

            // Check if the statement was prepared successfully
            if (!$stmt) {
                echo "Error preparing query: " . $conn->error;
                exit;
            }

            // Bind the parameter with '%' to match the current month (e.g., "2025-02%")
            $month_with_wildcards = $current_month . '%';
            $stmt->bind_param("s", $month_with_wildcards);

            // Execute the query
            $stmt->execute();
            $result = $stmt->get_result();

            // Process the attendance data for the target student
            $attendance_data = [];
            while ($row = $result->fetch_assoc()) {
                // Decode the attendance_data JSON for this row
                $attendance_data_decoded = json_decode($row['attendance_data'], true);

                // If the target student exists in the data, store their attendance
                if (isset($attendance_data_decoded[$student_id])) {
                    $attendance_data[] = [
                        'date' => $row['attendance_date'],
                        'attendance' => $attendance_data_decoded[$student_id]['attendance']
                    ];
                }
            }

            // Get student details
            $stmt_students = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
            $stmt_students->bind_param("s", $student_id);
            $stmt_students->execute();
            $student_result = $stmt_students->get_result();
            $student = $student_result->fetch_assoc();

            // Calculate attendance percentage
            $days_in_month = date('t');
            $total_present = 0;

            if (!empty($attendance_data)) {
                foreach ($attendance_data as $attendance_entry) {
                    if (isset($attendance_entry['attendance']['period_1']) && $attendance_entry['attendance']['period_1'] == 'Present') {
                        $total_present++;
                    }
                }
            }

            $attendance_percentage = ($days_in_month > 0) ? ($total_present / $days_in_month) * 100 : 0;
            ?>

            <!-- Display Attendance Percentage -->
            <div class="attendance-percentages">
                <div class="attendance-item">
                    <p><?php echo htmlspecialchars($student['first_name'] . " " . $student['last_name']); ?></p>
                    <p>Attendance: <?php echo round($attendance_percentage, 2); ?>%</p>
                </div>
            </div>

            <!-- Canvas for the Graph -->
            <canvas id="attendanceGraph" width="400" height="200"></canvas>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                // Prepare data for the graph
                const labels = ["<?php echo htmlspecialchars($student['first_name'] . " " . $student['last_name']); ?>"];
                const presentDaysData = [<?php echo $total_present; ?>];

                const ctx = document.getElementById('attendanceGraph').getContext('2d');
                const attendanceGraph = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Present Days',
                            data: presentDaysData,
                            backgroundColor: '#4caf50',
                            borderColor: '#388e3c',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: <?php echo $days_in_month; ?>
                            }
                        }
                    }
                });
            </script>
        </div>


        <div class="display-content-box" id="apply-leave" style="display: none;">
            <h2>Apply for a Leave</h2>
            <div class="apply-leave-student-details">
                <p><b>Student Name : </b>
                    <span><?php echo $student['first_name'] . ' ' . $student['last_name'] ?></span></p>
                <p><b>Student ID : </b><span><?php echo $student_id; ?></span></p>
                <button id="applyLeave">Apply Leave</button>
                <!-- Leave Application Popup -->
                <div id="leavePopup" class="popup-container">
                    <div class="popup-content">
                        <span class="close-btn">&times;</span>
                        <form id="applyLeaveForm">
                            <h2>Apply for Leave</h2>

                            <div class="input-fields">
                                <label>Name:</label>
                                <input type="text" id="student-name" readonly
                                       value="<?php echo $student['first_name'] . ' ' . $student['last_name'] ?>">
                            </div>

                            <div class="input-fields">
                                <label>ID:</label>
                                <input type="text" id="student-id" readonly value="<?php echo $student_id; ?>">
                            </div>

                            <div class="input-fields">
                                <label>Class:</label>
                                <input type="text" id="student-class" readonly value="<?php echo $student['class']; ?>">
                            </div>

                            <div class="input-fields">
                                <label>Date:</label>
                                <input type="date" id="leave-date" required>
                            </div>

                            <div class="input-fields">
                                <label>Reason:</label>
                                <textarea id="leave-reason" rows="4" required></textarea>
                            </div>

                            <button type="submit" id="leaveFormSubmit">Apply Leave</button>
                        </form>
                    </div>
                </div>


            </div>
            <?php
            // Database connection
            $host = "localhost";
            $user = "root";  // Change if needed
            $pass = "";      // Change if needed
            $dbname = "student_staff_integration";

            $conn = new mysqli($host, $user, $pass, $dbname);

            // Check if the table exists
            $tableExistsQuery = "SHOW TABLES LIKE 'leave_requests'";
            $tableResult = $conn->query($tableExistsQuery);

            if ($tableResult->num_rows > 0) {
            // Table exists, fetch data
            $sql = "SELECT * FROM leave_requests ORDER BY applied_at DESC";
            $result = $conn->query($sql);


            ?>


            <div class="container">
                <h2>Applied Leave Requests</h2>

                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Student ID</th>
                        <th>Class</th>
                        <th>Leave Date</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Applied At</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['student_name']}</td>
                    <td>{$row['student_id']}</td>
                    <td>{$row['student_class']}</td>
                    <td>{$row['leave_date']}</td>
                    <td>{$row['leave_reason']}</td>
                    <td><span class='status {$row['status']}'>{$row['status']}</span></td>
                    <td>{$row['applied_at']}</td>
                    <td>";

                            if ($row['status'] === 'Pending') {
                                echo "<div class='edit-actions'>";
                                echo "<button class='edit-btn' onclick='editLeaveRequest({$row['id']})'><i class='bx bx-edit-alt' ></i></button>";
                                echo "<button class='delete-btn' onclick='confirmDelete({$row['id']})'><i class='bx bxs-trash'></i></button>";
                                echo "</div>";
                            } else {
                                echo "<span class='no-actions'>🔒 Locked</span>";
                            }

                            echo "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='no-data'>No leave requests found</td></tr>";
                    }
                    }
                    ?>
                    </tbody>


                    <!-- Edit Leave Popup -->
                    <div id="editPopup" class="popup-overlay">
                        <div class="popup-box">
                            <h3>Edit Leave Request</h3>
                            <form id="editLeaveForm">
                                <input type="hidden" id="edit-id">

                                <label>Name:</label>
                                <input type="text" id="edit-student-name" readonly>

                                <label>Student ID:</label>
                                <input type="text" id="edit-student-id" readonly>

                                <label>Class:</label>
                                <input type="text" id="edit-student-class" readonly>

                                <label>Date:</label>
                                <input type="date" id="edit-leave-date" required>

                                <label>Reason:</label>
                                <textarea id="edit-leave-reason" rows="4" required></textarea>

                                <button type="submit">Update</button>
                                <button type="button" id="closeEditPopup">Cancel</button>
                            </form>
                        </div>
                    </div>

                    <!-- Custom Confirmation Popup -->
                    <div id="confirmPopup" class="popup-overlay">
                        <div class="popup-box">
                            <h3>Confirm Delete</h3>
                            <p>Are you sure you want to delete this leave request?</p>
                            <input type="hidden" id="delete-id">
                            <button id="confirmYes">Yes</button>
                            <button id="confirmNo">No</button>
                        </div>
                    </div>

            </div>

</body>
</html>

<?php $conn->close(); ?>

</div>
</div>
</div>
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
