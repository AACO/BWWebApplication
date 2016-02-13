<?php
include '/../queries/get_queries.php';

/* Operations */
if (isset($_GET['req'])) {
	switch ($_GET['req']) {
		case 0:
			get_nav_items($connection);
			break;
		case 1:
			get_sections($connection);
			break;
        default:
            print('[["ERROR"]["Request had an invalid ID"]]');
	}
}

function get_sections($connection) {
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
		print("[[\"SUCCESS\"],");
		print(json_encode($result_array));
		print("]");
		$results->close();
	}
    else {
        $error = $connection->error;
        print("[[\"ERROR\"],[\"$error\"]]");
        exit;
    }
	$connection->commit();
}

function get_section_text($connection, $id) {
	$return_text = "";
	$get_section_text_stmt = get_section_text_stmt($connection);
	$get_section_text_stmt->bind_param("i", $id);
	catch_execution_error($get_section_text_stmt->execute(), $connection);
	$results = $get_section_text_stmt->get_result();
	
	while ($row = $results->fetch_assoc()) {
		$return_text = $return_text . $row['text'];
	}
	
	return $return_text;
}

function get_section_button_text($connection, $id) {
	$return_text = "";
	$get_section_button_text_stmt = get_section_button_text_stmt($connection);
	$get_section_button_text_stmt->bind_param("i", $id);
	catch_execution_error($get_section_button_text_stmt->execute(), $connection);
	$results = $get_section_button_text_stmt->get_result();
	
	while ($row = $results->fetch_assoc()) {
		$return_text = $return_text . $row['button_text'];
	}
	
	return $return_text;
}

function get_section_images($connection, $id) {
	$return_array = [];
	$get_section_images_stmt = get_section_images_stmt($connection);
	$get_section_images_stmt->bind_param("i", $id);
	catch_execution_error($get_section_images_stmt->execute(), $connection);
	$results = $get_section_images_stmt->get_result();
	
	while ($row = $results->fetch_assoc()) {
		$return_array[] = array("id" => $row['id'], "filePath" => $row['file_path'], "title" => $row['title']);
	}
	
	return $return_array;
}

function get_image($connection, $id) {
	$filePath = "";
	$get_image_stmt = get_image_stmt($connection);
	$get_image_stmt->bind_param("i", $id);
	catch_execution_error($get_image_stmt->execute(), $connection);
	$results = $get_image_stmt->get_result();
	
	while ($row = $results->fetch_assoc()) {
		$filePath = $row['file_path'];
	}
	
	return $filePath;
}

function get_section_youtubes($connection, $id) {
	$return_array = [];
	$get_section_youtubes_stmt = get_section_youtubes_stmt($connection);
	$get_section_youtubes_stmt->bind_param("i", $id);
	catch_execution_error($get_section_youtubes_stmt->execute(), $connection);
	$results = $get_section_youtubes_stmt->get_result();
	
	while ($row = $results->fetch_assoc()) {
		$return_array[] = array("id" => $row['id'], "channelName" => $row['channel_name'], "channelUrl" => $row['channel_url']);
	}
	
	return $return_array;
}

function is_section_referenced($connection, $id) {
	$referenced = false;
	$section_count_stmt = section_count_stmt($connection);
	$section_count_stmt->bind_param("i", $id);
	catch_execution_error($section_count_stmt->execute(), $connection);
	$results = $section_count_stmt->get_result();
	
	while ($row = $results->fetch_assoc()) {
		$referenced = $row['count(id)'] > 0;
	}
	
	return $referenced;
}

function get_nav_items($connection) {
	$connection->begin_transaction();
	$get_nav_query = <<<SQL
select ni.id, ni.type, ni.text, nitl.url, nits.section_id
from nav_item ni
left join nav_item_to_section nits on nits.nav_item_id = ni.id and nits.nav_item_type = 'NAV_ITEM'
left join nav_item_to_link nitl on nitl.nav_item_id = ni.id and nitl.nav_item_type = 'NAV_ITEM'
order by ni.order asc;
SQL;

	if ($results = $connection->query($get_nav_query)) {
		$result_array = [];
		while ($row = $results->fetch_assoc()) {
		    $sub_nav_item_array = [];
			$nav_item_array = get_array_from_row($row);
			
			if ($row['type'] == "DROPDOWN") {
                $get_sub_nav_stmt = get_sub_nav_items($connection);
                $get_sub_nav_stmt->bind_param("i", $row['id']);
	            catch_execution_error($get_sub_nav_stmt->execute(), $connection);
				$sub_results = $get_sub_nav_stmt->get_result();
				while ($sub_row = $sub_results->fetch_assoc()) {
					$sub_nav_item_array[] = get_array_from_row($sub_row);
				}
				$sub_results->close();
			}
			
			$nav_item_array['subNavItems'] = $sub_nav_item_array;
			$result_array[] = $nav_item_array;
		}
		print("[[\"SUCCESS\"],");
		print(json_encode($result_array));
		print("]");
		$results->close();
	}
    else {
        $error = $connection->error;
        print("[[\"ERROR\"],[\"$error\"]]");
        exit;
    }
	$connection->commit();
}

function get_array_from_row($row) {
	return array("id" => $row['id'], "text" => $row['text'], "type" => $row['type'], "url" => $row['url'], "sectionId" => $row['section_id']);
}

function get_last_inserted_id($connection) {
	$get_last_inserted_id_query = <<<SQL
select last_insert_id();
SQL;

	if ($results = $connection->query($get_last_inserted_id_query)) {
		while ($row = $results->fetch_assoc()) {
			return $row['last_insert_id()'];
		}
	}
	print("[[\"ERROR\"],[\"No ID inserted\"]]");
    exit;
}

function get_next_order($connection, $table) {
	$get_next_order_query = <<<SQL
select max(`order`) + 1 as next from $table
SQL;

	$next = null;

	if ($results = $connection->query($get_next_order_query)) {
		while ($row = $results->fetch_assoc()) {
			$next = $row['next'];
		}
	}
	
	if ($next == null) {
		$next = 0;
	}
	
	return $next;
}
?>