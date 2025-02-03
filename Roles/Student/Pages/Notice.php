 <h2>Notice</h2>
            <?php
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
                echo "<p>Notices  does not exist.</p>";
            }
            ?>
        </div>
        <div class="display-content-box" id="attendance" style="display: none;">
            <h2>Monthly Attendance Rate</h2>

		    <?php
		    // Ensure $student_id is defined (replace this with the correct method to get the student_id, e.g., session or query string)
		    if (!isset($student_id)) {
			    echo "Error: Student ID is not set.";
		    } else {
			    // Check if the 'student_attendance' table exists
			    $table_check_sql = "SHOW TABLES LIKE 'student_attendance'";
			    $table_check_result = $conn->query($table_check_sql);

			    if ($table_check_result->num_rows == 0) {
				    echo "No attendance data available.";
			    } else {
				    // Get the current month (YYYY-MM)
				    $current_month = date('Y-m');

				    // Fetch attendance data for the specific student for the entire month
				    $sql = "SELECT * FROM student_attendance WHERE attendance_date LIKE ?";

				    // Prepare the query
				    $stmt = $conn->prepare($sql);

				    // Check if the statement was prepared successfully
				    if (!$stmt) {
					    echo "Error preparing query: " . $conn->error;
				    } else {
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

					    if ($student_result->num_rows == 0) {
						    echo "Student not found.";
					    } else {
						    $student = $student_result->fetch_assoc();

						    // Calculate attendance percentage
						    $days_in_month = date('t');
						    $total_present = 0;

						    if (!empty($attendance_data)) {
							    foreach ($attendance_data as $attendance_entry) {
								    // Check if student was present for period_1 (you may want to check other periods as well)
								    if (isset($attendance_entry['attendance']['period_1']) && $attendance_entry['attendance']['period_1'] == 'Present') {
									    $total_present++;
								    }
							    }
						    }

						    // Calculate the attendance percentage
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
						    <?php
					    }
				    }
			    }
		    }
		    ?>