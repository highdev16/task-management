<?php

session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

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

	$db->where('date', $_POST['date'], '=');
	$db->where('task_id', $_POST['task_id'], '=');
	$row = $db->getOne('task_history');
	if ($db->count >= 1) {
		echo json_encode(array('status' => 'error', 'message' => "Already added at this date."));
    	exit;	
	}
	$data_to_db = array(
		'task_id' => $_POST['task_id'],
		'date' => $_POST['date'],
		'money' => $_POST['money'],
		'comment' => $_POST['comment'],
	);
	$last_id = $db->insert('task_history', $data_to_db);
	if ($last_id)
	{
		echo 'success' . $last_id;
		exit;
	} else {
		echo json_encode(array('status' => 'error', 'message' => "Invalid Command"));
    	exit;	
	}
} else {

    echo json_encode(array('status' => 'error', 'message' => "Invalid Task ID"));
    exit;
}