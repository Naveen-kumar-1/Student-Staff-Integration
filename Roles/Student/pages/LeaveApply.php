<h2>Apply for a Leave</h2>
<div class="apply-leave-student-details">
	<p><b>Student Name : </b><span><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></span></p>
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
					<input type="text" id="student-name" readonly value="<?php echo $student['first_name'] . ' ' . $student['last_name']; ?>">
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


// Check if the 'leave_requests' table exists
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

					// Actions for Pending Leave Requests
					if ($row['status'] === 'Pending') {
						echo "<div class='edit-actions'>
                                    <button class='edit-btn' onclick='editLeaveRequest({$row['id']})'><i class='bx bx-edit-alt'></i></button>
                                    <button class='delete-btn' onclick='confirmDelete({$row['id']})'><i class='bx bxs-trash'></i></button>
                                  </div>";
					} else {
						echo "<span class='no-actions'>🔒 Locked</span>";
					}

					echo "</td></tr>";
				}
			} else {
				echo "<tr><td colspan='9' class='no-data'>No leave requests found</td></tr>";
			}
			?>
			</tbody>
		</table>
	</div>

	<?php
} else {
	echo "<p>No leave requests data available. </p>";
}
?>

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