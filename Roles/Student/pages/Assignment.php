<h2>Assignments</h2>
<div class="assignment-list">
	<?php
	// Check if the 'assignments' table exists
	$checkTableSql = "SHOW TABLES LIKE 'assignments'";
	$checkTableResult = $conn->query($checkTableSql);

	// If the 'assignments' table exists
	if ($checkTableResult && $checkTableResult->num_rows > 0) :
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
	else:
		// Display message if the assignments table doesn't exist
		echo "<p>No assignments  found.</p>";
	endif;
	?>
</div>
	<!-- Show the unsubmitted count in the menu -->
	<script>
        document.addEventListener('DOMContentLoaded', function() {
            const unsubmittedCount = <?php echo $unsubmittedCount ?? null ?>

            const notificationBadge = document.getElementById('assignment-notification');

            if (notificationBadge) { // Check if the element exists
                if (unsubmittedCount > 0) {
                    notificationBadge.textContent = unsubmittedCount; // Show count in badge
                    notificationBadge.style.display = 'inline-block'; // Make sure it's visible
                } else {
                    notificationBadge.style.display = 'none'; // Hide the badge if no unsubmitted assignments
                }
            } else {
                console.error("Element with ID 'assignment-notification' not found.");
            }
        });


	</script>