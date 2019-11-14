<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

// Users class
require_once BASE_PATH . '/lib/Users/Users.php';

if ($_SESSION['admin_type'] != 'super') {
    header('HTTP/1.1 401 Unauthorized', true, 401);
    exit('401 Unauthorized');
}

$users = new Users();

// Get Input data from query string
$del_id		= filter_input(INPUT_GET, 'del_id');
$order_by	= filter_input(INPUT_GET, 'order_by');
$order_dir	= filter_input(INPUT_GET, 'order_dir');
$search_str	= str_replace('%', ' ', filter_input(INPUT_GET, 'search_str'));

// Per page limit for pagination
$pagelimit = 3;

// Get current page
$page = filter_input(INPUT_GET, 'page');
if (!$page) {
	$page = 1;
}

// If filter types are not selected we show latest added data first
if (!$order_by) {
	$order_by = 'id';
}
if (!$order_dir) {
	$order_dir = 'asc';
}

// Get DB instance. i.e instance of MYSQLiDB Library
$db = getDbInstance();
$select = array('id', 'user_name', 'admin_type', 'full_name', 'birthday');

// Start building query according to input parameters
// If search string
if ($search_str) {
	$db->where('user_name', '%' . $search_str . '%', 'like');
    $db->orWhere('full_name', '%' . $search_str . '%', 'like');
    $db->orWhere('admin_type', $search_str , 'like');
    $db->where('is_deleted', '0', '=');    
} else 
    $db->where('is_deleted', '0', '=');

// If order direction option selected
if ($order_dir == 'Desc') {
	$db->orderBy($order_by, $order_dir);
} else $db->orderBy('id', 'asc');

// Set pagination limit
$db->pageLimit = $pagelimit;

// Get result of the query
$rows = $db->arraybuilder()->paginate('admin_accounts', $page, $select);
$total_pages = $db->totalPages;
?>
<?php include BASE_PATH . '/includes/header.php'; ?>
<!-- Main container -->
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-6">
            <h1 class="page-header">Users</h1>
        </div>
        <div class="col-lg-6">
            <div class="page-action-links text-right">
                <a href="add_admin.php" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Add new user</a>
            </div>
        </div>
    </div>
    <?php include BASE_PATH . '/includes/flash_messages.php'; ?>

    <?php
    if (isset($del_stat) && $del_stat == 1)
    {
        echo '<div class="alert alert-info">Successfully deleted</div>';
    }
    ?>
    
    <!-- Filters -->
    <div class="well filter-form">
        <form class="form form-inline" action="" id='form_table'>
            <label for="input_search">Search</label>
            <input type="text" class="form-control" id="input_search" name="search_str" value="<?php echo htmlspecialchars($search_str, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="input_order" style='display:none'>Order By</label>
            <select name="order_by" id='order_by' class="form-control" style='display:none'>
                <?php
foreach ($users->setOrderingValues() as $opt_value => $opt_name):
	($order_by === $opt_value) ? $selected = 'selected' : $selected = '';
	echo ' <option value="' . $opt_value . '" ' . $selected . '>' . $opt_name . '</option>';
endforeach;
?>
            </select>
            <select name="order_dir" style='display:none'  class="form-control" id="input_order">
                <option value="Asc" <?php
if ($order_dir == 'Asc') {
	echo 'selected';
}
?> >Asc</option>
                <option value="Desc" <?php
if ($order_dir == 'Desc') {
	echo 'selected';
}
?>>Desc</option>
            </select>
            <input type="submit" id='submit_button' style='margin-left:40px' value="Go" class="btn btn-primary">
        </form>
    </div>
    <hr>
    <!-- //Filters -->

    <!-- Table -->
    <table class="table table-striped table-bordered table-condensed">
        <thead>
            <tr>
                <th width="5%" onclick='sort_table("id")' style='cursor: pointer'>ID</th>
                <th width="15%" onclick='sort_table("user_name")' style='cursor: pointer'>Username</th>
                <th width="15%" onclick='sort_table("full_name")' style='cursor: pointer'>Full Name</th>
                <th width="20%" onclick='sort_table("birthday")' style='cursor: pointer'>Birthday</th>
                <th width="30%" onclick='sort_table("admin_type")' style='cursor: pointer'>Admin type</th>
                <th width="20%">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $index => $row): ?>
            <tr>
                <td style='padding-left:10px; vertical-align: middle;'><?php echo $index + 1; ?></td>
                <td style='vertical-align: middle;'><?php echo htmlspecialchars($row['user_name']); ?></td>
                <td style='vertical-align: middle;'><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td style='vertical-align: middle;' style='vertical-align: middle;'><?php echo htmlspecialchars($row['birthday']); ?></td>
                <td style='vertical-align: middle;'><?php echo htmlspecialchars(($row['admin_type'] == 'super' ? "Administrator" : "User")); ?></td>
                <td>
                    <a href="edit_admin.php?admin_user_id=<?php echo $row['id']; ?>&operation=<?php
                                    $str = 's' . rand(0, 1020300);
                                    echo urlencode(base64_encode($str));
                                ?>" class="btn btn-primary"><i class="glyphicon glyphicon-edit"></i></a>
                    <a href="#" class="btn btn-danger delete_btn" data-toggle="modal" data-target="#confirm-delete-<?php echo $row['id']; ?>"><i class="glyphicon glyphicon-trash"></i></a>
                </td>
            </tr>
            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="confirm-delete-<?php echo $row['id']; ?>" role="dialog">
                <div class="modal-dialog">
                    <form action="delete_user.php" method="POST">
                        <!-- Modal content -->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Confirm</h4>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="del_id" id="del_id" value="<?php echo $row['id']; ?>">
                                <p>Are you sure you want to delete this row?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-default pull-left">Yes</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- //Delete Confirmation Modal -->
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- //Table -->

    <!-- Pagination -->
    <div class="text-center">
    	<?php echo paginationLinks($page, $total_pages, 'admin_users.php'); ?>
    </div>
    <!-- //Pagination -->
</div>
<!-- //Main container -->
<?php include BASE_PATH . '/includes/footer.php'; ?>

<script>
    function sort_table(id) {
        document.getElementById('order_by').value = id;
        document.getElementById('input_order').value = document.getElementById('input_order').value == 'Asc' ? 'Desc' : 'Asc';
        document.getElementById('form_table').submit();
    }
</script>


<script>
    $(document).ready(function() {
        $(window).resize();

    });
</script>