<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

// Users class
require_once BASE_PATH . '/lib/Users/Users.php';
$users = new Users();

// User ID for which we are performing operation
if ($_SESSION['admin_type'] == 'super') {
	$admin_user_id = filter_input(INPUT_GET, 'admin_user_id');
	if ($admin_user_id) ; else $admin_user_id = $_SESSION['user_id']; //
}
else $admin_user_id = $_SESSION['user_id']; //
$operation1 = filter_input(INPUT_GET, 'operation', FILTER_SANITIZE_STRING);
$operation = base64_decode($operation1);

if ($operation[0] == 's') $edit = true;
else if ($operation[0] == 't') $edit = false;
else {
	header('HTTP/1.1 401 Unauthorized', true, 401);
	exit('401 Unauthorized');
}

// Serve POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// Sanitize input post if we want
	$data_to_db = filter_input_array(INPUT_POST);

	
	$db = getDbInstance();

	if ($data_to_db['delete']) {
		$db->where('id', $admin_user_id, '=');	
		$stat = $db->update('admin_accounts', array('is_deleted' => 1));
		session_unset();
		header('location: index.php');
		exit;
	} else {
		unset($data_to_db['delete']);
	}

	// Check whether the user name already exists
	
	$db->where('user_name', $data_to_db['user_name']);
	$db->where('id', $admin_user_id, '!=');
	//print_r($data_to_db['user_name']);die();
	$row = $db->getOne('admin_accounts');
	//print_r($data_to_db['user_name']);
	//print_r($row); die();

	if (!empty($row['user_name']))
	{
		$_SESSION['failure'] = 'Username already exists';
		$query_string = http_build_query(array(
			'admin_user_id' => $admin_user_id,
			'operation' => $operation1,
		));
		header('location: edit_admin.php?'.$query_string );
		exit;
	}

	$admin_user_id = $admin_user_id;
	// Encrypting the password
	$data_to_db['password'] = md5($data_to_db['password']);

	// Reset db instance
	$db = getDbInstance();
	$db->where('id', $admin_user_id);
	$stat = $db->update('admin_accounts', $data_to_db);

	if ($stat)
	{
		$_SESSION['success'] = 'Admin user has been updated successfully';
	} else {
		$_SESSION['failure'] = 'Failed to update Admin user: ' . $db->getLastError();
	}

	header('location: admin_users.php');
	exit;
}

// Select where clause
$db = getDbInstance();
$db->where('id', $admin_user_id);

$admin_account = $db->getOne("admin_accounts");

// Set values to $row
?>
<?php include BASE_PATH . '/includes/header.php'; ?>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h2 class="page-header"><?php echo (!$edit) ? 'Add' : 'Update'; ?> <?php echo filter_input(INPUT_GET, 'admin_user_id') ? "User" : "Profile"; ?></h2>
		</div>
	</div>
	<?php include BASE_PATH . '/includes/flash_messages.php'; ?>
	<form class="well form-horizontal" action="" method="post" id="contact_form" enctype="multipart/form-data">
		<?php include BASE_PATH . '/forms/admin_users_form.php'; ?>
	</form>
</div>
<?php include BASE_PATH . '/includes/footer.php'; ?>



<script>
    $(document).ready(function() {
        $(window).resize();

    });
</script>