<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH.'/includes/auth_validate.php';

// Serve POST method, After successful insert, redirect to tasks.php page.
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    // Mass Insert Data. Keep "name" attribute in html form same as column name in mysql table.
    $data_to_db = array_filter($_POST);
    $data_to_db['user_id'] = $_SESSION['user_id'];
    // Insert user and timestamp
    $db = getDbInstance();
    $last_id = $db->insert('tasks', $data_to_db);

    if ($last_id)
    {

        $_SESSION['success'] = 'Task added successfully!';
        // Redirect to the listing page
        header('Location: ' . ($_GET['back'] != '' ? $_GET['back'] : "tasks.php"));
        // Important! Don't execute the rest put the exit/die.
    	exit();
    }
    else
    {
        echo 'Insert failed: ' . $db->getLastError();
        exit();
    }
}

// We are using same form for adding and editing. This is a create form so declare $edit = false.
$edit = false;
?>
<?php include BASE_PATH.'/includes/header.php'; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Add Task</h2>
        </div>
    </div>
    <!-- Flash messages -->
    <?php include BASE_PATH.'/includes/flash_messages.php'; ?>
    <form class="form" action="" method="post" id="customer_form" enctype="multipart/form-data">
        <?php include BASE_PATH.'/forms/customer_form.php'; ?>
    </form>
</div>
<script type="text/javascript">
$(document).ready(function(){
   $('#customer_form').validate({
       rules: {
            task_name: {
                required: true,
                minlength: 3
            },
            start_date: {
                required: true,
                minlength: 10,
                maxlength: 10
            }            
        }
    });
});
</script>
<?php include BASE_PATH.'/includes/footer.php'; ?>

<script>
    $(document).ready(function() {
        $(window).resize();

    });
</script>