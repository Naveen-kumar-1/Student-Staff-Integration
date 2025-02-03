
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
