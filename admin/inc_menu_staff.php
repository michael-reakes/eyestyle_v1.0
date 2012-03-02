<div class="panel">
	<div class="title">Staff Management</div>
	<div class="list">
		<a href="staff_staff.php">Staff Accounts</a>
		<?php if (access::verify($_STAFF->access, 'staff.group')) {?>
			<a href="staff_groups.php">User Groups</a>
		<?php } ?>
	</div>
</div>
