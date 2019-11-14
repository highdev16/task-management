<?php

session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

if ($_SESSION['user_logged_in'] != 'This is a logged user.') {
    echo json_encode(array('status' => 'error', 'message' => "User not logged in"));
    exit;
}

$user_id = $_SESSION['user_id'];

$db = getDbInstance();

$db->where('id', $_POST['task_id'], '=');
$row = $db->getOne('tasks');

if ($db->count >= 1) {
	if ($row['is_deleted']) {
		echo json_encode(array('status' => 'error', 'message' => "Deleted Task!"));
    	exit;	
	}

	if ($row['user_id'] != $user_id) {
		echo json_encode(array('status' => 'error', 'message' => "Wrong user!"));
    	exit;		
	}

	if ($row['created_date'] != date('Y-m-d')) {
		echo json_encode(array('status' => 'error', 'message' => "You can't delete the data that are created in the past."));
    	exit;		
	}

	$db = getDbInstance();
	// sql to delete a record
	$db->where('id', $_POST['history_id'], '=');
	$sql = $db->delete('task_history');

	if ($sql) {
	    echo 'success';
		exit;
	} else {
	    echo json_encode(array('status' => 'error', 'message' => "Invalid Command"));
    	exit;	
	}	
} else {

    echo json_encode(array('status' => 'error', 'message' => "Invalid Task ID"));
    exit;
}

