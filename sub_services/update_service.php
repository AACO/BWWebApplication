<?php
include '/../queries/update_queries.php';

/* Operations */
if (isset($_GET['save'])) {
	switch ($_GET['save']) {
		case 0:
			save_nav_item($connection);
			break;
		case 1:
			save_nav_order($connection);
			break;
		case 2:
			add_nav_item($connection);
			break;
		case 3:
			add_sub_nav_item($connection);
			break;
		case 4:
			save_yt_order($connection);
			break;
		case 5:
			save_image_order($connection);
			break;
		case 6:
			save_section_order($connection);
			break;
        case 7:
			save_section($connection);
			break;
        case 8:
			save_youtube($connection);
			break;
        case 9:
			save_image($connection);
			break;
        case 10:
			add_section($connection);
			break;
        case 11:
			add_youtube($connection);
			break;
        case 12:
			add_image($connection);
			break;
        default:
            print('[["ERROR"]["Save had an invalid ID"]]');
	}
}

/* Navigation items */

// Add items
function add_section_item($id, $is_sub, $content, $connection) {
	$nav_type = $is_sub ? "SUB_NAV_ITEM" : "NAV_ITEM";
	$insert_or_update_nits_stmt = insert_or_update_nits_stmt($connection);
    $insert_or_update_nits_stmt->bind_param("siss", $nav_type, $id, $content, $content);
    catch_execution_error($insert_or_update_nits_stmt->execute(), $connection);
}

function add_link_item($id, $is_sub, $content, $connection) {
	$nav_type = $is_sub ? "SUB_NAV_ITEM" : "NAV_ITEM";
	$insert_or_update_nitl_stmt = insert_or_update_nitl_stmt($connection);
    $insert_or_update_nitl_stmt->bind_param("siss", $nav_type, $id, $content, $content);
    catch_execution_error($insert_or_update_nitl_stmt->execute(), $connection);
}

function add_nav_item($connection) {
	$nav_title = $_REQUEST['title'];
	$nav_type = $_REQUEST['type'];
	
	$connection->begin_transaction();
	
	insert_nav_item($nav_title, $nav_type, $connection);
	print("[[\"SUCCESS\"],{\"id\": " . get_last_inserted_id($connection) . ", \"text\": \"$nav_title\", \"type\": \"$nav_type\"}]");

	$connection->commit();
}

function insert_nav_item($nav_title, $nav_type, $connection) {
	$next = get_next_order($connection, "nav_item");
    $insert_nav_item_stmt = insert_nav_item_stmt($connection);
    $insert_nav_item_stmt->bind_param("iss", $next, $nav_title, $nav_type);
    catch_execution_error($insert_nav_item_stmt->execute(), $connection);
}

function add_sub_nav_item($connection) {
	$nav_id = $_REQUEST['parentId'];
	$nav_title = $_REQUEST['title'];
	$nav_type = $_REQUEST['type'];
	
	$connection->begin_transaction();
	
	insert_sub_nav_item($nav_id, $nav_title, $nav_type, $connection);
	print("[[\"SUCCESS\"],{\"id\": " . get_last_inserted_id($connection) . ", \"text\": \"$nav_title\", \"type\": \"$nav_type\", \"parentId\": \"$nav_id\"}]");
	
	$connection->commit();
}

function insert_sub_nav_item($nav_id, $nav_title, $nav_type, $connection) {
	$next = get_next_order($connection, "sub_nav_item");
    $insert_sub_nav_item_stmt = insert_sub_nav_item_stmt($connection);
    $insert_sub_nav_item_stmt->bind_param("iiss", $nav_id, $next, $nav_title, $nav_type);
    catch_execution_error($insert_sub_nav_item_stmt->execute(), $connection);
}

// Save items
function save_nav_item($connection) {
	$connection->begin_transaction();
	$is_sub_nav = (bool)$_GET['subnav'];
	$nav_id = $_REQUEST['id'];
	$nav_title = $_REQUEST['title'];
	$nav_type = $_REQUEST['type'];
	$nav_content = $_REQUEST['content'];
	
	if ($nav_type == "SECTION") {
		add_section_item($nav_id, $is_sub_nav, $nav_content, $connection);
		update_nav_item($nav_id, $nav_title, $nav_type, $is_sub_nav, $connection);
		remove_link_item($nav_id, $is_sub_nav, $connection);
		remove_sub_nav_items($nav_id, $connection);
	}
	else if ($nav_type == "LINK") {
		add_link_item($nav_id, $is_sub_nav, $nav_content, $connection);
		update_nav_item($nav_id, $nav_title, $nav_type, $is_sub_nav, $connection);
		remove_section_item($nav_id, $is_sub_nav, $connection);
		remove_sub_nav_items($nav_id, $connection);
	}
	else if (!$is_sub_nav && $nav_type == "DROPDOWN") {
		update_nav_item($nav_id, $nav_title, $nav_type, $is_sub_nav, $connection);
		remove_link_item($nav_id, $is_sub_nav, $connection);
		remove_section_item($nav_id, $is_sub_nav, $connection);
	}

	$connection->commit();
	print('[["SUCCESS"],["Navigation item successfully updated"]]');
}

function update_nav_item($nav_id, $nav_title, $nav_type, $is_sub, $connection) {
    if ($is_sub) {
        $update_stmt = update_sub_nav_item_stmt($connection);
    }
    else {
        $update_stmt = update_nav_item_stmt($connection);
    }
    $update_stmt->bind_param("ssi", $nav_title, $nav_type, $nav_id);
    catch_execution_error($update_stmt->execute(), $connection);
}

// Orderings
function save_nav_order($connection) {
	$connection->begin_transaction();
	$is_sub_nav = (bool)$_GET['subnav'];
	$order_array = $_REQUEST['order'];
	
	for ($i = 0; $i < count($order_array); $i++) {
    	update_nav_order($connection, $is_sub_nav, $order_array[$i], $i);
	}
	
	$connection->commit();
    
    print('[["SUCCESS"],["Navigation order successfully updated"]]');
}

function update_nav_order($connection, $is_sub_nav, $id, $order) {
	if ($is_sub_nav) {
		$update_stmt = update_sub_nav_order_stmt($connection);
	}
	else {
		$update_stmt = update_nav_order_stmt($connection);
	}
	$update_stmt->bind_param("ii", $order, $id);
	catch_execution_error($update_stmt->execute(), $connection);
}

/* Section Items */

// Add items
function add_section($connection) {
	$title = $_REQUEST['title'];
	$type = $_REQUEST['type'];
	
	$connection->begin_transaction();
	
	$next = get_next_order($connection, "section");
	$insert_section_stmt = insert_section_stmt($connection);
	$insert_section_stmt->bind_param("sis", $type, $next, $title);
	catch_execution_error($insert_section_stmt->execute(), $connection);
	
	print("[[\"SUCCESS\"],[\"Added new section: $title\"]]");
	$connection->commit();
}

function add_image($connection) {
	
}

function add_youtube($connection) {
	$channel_name = $_REQUEST['channelName'];
	$channel_url = $_REQUEST['channelUrl'];
	$section_id = $_REQUEST['sectionId'];
	
	$connection->begin_transaction();
	
	$next = get_next_order($connection, "section_to_youtube");
	$insert_section_youtube_stmt = insert_section_youtube_stmt($connection);
	$insert_section_youtube_stmt->bind_param("iiss", $section_id, $next, $channel_name, $channel_url);
	catch_execution_error($insert_section_youtube_stmt->execute(), $connection);
	
	print("[[\"SUCCESS\"],{\"id\": " . get_last_inserted_id($connection) . ", \"sectionId\": $section_id, \"channelName\": \"$channel_name\", \"channelUrl\": \"$channel_url\"}]");
	$connection->commit();
}

// Save items
function save_section($connection) {
	$id = $_REQUEST['id'];
	$title = $_REQUEST['title'];
	$type = $_REQUEST['type'];
	
	$connection->begin_transaction();
	
	if ($type == "TEXT" || $type == "JOIN" || $type == "MODS" || $type == "INTRO") {
		$text = $_REQUEST['text'];
		$insert_update_section_text_stmt = insert_update_section_text_stmt($connection);
		$insert_update_section_text_stmt->bind_param("iss", $id, $text, $text);
		catch_execution_error($insert_update_section_text_stmt->execute(), $connection);
		
		if ($type == "JOIN" || $type == "MODS") {
			$button_text = $_REQUEST['buttonText'];
			$insert_update_section_button_text_stmt = insert_update_section_button_text_stmt($connection);
			$insert_update_section_button_text_stmt->bind_param("iss", $id, $button_text, $button_text);
			catch_execution_error($insert_update_section_button_text_stmt->execute(), $connection);
		}
		
		update_section($connection, $id, $type, $title);
		
		if ($type != "JOIN" &&  $type != "MODS") {
			remove_section_button_text($connection, $id);
		}
		
		remove_section_images($connection, $id);
		remove_section_youtubes($connection, $id);
	}
	else {
		update_section($connection, $id, $type, $title);
		remove_section_text($connection, $id);
		remove_section_button_text($connection, $id);
	}

	$connection->commit();
	
	print('[["SUCCESS"],["Section saved successfully"]]');
}

function update_section($connection, $id, $type, $title) {
	$update_section_stmt = update_section_stmt($connection);
	$update_section_stmt->bind_param("ssi", $type, $title, $id);
	catch_execution_error($update_section_stmt->execute(), $connection);
}

function do_file_move($file) {
	global $screenshot_folder;
	$check = getimagesize($file["tmp_name"]);
	if(!$check) {
		print('[["INLINE"],["File uploaded not an image"]]');
		exit;
	}
	
	$target_file = $_SERVER['DOCUMENT_ROOT'] . $screenshot_folder . basename($file["name"]);
	
	if (file_exists($target_file)) {
		print('[["INLINE"],["Image with same name already exists"]]');
		exit;
	}
	
	if ($file["size"] > 6553600) {
		print('[["INLINE"],["Image size larger than 50MB"]]');
		exit;
	}
	
	move_uploaded_file($file["tmp_name"], $target_file);
}

function save_image($connection) {
	$file = $_FILES["image"];
	$image_id = $_REQUEST['imageId'];
	$title = $_REQUEST['title'];
	$section_id = $_REQUEST['sectionId'];
	$file_path = basename($file["name"]);
	
	$connection->begin_transaction();
	
	if ($image_id > 0) {
		$old_image = get_image($connection, $id);
		if ($file_path != "" && $old_image != $file_path) {
			remove_image_file($old_image);
			do_file_move($file);
			
			$update_section_image_stmt = update_section_image_stmt($connection);
			$update_section_image_stmt->bind_param("ssi", $file_path, $title, $id);
			catch_execution_error($update_section_image_stmt->execute(), $connection);
		}
		else {
			$update_section_image_stmt = update_section_image_stmt($connection);
			$update_section_image_stmt->bind_param("ssi",$old_image, $title, $id);
			catch_execution_error($update_section_image_stmt->execute(), $connection);
		}
		print('[["SUCCESS"],["Image saved successfully"]]');
	}
	else {
		do_file_move($file);
		
		$next = get_next_order($connection, "section_to_image");
		$insert_section_image_stmt = insert_section_image_stmt($connection);
		$insert_section_image_stmt->bind_param("iiss", $section_id, $next,  $file_path, $title);
		catch_execution_error($insert_section_image_stmt->execute(), $connection);
		
		print("[[\"SUCCESS\"],{\"id\": " . get_last_inserted_id($connection) . ", \"sectionId\": $section_id, \"title\": \"$title\", \"filePath\": \"$file_path\"}]");
	}
	
	$connection->commit();
}

function save_youtube($connection) {
	$id = $_REQUEST['id'];
	$channel_name = $_REQUEST['channelName'];
	$channel_url = $_REQUEST['channelUrl'];
	
	$connection->begin_transaction();

	$update_section_youtube_stmt = update_section_youtube_stmt($connection);
	$update_section_youtube_stmt->bind_param("ssi", $channel_name, $channel_url, $id);
	catch_execution_error($update_section_youtube_stmt->execute(), $connection);
	
	$connection->commit();
	
	print('[["SUCCESS"],["Youtube saved successfully"]]');
}

// Orderings
function save_yt_order($connection) {
	$connection->begin_transaction();
	$order_array = $_REQUEST['order'];
	
	for ($i = 0; $i < count($order_array); $i++) {
    	update_yt_order($connection, $order_array[$i], $i);
	}
	
	$connection->commit();
    
    print('[["SUCCESS"],["Youtube order successfully updated"]]');
}

function update_yt_order($connection, $id, $order) {
	$update_stmt = update_yt_order_stmt($connection);
	$update_stmt->bind_param("ii", $order, $id);
	catch_execution_error($update_stmt->execute(), $connection);
}

function save_image_order($connection) {
	$connection->begin_transaction();
	$order_array = $_REQUEST['order'];
	
	for ($i = 0; $i < count($order_array); $i++) {
    	update_image_order($connection, $order_array[$i], $i);
	}
	
	$connection->commit();
    
    print('[["SUCCESS"],["Image order successfully updated"]]');
}

function update_image_order($connection, $id, $order) {
	$update_stmt = update_image_order_stmt($connection);
	$update_stmt->bind_param("ii", $order, $id);
	catch_execution_error($update_stmt->execute(), $connection);
}

function save_section_order($connection) {
	$connection->begin_transaction();
	$order_array = $_REQUEST['order'];
	
	for ($i = 0; $i < count($order_array); $i++) {
    	update_section_order($connection, $order_array[$i], $i);
	}
	
	$connection->commit();
    
    print('[["SUCCESS"],["Section order successfully updated"]]');
}

function update_section_order($connection, $id, $order) {
	$update_stmt = update_section_order_stmt($connection);
	$update_stmt->bind_param("ii", $order, $id);
	catch_execution_error($update_stmt->execute(), $connection);
}
?>