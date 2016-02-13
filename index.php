<!doctype html>
<?php
include '/config/config.php';
include '/sub_services/get_service.php';

function catch_execution_error($execution, $connection) {
    if (!$execution) {
        $error = $connection->error;
        error_log($error);
        exit;
    }
}

// Create connection
$connection = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_database, $mysql_serverport);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$get_intro_query = <<<SQL
select id
from section
where type = 'INTRO'
limit 1
SQL;

$get_nav_query = <<<SQL
select ni.id, ni.type, ni.text, ifnull(nitl.url, concat('#', nits.section_id)) as ref
from nav_item ni
left join nav_item_to_section nits on nits.nav_item_id = ni.id and nits.nav_item_type = 'NAV_ITEM'
left join nav_item_to_link nitl on nitl.nav_item_id = ni.id and nitl.nav_item_type = 'NAV_ITEM'
order by ni.order asc;
SQL;

$get_sub_nav_query = <<<SQL
select sni.text, ifnull(nitl.url, concat('#', nits.section_id)) as ref
from sub_nav_item sni
left join nav_item_to_section nits on nits.nav_item_id = sni.id and nits.nav_item_type = 'SUB_NAV_ITEM'
left join nav_item_to_link nitl on nitl.nav_item_id = sni.id and nitl.nav_item_type = 'SUB_NAV_ITEM'
where sni.nav_item_id = ?
order by sni.order asc;
SQL;

$get_sub_nav_stmt = mysqli_prepare($connection, $get_sub_nav_query);

function get_sections_array($connection) {
	$connection->begin_transaction();
	$get_section_query = <<<SQL
select s.id, s.type, s.title
from section s
order by s.order asc;
SQL;
	if ($results = $connection->query($get_section_query)) {
		$result_array = [];
		while ($row = $results->fetch_assoc()) {
            $id = $row['id'];
            $type = $row['type'];
            $array_builder = array("id" => $id, "title" => $row['title'], "type" => $type, "text" => "", "buttonText" => "", "images" => [], "youtubes" => []);
            
            if ($type == "TEXT" || $type == "INTRO" || $type == "JOIN" || $type == "MODS") {
                $array_builder["text"] = get_section_text($connection, $id);
				if ($type == "JOIN" || $type == "MODS") {
					 $array_builder["buttonText"] = get_section_button_text($connection, $id);
				}
            }
            else if ($type == "MEDIA") {
                $array_builder["images"] = get_section_images($connection, $id);
                $array_builder["youtubes"] = get_section_youtubes($connection, $id);
            }
            
			$result_array[] = $array_builder;
		}
		$results->close();
		return $result_array;
	}
    else {
        $error = $connection->error;
        error_log("$error");
        exit;
    }
	$connection->commit();
}

?>
<html>
<head>
    <!-- Set metadata -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="ArmA, ArmA3, Bourbon Warfare, BW">
    <meta name="description" content="Bourbon Warfare an ArmA3 Gaming Community">
    <meta name="author" content="Mark Ruffner">

    <!-- Set page title -->
    <title>Bourbon Warfare</title>

    <!-- Set favicon -->

    <!-- Import style sheets -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
</head>
<body data-spy="scroll" data-target=".navbar-fixed-top">
<!-- Import javascript -->
<script src="js/jquery.js"></script>
<script src="js/jquery.easing.js"></script>
<script src="js/bootstrap.js"></script>

<!-- Add navigation bar -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand page-scroll" href="#<?php if ($results = $connection->query($get_intro_query)) {$row = $results->fetch_assoc(); print($row['id']);} ?>">Bourbon Warfare</a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav">
				<?php
                    if ($results = $connection->query($get_nav_query)) {
                        while ($row = $results->fetch_assoc()) {
                            if ($row['type'] == 'DROPDOWN') {
                                printf("<li class='dropdown'><a class='dropdown-toggle' data-toggle='dropdown' href='#'>%s <span class='caret'></span></a><ul class='dropdown-menu'>", $row['text']);

                                $get_sub_nav_stmt->bind_param("i", $row['id']);
                                $get_sub_nav_stmt->execute();
                                $sub_results = $get_sub_nav_stmt->get_result();
                                while ($sub_row = $sub_results->fetch_assoc()) {
                                    printf("<li><a class='page-scroll' href='%s'>%s</a></li>", $sub_row['ref'], $sub_row['text']);
                                }
                                
                                printf("</ul></li>");
                            }
                            else if ($row['type'] == 'LINK') {
                                printf("<li><a href='%s'>%s</a></li>", $row['ref'], $row['text']);
                            }
                            else {
                                printf("<li><a class='page-scroll' href='%s'>%s</a></li>", $row['ref'], $row['text']);
                            }
                        }
                        $results->close();
                    }
                ?>
            </ul>
        </div>
    </div>
</nav>
<?php
$sections = get_sections_array($connection);
foreach ($sections as $section) {
	printf("<section id='%s' class='content-section text-center'><h2>%s</h2>", $section['id'], $section['title']);
	if ($section['type'] == "TEXT" || $section['type'] == "INTRO") {
		print($section['text']);
	}
	else if ($section['type'] == "MEDIA") {
			print("<div class='container-fluid youtube'>");
			foreach ($section['youtubes'] as $youtube) {
				printf("<a href='%s' class='btn btn-default btn-yt btn-lg'><i class='fa fa-youtube fa-fw'></i> <span class='network-name'>%s</span></a>", $youtube['channelUrl'], $youtube['channelName']);
			}
			print("</div>");
			
			$carousel_id = "carousel-" . $section['id'];
		?>
			<div id="<?php print($carousel_id) ?>" class="carousel slide" data-ride="carousel">
				<!-- Indicators -->
				<ol class="carousel-indicators">
					<?php
						$first = true;
						for ($i = 0; $i < count($section['images']); $i++) {
							printf("<li data-target='#%s' data-slide-to='%i' class='%s'></li>", $carousel_id, $i, $first ? "active" : "");
							$first = false;
						}
					?>
				</ol>

			    <!-- Wrapper for slides -->
				<div class="carousel-inner" role="listbox">
					<?php
						$first = true;
						foreach ($section['images'] as $image) {
							printf("<div class='item%s'><img src='img/screenshots/%s' alt='%s'></div>", $first ? " active" : "", $image['filePath'], $image['title']);
							$first = false;
						}
					?>
				</div>

			  <!-- Controls -->
			  <a class="left carousel-control" href="#<?php print($carousel_id) ?>" role="button" data-slide="prev">
				<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			  </a>
			  <a class="right carousel-control" href="#<?php print($carousel_id) ?>" role="button" data-slide="next">
				<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			  </a>
			</div>
		<?php
	}
	print("</section>");
}
?>
</body>
</html>
