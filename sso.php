<?php
define('IN_PHPBB', true);
if (!defined('ROOT_PATH')) {
	define('ROOT_PATH', "forums");
};

$phpEx = "php";
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : ROOT_PATH . '/';
include($phpbb_root_path . 'common.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
?>