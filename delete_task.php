<?php 
session_start();
require_once 'includes/auth_validate.php';
require_once './config/config.php';
$del_id = filter_input(INPUT_POST, 'del_id');

if ($del_id && $_SERVER['REQUEST_METHOD'] == 'POST') 
{

    $customer_id = $del_id;

    $db = getDbInstance();
    $db->where('id', $customer_id);
    $task = $db->getOne('tasks');
    
    if ($task['created_date'] == date('Y-m-d'))  {
        $_SESSION['failure'] = "Can't delete the task created before today.";
        header('location: tasks.php');
        exit;   
    }
    $db->where('id', $customer_id);
    $db->update('tasks', array('is_deleted' => 1));
    if ($status) 
    {
        $_SESSION['info'] = "Task deleted successfully!";
        header('location: tasks.php');
        exit;
    }
    else
    {
    	$_SESSION['failure'] = "Unable to delete Task";
    	header('location: tasks.php');
        exit;

    }
    
}