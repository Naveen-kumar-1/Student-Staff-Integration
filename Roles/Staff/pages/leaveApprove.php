<h2>Approve Leave Request</h2>


<!-- Custom Popup Modal -->
<div id="customDeletePopup" class="popup-overlay" style="display: none;">
	<div class="popup-box">
		<h3>Delete All Leave Requests</h3>
		<p>Are you sure you want to delete all leave requests?</p>
		<div class="popup-actions">
			<button id="confirmDelete" class="confirm-btn">Yes, Delete</button>
			<button id="cancelDelete" class="cancel-btn">Cancel</button>
		</div>
	</div>
</div>

<!-- Popup CSS -->


<!-- Request boxes for each status -->
<div class="request-box" id="all" style="display: block;">
	<?php
	// Database connection (Make sure $conn is already declared before this block)
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "student_staff_integration";

	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
		die("<p style='color: red;'>Database connection failed</p>");
	}

	// Check if table exists
	$tableCheck = $conn->query("SHOW TABLES LIKE 'leave_requests'");
	if ($tableCheck->num_rows == 0) {
		echo "<p style='color: red;'>Table does not exist</p>";
		exit;
	}

	// Fetch staff ID from session or wherever it's stored
	$staff_id = $_SESSION['staff_id'] ?? null; // Assuming staff ID is stored in session

	if (!$staff_id) {
		echo "<p style='color: red;'>Staff ID is missing</p>";
		exit;
	}

	// Corrected SQL Query (Prepared Statement)
	$sql = "SELECT * FROM leave_requests WHERE staff_id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $staff_id);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows == 0) {
		echo "<p>No leave requests found</p>";
	} else {
	echo ' <span class="delete-all-leave-req" id="deleteAllLeaveReq">Delete All</span>';
	while ($row = $result->fetch_assoc()) {
	$statusClass = strtolower($row['status']); // Convert status to lowercase
		$statusClass = strtolower($row['status']); // Convert status to lowercase
		if ($statusClass === 'pending') {
			$pendingCount++; // Increment the pending count
		}
	?>
	<div class="approve-leave-box" data-id="<?php echo $row['id']; ?>"
	     data-status="<?php echo $statusClass; ?>">
		<p><strong>Student
				Name:</strong> <?php echo htmlspecialchars($row['student_name']); ?></p>
		<p><strong>Class:</strong> <?php echo htmlspecialchars($row['student_class']); ?>
		</p>
		<p><strong>Date:</strong> <?php echo htmlspecialchars($row['leave_date']); ?></p>
		<p><strong>Status:</strong> <?php echo ucfirst($statusClass); ?>
			<i class="status-icon bx
                            <?php echo ($statusClass == 'approved') ? 'bxs-check-circle' : (($statusClass == 'declined') ? 'bxs-x-circle' : 'bxs-trash'); ?>">
			</i>
		</p>
		<p><strong>Reason:</strong> <?php echo htmlspecialchars($row['leave_reason']); ?>
		</p>

		<div class="approve-leave-actions">
			<div class="custom-select">
				<div class="select-box" onclick="toggleDropdown(this)">
                                <span class="selected-option" data-value="<?php echo $statusClass; ?>">
                                    <i class="bx
                                        <?php echo ($statusClass == 'approved') ? 'bxs-check-circle' : (($statusClass == 'declined') ? 'bxs-x-circle' : 'bxs-trash'); ?>">
                                    </i>
                                    <?php echo ucfirst($statusClass); ?>
                                </span>


					<i class="bx bx-chevron-down arrow"></i>
				</div>
				<div class="options-container">
					<div class="option" data-value="Pending" onclick="selectOption(this)">
						<i class='bx bx-time-five'></i> Pending
					</div>
					<div class="option" data-value="Approved" onclick="selectOption(this)">
						<i class="bx bxs-check-circle"></i> Approved
					</div>
					<div class="option" data-value="Rejected" onclick="selectOption(this)">
						<i class="bx bxs-x-circle"></i> Rejected
					</div>
					<div class="option" data-value="trash" onclick="selectOption(this)">
						<i class="bx bxs-trash"></i> Trash
					</div>
				</div>
			</div>
			<button id="saveLeaveStatus" class="save-status-btn" onclick="saveLeaveStatus(this)">
				<i class="bx bxs-save"></i>
			</button>
		</div>
	</div>
<?php
}
}

$stmt->close();
$conn->close();
?>
	<script>
        let leaveReqCount = <?php
			echo $pendingCount;
			?>;

        const notificationBadge = document.getElementById('leave-notification');

        if (leaveReqCount > 0) {
            notificationBadge.textContent = leaveReqCount;
        } else {
            notificationBadge.style.display = "none";
        }
        document.getElementById("deleteAllLeaveReq").addEventListener("click", function () {
            document.getElementById("customDeletePopup").style.display = "flex";
        });

        document.getElementById("cancelDelete").addEventListener("click", function () {
            document.getElementById("customDeletePopup").style.display = "none";
        });

        document.getElementById("confirmDelete").addEventListener("click", function () {
            document.getElementById("customDeletePopup").style.display = "none";

            const staffId = "<?php echo $staff_id ?? ''; ?>"; // Replace with dynamic staff_id if needed

            if (!staffId) {
                showToast('Staff ID is missing.','error');
                return;
            }

            fetch('actions/Leave/deleteAllLeave.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `staff_id=${staffId}`
            })
                .then(response => response.json())
                .then(data => {
                    showToast(data.message, 'success');
                    if (data.success) {
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while deleting leave requests.');
                });
        });
	</script>



