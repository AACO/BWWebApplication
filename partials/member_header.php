<?php
define('ROOT_PATH', "../forums");
if (!defined('redirect')) { define('redirect', '../index.php'); };
include("../sso.php");
if ($user->data["is_registered"] != 1 || $user->data["is_bot"] != null) {
	header("Location: ../forums/ucp.php?mode=login&redirect=" . redirect);
	exit();
}

if (defined('perm')) {
	if ($auth->acl_get(perm) != 1) {
		header("Location: ../index.php");
		exit();
	}
}
?>
<!doctype html>
<html>
<head>
    <!-- Set metadata -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Set page title -->
    <title>Bourbon Warfare</title>

    <!-- Set favicon -->

    <!-- Import style sheets -->
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../css/summernote.css">
</head>
<body>
<!-- Import javascript -->
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery.easing.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/common.js"></script>

<!-- Add navigation bar -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand page-scroll" href="#nav">Bourbon Warfare</a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav">
            	<li><a class='page-scroll' href='#nav'>Navigation</a></li>
            	<li><a class='page-scroll' href='#section'>Section</a></li>
            </ul>
        </div>
    </div>
</nav>
<div id="results" style="display:none"></div>