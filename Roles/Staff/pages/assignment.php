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
	$sql = "SELECT * FROM assignments WHERE staff_id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $staff_id);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) :
		while ($row = $result->fetch_assoc()) :
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
						<?php if (isset($row['submitted_students']) && !empty(trim($row['submitted_students']))) : ?>
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
		<?php endwhile; endif;
} ?>

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
		<input type="text" id="assignment-subject" name="assignment_subject"
		       placeholder="Enter Subject" required>
		<span class="error-message" id="assignment-subject-error"></span>
		<br>

		<!-- Due Date Input -->
		<label for="assignment-due-date">Due Date:</label>
		<input type="date" id="assignment-due-date" name="assignment_due_date" required>
		<span class="error-message" id="assignment-due-date-error"></span>
		<br>

		<!-- Assignment Details -->
		<label for="assignment-details">Assignment:</label>
		<textarea id="assignment-details" name="assignment_details" rows="5"
		          placeholder="Enter assignment details" required></textarea>
		<span class="error-message" id="assignment-details-error"></span>
		<br>

		<!-- Buttons -->
		<button type="submit">Save</button>
		<button type="button" onclick="hideAssignmentForm()">Cancel</button>
	</form>

	<div id="response"></div>
</div>