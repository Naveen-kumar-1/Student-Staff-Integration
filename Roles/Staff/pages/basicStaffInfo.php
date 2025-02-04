
<h2>Basic Information</h2>
                    <div class="info-grid">
                        <div class="info-row">
                            <strong>Name:</strong>
                            <span><?php echo $staff['first_name'] . ' ' . $staff['last_name']; ?></span>
</div>
<div class="info-row">
	<strong>Email:</strong> <span><?php echo $staff['email'] ?></span>
</div>
<div class="info-row">
	<strong>Address:</strong> <span><?php echo $staff['address'] ?></span>
</div>
<div class="info-row">
	<strong>Phone:</strong> <span><?php echo $staff['phone'] ?></span>
</div>
<div class="info-row">
	<strong>Gender:</strong> <span><?php echo $staff['gender'] ?></span>
</div>
<div class="info-row">
	<strong>Blood Group:</strong> <span><?php echo $staff['blood_group'] ?></span>
</div>
<div class="info-row">
	<strong>Aadhaar:</strong> <span><?php echo $staff['adhaar'] ?></span>
</div>
<div class="info-row">
	<strong>Position:</strong> <span><?php echo $staff['position'] ?></span>
</div>
<div class="info-row">
	<strong>Staff ID:</strong> <span><?php echo $staff['staff_id'] ?></span>
</div>
<?php
if ($staff['class_adviser'] !== 'not-applicable') {
	?>
	<div class="info-row">
		<strong>Class Adviser of:</strong> <span>
                                  <?php
                                  switch ($staff['class_adviser']):
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
                                </span>
	</div>
<?php } ?>

</div>