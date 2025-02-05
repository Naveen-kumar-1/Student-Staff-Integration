<h2>Notices</h2>
<button class="create-btn" onclick="showNoticeForm('create')">+ Create Notice</button>

<!-- Notice List -->
<div class="notice-list" id="notice-list">
	<?php

	if (isset($_SESSION['staff_id'])) {
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
		$sql = "SELECT * FROM notices WHERE staff_id = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $staff_id);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0):
			while ($row = $result->fetch_assoc()):
				?>
				<div class="notice-item" data-id="<?php echo $row['id']; ?>">
					<p><strong>Title:</strong> <span
							class="notice-title"><?php echo htmlspecialchars($row['title']); ?></span>
					</p>
					<p><strong>Details:</strong> <span
							class="notice-details"><?php echo htmlspecialchars($row['details']); ?></span>
					</p>
					<p><strong>Date:</strong> <span
							class="notice-date"><?php echo htmlspecialchars($row['date']); ?></span>
					</p>
					<p><strong>Class:</strong> <span
							class="notice-class"><?php echo htmlspecialchars($row['class']); ?></span>
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

    <button id="save-notice-button" onclick="saveNotice()">Save Notice</button>
	<button onclick="cancelNoticeForm()">Cancel</button>
</div>
<?php } ?>