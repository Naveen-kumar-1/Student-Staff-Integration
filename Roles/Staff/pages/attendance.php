<?php
// Get the current date
$current_date = date('y-m-d');

// Fetch students for class-wise display
$stmt = $conn->prepare("SELECT * FROM students ORDER BY class, first_name");
$stmt->execute();
$result = $stmt->get_result();

// Create an array to hold students grouped by class
$students_by_class = [];
while ($student = $result->fetch_assoc()) {
	$students_by_class[$student['class']][] = $student;
}

// Check if the leave_requests table exists
$check_leave_table_query = "SHOW TABLES LIKE 'leave_requests'";
$check_leave_table_result = $conn->query($check_leave_table_query);

$approved_leaves = [];
if ($check_leave_table_result->num_rows > 0) {
	// The leave_requests table exists, fetch approved leaves for today
	$leave_query = "SELECT student_id FROM leave_requests WHERE leave_date = ? AND status = 'Approved'";
	$stmt_leave = $conn->prepare($leave_query);
	$stmt_leave->bind_param("s", $current_date);
	$stmt_leave->execute();
	$leave_result = $stmt_leave->get_result();

	// Store the approved leave student IDs in an array
	while ($leave = $leave_result->fetch_assoc()) {
		$approved_leaves[] = $leave['student_id'];
	}
}

// Display students for each class
foreach ($students_by_class as $class => $students) {
	// Fetch saved attendance data for today for this class
	// Check if the table exists before running the query
	$check_table_query = "SHOW TABLES LIKE 'student_attendance'";
	$check_table_result = $conn->query($check_table_query);

	if ($check_table_result->num_rows > 0) {
		// Table exists, fetch saved attendance data for today for this class
		$stmt_attendance = $conn->prepare("SELECT attendance_data FROM student_attendance WHERE class = ? AND attendance_date = ?");
		$stmt_attendance->bind_param("ss", $class, $current_date);
		$stmt_attendance->execute();
		$attendance_result = $stmt_attendance->get_result();
		$attendance_data = $attendance_result->num_rows > 0 ? json_decode($attendance_result->fetch_assoc()['attendance_data'], true) : [];
	} else {
		$attendance_data = []; // Table does not exist, so assume no attendance data
	}

	?>
    <div class="classes" id="<?php echo htmlspecialchars($class); ?>"
         style="<?php echo ($class == 'first-ug') ? 'display:block;' : 'display:none;'; ?>">

        <h3>Class: <span style="color: #007bff">
                <?php
                switch ($class):
	                case 'first-ug':
		                echo "UG First Year";
		                break;
	                case 'second-ug':
		                echo "UG Second Year";
		                break;
	                case 'third-ug':
		                echo "UG Third Year";
		                break;
	                case 'first-pg':
		                echo "PG First Year";
		                break;
	                case 'second-pg':
		                echo "PG Second Year";
		                break;
	                default:
		                echo "Unknown class";
		                break;
                endswitch;
                ?>
            </span> | <?php echo $current_date; ?></h3>
        <button class="save-button"
                onclick="saveAttendance('<?php echo htmlspecialchars($class); ?>')">Save
        </button>
        <table width="100%" cellpadding="5">
            <thead>
            <tr>
                <th>Student Name</th>
                <th>1st Period</th>
                <th>2nd Period</th>
                <th>Break</th>
                <th>3rd Period</th>
                <th>Lunch</th>
                <th>4th Period</th>
                <th>5th Period</th>
            </tr>
            </thead>
            <tbody>
			<?php

            foreach ($students as $student) {
				// If attendance data exists, use it; otherwise, set default "Not Set"
				$student_attendance = isset($attendance_data[$student['student_id']]) ? $attendance_data[$student['student_id']]['attendance'] : [
					'period_1' => 'Not Set',
					'period_2' => 'Not Set',
					'period_3' => 'Not Set',
					'period_4' => 'Not Set',
					'period_5' => 'Not Set'
				];

				// Check if the student has an approved leave for today
				$is_on_leave = in_array($student['student_id'], $approved_leaves);
				?>
                <tr>
                    <td><?php echo htmlspecialchars($student['first_name'] . " " . $student['last_name']); ?></td>

					<?php if ($is_on_leave) { ?>
                        <!-- Display leave message if the student is on leave -->
                        <td class="display-leave-message" colspan="7" style="color: green; font-weight: bold;">
                            <span class="leave-approved-text"
                                  data-student-id="<?php echo $student['student_id']; ?>"
                                  data-student-name="<?php echo htmlspecialchars($student['first_name'] . " " . $student['last_name']); ?>"
                                  onclick="showLeaveDetails(this)">
                                Leave Approved for Today
                            </span>
                        </td>
					<?php } else { ?>
                        <!-- Otherwise, display attendance options -->
                        <!-- 1st Period -->
                        <td>

                            <select class="attendance"
                                    data-student-id="<?php echo $student['student_id']; ?>"
                                    data-student-name="<?php echo htmlspecialchars($student['first_name'] . " " . $student['last_name']); ?>"
                                    data-period="1"
                                    style="background-color: <?php echo ($student_attendance['period_1'] === 'Present') ? 'green' : (($student_attendance['period_1'] === 'Absent') ? 'red' : 'gray'); ?>; color: white;">
                                <option value="Not Set" <?php echo ($student_attendance['period_1'] === 'Not Set') ? 'selected' : ''; ?>>
                                    Not Set
                                </option>
                                <option value="Present" <?php echo ($student_attendance['period_1'] === 'Present') ? 'selected' : ''; ?>>
                                    Present
                                </option>
                                <option value="Absent" <?php echo ($student_attendance['period_1'] === 'Absent') ? 'selected' : ''; ?>>
                                    Absent
                                </option>
                            </select>
                        </td>

                        <!-- 2nd Period -->
                        <td>
                            <select class="attendance"
                                    data-student-id="<?php echo $student['student_id']; ?>"
                                    data-period="2"
                                    data-student-name="<?php echo htmlspecialchars($student['first_name'] . " " . $student['last_name']); ?>"
                                    style="background-color: <?php echo ($student_attendance['period_2'] === 'Present') ? 'green' : (($student_attendance['period_2'] === 'Absent') ? 'red' : 'gray'); ?>; color: white;">
                                <option value="Not Set" <?php echo ($student_attendance['period_2'] === 'Not Set') ? 'selected' : ''; ?>>
                                    Not Set
                                </option>
                                <option value="Present" <?php echo ($student_attendance['period_2'] === 'Present') ? 'selected' : ''; ?>>
                                    Present
                                </option>
                                <option value="Absent" <?php echo ($student_attendance['period_2'] === 'Absent') ? 'selected' : ''; ?>>
                                    Absent
                                </option>
                            </select>
                        </td>

                        <!-- Break -->
                        <td>Break</td>

                        <!-- 3rd Period -->
                        <td>
                            <select class="attendance"
                                    data-student-id="<?php echo $student['student_id']; ?>"
                                    data-period="3"
                                    data-student-name="<?php echo htmlspecialchars($student['first_name'] . " " . $student['last_name']); ?>"
                                    style="background-color: <?php echo ($student_attendance['period_3'] === 'Present') ? 'green' : (($student_attendance['period_3'] === 'Absent') ? 'red' : 'gray'); ?>; color: white;">
                                <option value="Not Set" <?php echo ($student_attendance['period_3'] === 'Not Set') ? 'selected' : ''; ?>>
                                    Not Set
                                </option>
                                <option value="Present" <?php echo ($student_attendance['period_3'] === 'Present') ? 'selected' : ''; ?>>
                                    Present
                                </option>
                                <option value="Absent" <?php echo ($student_attendance['period_3'] === 'Absent') ? 'selected' : ''; ?>>
                                    Absent
                                </option>
                            </select>
                        </td>

                        <!-- Lunch -->
                        <td>Lunch</td>

                        <!-- 4th Period -->
                        <td>
                            <select class="attendance"
                                    data-student-id="<?php echo $student['student_id']; ?>"
                                    data-period="4"
                                    data-student-name="<?php echo htmlspecialchars($student['first_name'] . " " . $student['last_name']); ?>"
                                    style="background-color: <?php echo ($student_attendance['period_4'] === 'Present') ? 'green' : (($student_attendance['period_4'] === 'Absent') ? 'red' : 'gray'); ?>; color: white;">
                                <option value="Not Set" <?php echo ($student_attendance['period_4'] === 'Not Set') ? 'selected' : ''; ?>>
                                    Not Set
                                </option>
                                <option value="Present" <?php echo ($student_attendance['period_4'] === 'Present') ? 'selected' : ''; ?>>
                                    Present
                                </option>
                                <option value="Absent" <?php echo ($student_attendance['period_4'] === 'Absent') ? 'selected' : ''; ?>>
                                    Absent
                                </option>
                            </select>
                        </td>

                        <!-- 5th Period -->
                        <td>
                            <select class="attendance"
                                    data-student-id="<?php echo $student['student_id']; ?>"
                                    data-period="5"
                                    data-student-name="<?php echo htmlspecialchars($student['first_name'] . " " . $student['last_name']); ?>"
                                    style="background-color: <?php echo ($student_attendance['period_5'] === 'Present') ? 'green' : (($student_attendance['period_5'] === 'Absent') ? 'red' : 'gray'); ?>; color: white;">
                                <option value="Not Set" <?php echo ($student_attendance['period_5'] === 'Not Set') ? 'selected' : ''; ?>>
                                    Not Set
                                </option>
                                <option value="Present" <?php echo ($student_attendance['period_5'] === 'Present') ? 'selected' : ''; ?>>
                                    Present
                                </option>
                                <option value="Absent" <?php echo ($student_attendance['period_5'] === 'Absent') ? 'selected' : ''; ?>>
                                    Absent
                                </option>
                            </select>
                        </td>
					<?php } ?>
                </tr>
			<?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>
