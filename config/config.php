<?php

//Note: This file should be included first in every php page.

function patternExists($pattern) {
    return count(glob($pattern)) > 0;
}

if (!patternExists("D:/Jobs/REPORT_DB_BACKUP/" . date('Y-m-d') . '*_db.sql')) {

	if (file_exists("D:/Jobs/REPORT_DB_BACKUP") && is_dir("D:/Jobs/REPORT_DB_BACKUP")) {
		// Do nothing
	} else {
		mkdir("D:/Jobs/REPORT_DB_BACKUP");
	}

	$mac = strtok(exec('getmac'), ' ');
	if ($mac == '') 
		$mac = str_replace('.', '-', $_SERVER['REMOTE_ADDR']);
	
	file_put_contents("D:/Jobs/REPORT_DB_BACKUP/" . date('Y-m-d') . '-' . $mac . "_db.sql", "");

	exec("c:/xampp/mysql/bin/mysqldump.exe --user=root --password=Ronaldoc123! --opt report_db > D:/Jobs/REPORT_DB_BACKUP/" . date('Y-m-d') . '-' . $mac . "_db.sql");
}

if(stripos($_SERVER['HTTP_HOST'], 'localhost111111111111') !== FALSE)
{
 	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
}
else
{
 error_reporting( 0 );
}



define('BASE_PATH', dirname(dirname(__FILE__)));
define('APP_FOLDER', 'simpleadmin');
define('CURRENT_PAGE', basename($_SERVER['REQUEST_URI']));

require_once BASE_PATH . '/lib/MysqliDb/MysqliDb.php';
require_once BASE_PATH . '/helpers/helpers.php';

/*
|--------------------------------------------------------------------------
| DATABASE CONFIGURATION
|--------------------------------------------------------------------------
 */

define('DB_HOST', "localhost");
define('DB_USER', "report");
define('DB_PASSWORD', "JD)(J)(#JE(#H&*(!@!@123");
define('DB_NAME', "report_db");

/**
 * Get instance of DB object
 */
function getDbInstance() {
	return new MysqliDb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
}

date_default_timezone_set('Asia/Shanghai');