<?php

define('ROOT_PATH', "../forums");
include("../sso.php");
if ($user->data["is_registered"] != 1 || $user->data["is_bot"] != null) {
	die('[["ERROR"],["No active session, please login"]]');
}
if ($auth->acl_get("u_front_page_admin") != 1) {
	die('[["ERROR"],["No permission to access this resource, please contact an Admin"]]');
}
$request->enable_super_globals();
include '/../config/config.php';

// Create connection
$connection = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_database, $mysql_serverport);

// Check connection
if ($connection->connect_error) {
    die('[["ERROR"],["Problem connecting to the database"]]');
}

$connection->autocommit(false);

include '/../sub_services/get_service.php';
include '/../sub_services/delete_service.php';
include '/../sub_services/update_service.php';

$connection->close();

function catch_execution_error($execution, $connection) {
    if (!$execution) {
        $error = $connection->error;
        error_log($error);
        print("[[\"ERROR\"],[\"Database Error: $error\"]]");
        exit;
    }
}
?>