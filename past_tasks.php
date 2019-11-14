<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

// Costumers class
require_once BASE_PATH . '/lib/Costumers/Costumers.php';
$costumers = new Costumers();

// Get Input data from query string
$order_by   = filter_input(INPUT_GET, 'order_by');
$order_dir  = filter_input(INPUT_GET, 'order_dir');
$search_str = str_replace('%', ' ', filter_input(INPUT_GET, 'search_str'));

// Per page limit for pagination
$pagelimit = 3000000;

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
    $order_dir = 'Asc';
}

// Get DB instance. i.e instance of MYSQLiDB Library
$db = getDbInstance();
$select = array('id', 'task_name', 'start_date', 'end_date', 'last_update', 'comment', 'contact1','contact2','contact3');

// Start building query according to input parameters
// If search string

$start_date = array_key_exists('search_start_date', $_GET) ? $_GET['search_start_date'] : '0000-00-00';
if (!preg_match("/^\d\d\d\d-\d\d-\d\d$/", $start_date)) 
    $start_date = '0000-00-00';

$end_date = array_key_exists('search_end_date', $_GET) ? $_GET['search_end_date'] : '9999-99-99';
if (!preg_match("/^\d\d\d\d-\d\d-\d\d$/", $end_date)) 
    $end_date = '9999-99-99';


$where = '1';
if ($search_str) {
    $where = '`task_name` like "%'. $search_str . '%" and `is_deleted` = 0 and `end_date` not like "9999-99-99"';    
} else {
    $where = '`is_deleted` = 0 and `end_date` not like "9999-99-99"';        
}

// $where .= " and not ( `start_date` < '$start_date' and `end_date` < '$start_date' )";
// $where .= " and not ( `start_date` > '$end_date' and `end_date` > '$end_date' )";


// If order direction option selected
if ($order_dir) {
    $db->orderBy($order_by, $order_dir);
}

// Set pagination limit
$db->pageLimit = $pagelimit;

// Get result of the query
$rows = $db->arraybuilder()->rawQuery("select * from tasks where $where order by `$order_by` $order_dir limit " . (($page - 1) * $pagelimit) . ", $pagelimit");

$keyArrays = array(-1);
foreach ($rows as $r) {
    $keyArrays[] = $r['id'];
}

$keyArraysStr = implode(',', $keyArrays);
$keyArraysStr = $db->query("select * from task_history where task_id in ($keyArraysStr) and `date` <= '$end_date' and `date` >= '$start_date' order by `date` desc, `id`");

$keyArrays = array();

foreach ($keyArraysStr as $r) {
    if (array_key_exists($r['task_id'], $keyArrays)) {
        if (abs($r['money']) >= 0.001) {
            $keyArrays[$r['task_id']][] = $r['money'];            
        }
    }
    else 
        $keyArrays[$r['task_id']] = array($r['date'], $r['money']);
}

$total_pages = $db->totalPages;
?>
<?php include BASE_PATH . '/includes/header.php'; ?>
<!-- <script src="https://code.jquery.com/jquery-3.3.1.js"></script> -->
<!-- Main container -->
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-6">
            <h1 class="page-header">Completed Tasks</h1>
        </div>
        <div class="col-lg-6">
            <div class="page-action-links text-right">
                <a href="add_task.php?operation=create&back=past_tasks.php" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Add new Task</a>
            </div>
        </div>
    </div>
    <?php include BASE_PATH . '/includes/flash_messages.php'; ?>

    <!-- Filters -->
    <div class="well text-center filter-form">
        <form class="form form-inline" action="">
            <label for="input_search" class='padding-fullscreen-left'>Search</label>
            <input type="text" class="form-control" id="input_search" name="search_str" value="<?php echo htmlspecialchars($search_str, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="input_order" class='padding-fullscreen-left'>Order By</label>
            <select name="order_by" class="form-control">
                <?php
foreach ($costumers->setOrderingValues() as $opt_value => $opt_name):
    ($order_by === $opt_value) ? $selected = 'selected' : $selected = '';
    echo ' <option value="' . $opt_value . '" ' . $selected . '>' . $opt_name . '</option>';
endforeach;
?>
            </select>
            <select name="order_dir" class="form-control" id="input_order">
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
            <label for="search_start_date" class='padding-fullscreen-left'>Period</label>
            <input type="text" class="form-control" id="search_start_date" name="search_start_date" value="<?php echo (array_key_exists('search_start_date', $_GET)) ? $_GET['search_start_date'] : ''; ?>" placeholder='YYYY-MM-DD'>
            <label for="search_end_date">~</label>
            <input type="text" class="form-control" id="search_end_date" name="search_end_date" value="<?php echo (array_key_exists('search_end_date', $_GET)) ? $_GET['search_end_date'] : ''; ?>"  placeholder='YYYY-MM-DD'>
            <input type="submit" value="Go" class="btn btn-primary">
            <label id='total_sum' style='color: blue'></label>
        </form>
    </div>
    <hr>
    <!-- //Filters -->

    <style>
        td a.btn {
            padding: 6px 8px !important;
        }
    </style>
        <!-- Table -->
    <table class="table table-striped table-bordered table-condensed" id='data_table'>
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="20%">Title</th>
                <th width="17%">Comment</th>                
                <th width="6%">Start Date</th>
                <th width="6%">End Date</th>
                <th width="6%">Last Update</th>
                <th width="9%">Total Income</th>                
                <th width="9%">Last Income</th>                
                <th width="13%">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $count = 1; foreach ($rows as $row): ?>
            <tr>
                <td><?php echo $count++; ?></td>
                <td><?php echo htmlspecialchars($row['task_name']); ?></td>
                <td><?php echo htmlspecialchars($row['comment']); ?></td>
                <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                <td><?php echo htmlspecialchars($row['end_date'] == '9999-99-99' ? "" : $row['end_date']); ?></td>
                <td>
                    <?php 
                        $sum = 0; 
                        if (array_key_exists($row['id'], $keyArrays) && sizeof($keyArrays[$row['id']]) > 0) 
                            foreach ($keyArrays[$row['id']] as $i => $r) 
                                if ($i > 0)
                                    $sum += $r; 
                        if (array_key_exists($row['id'], $keyArrays) && sizeof($keyArrays[$row['id']]) > 0) 
                            echo htmlspecialchars($keyArrays[$row['id']][0]); 
                        else echo "";
                    ?>
                </td>
                <td>
                    <?php                         
                        echo htmlspecialchars(sprintf("%0.2f", $sum)); 
                    ?>                            
                </td>
                <td>
                    <?php 
                        if (array_key_exists($row['id'], $keyArrays) && sizeof($keyArrays[$row['id']]) > 0)
                            echo htmlspecialchars(sprintf("%0.2f", $keyArrays[$row['id']][1])); 
                        else echo '-';
                    ?>
                </td>
                <td>
                    <a href="edit_task.php?back=past_tasks.php&task_id=<?php echo $row['id']; ?>&operation=<?php echo urlencode(base64_encode('s' . rand(20000,10000000))); ?>" class="btn btn-primary"><i class="glyphicon glyphicon-edit" title='Edit Task'></i></a>
                    <a style='display:none' href="#" title='Add Daily History' class="btn btn-success delete_btn" onclick="$('#classModaladd_<?php echo $row['id']; ?>').modal('show'); $('#add_index_<?php echo $row['id']; ?>').val(<?php echo $row['id']; ?>);"><i class="glyphicon glyphicon-plus"></i></a>
                    <?php 
                        if ($row['created_date'] == date('Y-m-d')) {
                    ?>
                    <a href="#" title='See all daily history' class="btn btn-warning delete_btn" onclick="$('#classModal_<?php echo $row['id']; ?>').modal('show');"><i class="glyphicon glyphicon-tasks"></i></a>
                    <?php
                        }
                    ?>
                    <a href="#" class="btn btn-danger delete_btn" title='Delete' onclick="$('#confirm-delete-<?php echo $row['id']; ?>').modal('show'); "><i class="glyphicon glyphicon-trash"></i></a>
                </td>
            </tr>
            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="confirm-delete-<?php echo $row['id']; ?>" role="dialog">
                <div class="modal-dialog">
                    <form action="delete_task.php" method="POST">
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
                                <button type="submit" class="btn btn-default btn-danger pull-left">Yes</button>
                                <button type="button" class="btn btn-default btn-primary" data-dismiss="modal">No</button>
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
        <?php echo paginationLinks($page, $total_pages, 'tasks.php'); ?>
    </div>
    <!-- //Pagination -->
</div>
<!-- //Main container -->
<?php include BASE_PATH . '/includes/footer.php'; ?>

<?php foreach ($rows as $row) { ?>
<div id="classModal_<?php echo $row['id']; ?>" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="classInfo" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          ×
        </button>
        <h4 class="modal-title" id="classModalLabel">
              Daily History (<?php echo htmlspecialchars($row['task_name']); ?>)
            </h4>
      </div>
      <div class="modal-body">
        <input type='hidden' id='add_index_<?php echo $row['id']; ?>'>
        <table class='table table-bordered'>
            <tr>
                <td>Date</td>
                <td><input type='text' placeholder="YYYY-MM-DD" id='m_date' value='<?php echo date('Y-m-d'); ?>'></td>
            </tr>
            <tr>
                <td>Amount $</td>
                <td><input type='text' placeholder="" id='m_money' value='0'></td>
            </tr>
            <tr>
                <td style='width:20%'>Comment</td>
                <td style='width:80%'>
                    <textarea id='m_comment' style='min-width:100%;resize: none; max-width: 100%; min-height: 50px'></textarea>
                    <button type="button" class="pull-left btn btn-primary" onclick='save_daily_history(<?php echo $row['id']; ?>)'>
                      Save
                    </button>
                </td>
            </tr>

        </table>
        <table  class="table table-bordered detail_history" id="<?php echo "table_detail_history_" . $row['id']; ?>">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Date</th>
                    <th>Money</th>
                    <th>Comment</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $no = 1;
                    foreach ($keyArraysStr as $index=>$k) {
                        if ($k['task_id'] != $row['id']) continue;

                        echo "<tr id='detail_row_{$k['task_id']}_{$k['id']}'>";
                        echo "<td style='width:5%'>$no</td><td style='width:25%; text-align: center'>" . htmlspecialchars($k['date']) . "</td>";
                        echo "<td style='width:20%; text-align: right; padding-right: 10px'>" . htmlspecialchars($k['money']) . "</td>";
                        echo "<td style='width:45%'>" . htmlspecialchars($k['comment']) . "</td>";
                        echo "<td style='width:10%;  text-align: center'>";

                        if ($k['created_date'] == date('Y-m-d'))
                            echo "<a href=\"#\" class=\"btn btn-danger delete_btn\" onclick='delete_detail_history({$k['task_id']}, {$k['id']})'><i class=\"glyphicon glyphicon-trash\"></i></a>";
                        else echo "Saved";
                        echo "</td></tr>";

                        $no++;
                    }
                ?>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick='if (confirm("Are you sure?")) window.location.reload();'>
          Close
        </button>
      </div>
    </div>
  </div>
</div>

<?php } ?>

<script>
$(document).ready(function () {
  $('.detail_history').each(function() {
        $(this).DataTable({"paging": true});
        $('.dataTables_length').addClass('bs-select');
    })
});;

function save_daily_history(t) {
    if (Math.abs($("#classModal_" + t + " #m_money").val()) < 0.0001) {
        alert("The balance 0 can't be added");
        return;
    }

    $.post('save_daily_history.php', {
        task_id: t,
        date: $("#classModal_" + t + " #m_date").val(),
        money: $("#classModal_" + t + " #m_money").val(),
        comment: $("#classModal_" + t + " #m_comment").val()
    }, function (a, b) {
        if (a.substr(0, 7) == 'success' && b == 'success') {
            alert ("Successfully Added!");            
            var rowIndex = $('#table_detail_history_' + t).dataTable()
                .fnAddData(["<b>-</b>",
                    "<b>" + $("#classModal_" + t + " #m_date").val() + "</b>",
                    "<b>" + $("#classModal_" + t + " #m_money").val() + "</b>",
                    "<b>" + $("#classModal_" + t + " #m_comment").val() + "</b>",
                    "<a href=\"#\" class=\"btn btn-danger delete_btn\" onclick='delete_detail_history(" + t + ", " + a.substr(7) + ")'><i class=\"glyphicon glyphicon-trash\"></i></a>"], true);

            var row = $('#table_detail_history_' + t).dataTable().fnGetNodes(rowIndex);
            $(row).attr('id', "detail_row_" + t + "_" + a.substr(7));

            var my_array = $('#table_detail_history_' + t).dataTable().fnGetNodes( );
            var last_element = my_array[my_array.length - 1];
 
            $(last_element).insertBefore($('#table_detail_history_' + t + ' tbody tr:first-child'));
        } else {
            try {
                a = JSON.parse(a);
                alert (a['message']);
            } catch (e) {
                alert ("Unexpected Error!");
            }
        }
    })
}

function delete_detail_history(task_id, history_id) {
    if (!confirm("Are you sure?")) return;

    $.post('delete_daily_history.php', {
        task_id: task_id,
        history_id: history_id
    }, function (a, b) {
        if (a == 'success' && b == 'success') {
            alert ("Successfully Removed!");
            $('#table_detail_history_' + task_id).dataTable().fnDeleteRow($("#detail_row_" + task_id + "_" + history_id)[0]);
        } else {
            try {
                a = JSON.parse(a);
                alert (a['message']);                
            } catch (e) {
                alert ("Unexpected Error!");
            }
        }
    })   
}
</script>


<script src='assets/js/datatables.min.js'></script>
<script src='assets/js/datatables.bootstrap.js'></script>
<!-- <script src="assets/js/bootstrap.min.js"></script> -->

<!-- Metis Menu Plugin JavaScript -->
<script src="assets/js/metisMenu/metisMenu.min.js"></script>




<!-- Custom Theme JavaScript -->
<script src="assets/js/sb-admin-2.js"></script>
<script src="assets/js/jquery.validate.min.js"></script>

<script>
    $(document).ready(function() {
        $(window).resize();

        var totalSum = 0;

        $("#data_table tr").each(function(ind, ele) {
            if (ind > 0) {
                totalSum += Number($(this).find('td:nth-child(7)').html());
            }
        })

        $("#total_sum").html("Total: " + totalSum.toFixed(2));
    });
</script>