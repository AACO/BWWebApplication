<?php
include('sso.php');
$auth_options = ["u_mission_list","u_mission_admin","u_member_page","u_member_admin","u_front_page_admin"];
$global = true;


if (!class_exists('auth_admin')) {
	global $phpbb_root_path, $phpEx;

	include($phpbb_root_path . 'includes/acp/auth.' . $phpEx);
}
$auth_admin = new auth_admin();

$auth_admin->acl_add_option(array('global' => $auth_options));
?>