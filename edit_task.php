<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH.'/includes/auth_validate.php';

// Sanitize if you want
$task_id = filter_input(INPUT_GET, 'task_id', FILTER_VALIDATE_INT);

if ($task_id >= 1) ;
else {
    header('HTTP/1.1 401 Unauthorized', true, 401);
    exit('401 Unauthorized');
}
$operation = filter_input(INPUT_GET, 'operation', FILTER_SANITIZE_STRING); 
$operation1 = base64_decode($operation);

if ($operation1[0] == 's') $edit = true;
else if ($operation1[0] == 't') $edit = false;
else {
    header('HTTP/1.1 401 Unauthorized', true, 401);
    exit('401 Unauthorized');
}
$db = getDbInstance();

// Handle update request. As the form's action attribute is set to the same script, but 'POST' method, 
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    // Get customer id form query string parameter.
    $task_id = filter_input(INPUT_GET, 'task_id', FILTER_SANITIZE_STRING);

    // Get input data
    $data_to_db = filter_input_array(INPUT_POST);

    
    $db = getDbInstance();
    $db->where('id', $task_id);
    $stat = $db->update('tasks', $data_to_db);

    if ($stat)
    {
        $_SESSION['success'] = 'Task updated successfully!';
        // Redirect to the listing page
        header('Location: ' . ($_GET['back'] != '' ? $_GET['back'] : "tasks.php"));
        // Important! Don't execute the rest put the exit/die.
        exit();
    }
}

// If edit variable is set, we are performing the update operation.
if ($edit)
{
    $db->where('id', $task_id);
    // Get data to pre-populate the form.
    $customer = $db->getOne('tasks');
}
?>
<?php include BASE_PATH.'/includes/header.php'; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Update Task</h2>
        </div>
    </div>
    <!-- Flash messages -->
    <?php include BASE_PATH.'/includes/flash_messages.php'; ?>
    <form class="form" action="" method="post" id="customer_form" enctype="multipart/form-data">
        <?php include BASE_PATH.'/forms/customer_form.php'; ?>
    </form>
</div>
<?php include BASE_PATH.'/includes/footer.php'; ?>



<script>
    $(document).ready(function() {
        $(window).resize();

    });
</script>