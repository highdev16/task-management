<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Task Management</title>

        <!-- Bootstrap Core CSS -->
        <link  rel="stylesheet" href="assets/css/bootstrap.min.css"/>

        <!-- MetisMenu CSS -->
        <link href="assets/js/metisMenu/metisMenu.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="assets/css/sb-admin-2.css" rel="stylesheet">
        <!-- Custom Fonts -->
        <link href="assets/fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script src="assets/js/jquery.min.js" type="text/javascript"></script>
        <script src="assets/js/xampp.js" type="text/javascript"></script>

    </head>

    <body>

        <div id="wrapper">

            <!-- Navigation -->
            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] == 'This is a logged user.'): ?>
                <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="index.php">Task Management</a>
                    </div>
                    <!-- /.navbar-header -->

                    <ul class="nav navbar-top-links navbar-right">
                        <!-- /.dropdown -->
                        <li class='only-screen' id='time_now'>
                            <?php echo date('Y-m-d h:i:s A'); ?>
                        </li>
                        <script> 
                            $(document).ready(function() {
                                var cc_time = <?php echo time() * 1000; ?>;
                                setInterval(function() {
                                    cc_time += 1000;
                                    var now = new Date(cc_time);
                                    $("#time_now").html(dateFormat(now, 'yyyy-mm-dd hh:MM:ss TT'));
                                }, 1000);
                            })
                        </script>
                        <!-- /.dropdown -->
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <?php echo $_SESSION['user_full_name'] . ' (' . ($_SESSION['admin_type'] == 'super' ? "Administrator" : "User") . ')'; ?>
                                <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="edit_admin.php?operation=<?php
                                    $str = 's' . rand(0, 1020300);
                                    echo urlencode(base64_encode($str));
                                ?>"><i class="fa fa-user fa-fw"></i> User Profile</a></li>
                                <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a></li>
                                <li class="divider"></li>
                                <li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                            </ul>
                            <!-- /.dropdown-user -->
                        </li>
                        <!-- /.dropdown -->
                    </ul>
                    <!-- /.navbar-top-links -->

                    <div class="navbar-default sidebar" role="navigation">
                        <div class="sidebar-nav navbar-collapse">
                            <ul class="nav" id="side-menu">
                                <li><a href="index.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                                <li<?php echo (CURRENT_PAGE == 'tasks.php' || CURRENT_PAGE == 'add_task.php') ? ' class="active"' : ''; ?>>
                                    <a href="#"><i class="fa fa-user-circle fa-fw"></i> Tasks<i class="fa arrow"></i></a>
                                    <ul class="nav nav-second-level">
                                        <li><a href="tasks.php"><i class="fa fa-list fa-fw"></i> List Current Tasks</a></li>
                                        <li><a href="all_tasks.php"><i class="fa fa-list fa-fw"></i> List All</a></li>
                                        <li><a href="past_tasks.php"><i class="fa fa-list fa-fw"></i> List Completed Tasks</a></li>
                                        
                                        <?php if (0) { ?>
                                        <li><a href="add_task.php"><i class="fa fa-plus fa-fw"></i> Add New  Task</a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                                <?php if ($_SESSION['admin_type'] == 'super') { ?>
                                <li><a href="admin_users.php"><i class="fa fa-users fa-fw"></i> Users</a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <!-- /.sidebar-collapse -->
                    </div>
                    <!-- /.navbar-static-side -->
                </nav>
            <?php endif; ?>
            <!-- The End of the Header -->
