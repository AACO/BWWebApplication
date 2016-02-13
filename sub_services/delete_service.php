<?php
include '/../queries/delete_queries.php';

/* Operations */
if (isset($_GET['del'])) {
	switch ($_GET['del']) {
		case 0:
			del_nav_item($connection);
			break;
		case 1:
			del_sub_nav_item($connection);
			break;
		case 2:
			del_youtube($connection);
			break;
		case 3:
			del_image($connection);
			break;
		case 4:
			del_section($connection);
			break;
        default:
            print('[["ERROR"]["Delete had an invalid ID"]]');
	}
}

function remove_section_youtubes($connection, $id) {
	$delete_youtubes_by_section_id_stmt = delete_youtubes_by_section_id_stmt($connection);
	$delete_youtubes_by_section_id_stmt->bind_param("i", $id);
	catch_execution_error($delete_youtubes_by_section_id_stmt->execute(), $connection);
}

function remove_youtube($connection, $id) {
	$delete_youtube_by_id_stmt = delete_youtube_by_id_stmt($connection);
	$delete_youtube_by_id_stmt->bind_param("i", $id);
	catch_execution_error($delete_youtube_by_id_stmt->execute(), $connection);
}

function del_youtube($connection) {
	$id = $_REQUEST['id'];
    
	$connection->begin_transaction();
	remove_youtube($connection, $id);
	$connection->commit();
    
	print('[["SUCCESS"],["Successfully removed Youtube channel"]]');
}

function remove_section_images($connection, $id) {
	$images = get_section_images($connection, $id);
	
	foreach ($images as $image) {
		remove_image_file($image['filePath']);
	}

	$delete_images_by_section_id_stmt = delete_images_by_section_id_stmt($connection);
	$delete_images_by_section_id_stmt->bind_param("i", $id);
	catch_execution_error($delete_images_by_section_id_stmt->execute(), $connection);
}

function remove_image($connection, $id) {
	remove_image_file(get_image($connection, $id));
	
	$delete_image_by_id_stmt = delete_image_by_id_stmt($connection);
	$delete_image_by_id_stmt->bind_param("i", $id);
	catch_execution_error($delete_image_by_id_stmt->execute(), $connection);
}

function remove_image_file($file_path) {
	global $screenshot_folder;
	$file_to_remove = $_SERVER['DOCUMENT_ROOT'] . $screenshot_folder . $file_path;
	
	try {
		unlink($file_to_remove);
	}
	catch (Exception $e) {
		error_log("Couldn't delete file: $file_to_remove");
	}
}

function del_image($connection) {
	$id = $_REQUEST['id'];
    
	$connection->begin_transaction();
	remove_image($connection, $id);
	$connection->commit();
    
	print('[["SUCCESS"],["Successfully removed image"]]');
}

function remove_section_text($connection, $id) {
	$delete_text_by_section_id_stmt = delete_text_by_section_id_stmt($connection);
	$delete_text_by_section_id_stmt->bind_param("i", $id);
	catch_execution_error($delete_text_by_section_id_stmt->execute(), $connection);
}

function remove_section_button_text($connection, $id) {
	$delete_button_text_by_section_id_stmt = delete_button_text_by_section_id_stmt($connection);
	$delete_button_text_by_section_id_stmt->bind_param("i", $id);
	catch_execution_error($delete_button_text_by_section_id_stmt->execute(), $connection);
}

function del_section($connection) {
	$id = $_REQUEST['id'];
	
    $connection->begin_transaction();
	if (is_section_referenced($connection, $id)) {
		print('[["ERROR"],["Can not remove section, section is referenced in a Navigation Item"]]');
		exit;
	}
	else {
		remove_section_text($connection, $id);
		remove_section_button_text($connection, $id);
		remove_section_images($connection, $id);
		remove_section_youtubes($connection, $id);
		
		$delete_section_stmt = delete_section_stmt($connection);
		$delete_section_stmt->bind_param("i", $id);
		catch_execution_error($delete_section_stmt->execute(), $connection);
		$connection->commit();
        
		print('[["SUCCESS"],["Successfully removed section"]]');
	}
}

function remove_link_item($id, $is_sub, $connection) {
	$nav_filter = $is_sub ? "SUB_NAV_ITEM" : "NAV_ITEM";
    $delete_nitl_stmt = delete_get_nitl_stmt($connection);
    $delete_nitl_stmt->bind_param("si", $nav_filter, $id);
	catch_execution_error($delete_nitl_stmt->execute(), $connection);
}

function remove_section_item($id, $is_sub, $connection) {
	$nav_filter = $is_sub ? "SUB_NAV_ITEM" : "NAV_ITEM";
    $delete_nits_stmt = delete_get_nits_stmt($connection);
    $delete_nits_stmt->bind_param("si", $nav_filter, $id);
	catch_execution_error($delete_nits_stmt->execute(), $connection);
}

function remove_sub_nav_items($id, $connection) {
    $sub_nav_stmt = get_sub_nav_items($connection);
    if ($sub_results = $sub_nav_stmt->get_result()) {
        while ($sub_row = $sub_results->fetch_assoc()) {
            remove_link_item($sub_row['id'], true, $connection);
            remove_section_item($sub_row['id'], true, $connection);
            remove_sub_nav_item($sub_row['id'], $connection);
        }
        $sub_results->close();
    }
}

function remove_sub_nav_item($id, $connection) {
	$delete_sni_stmt = delete_sni_stmt($connection);
    $delete_sni_stmt->bind_param("i", $id);
    catch_execution_error($delete_sni_stmt->execute(), $connection);
}

function remove_nav_item($id, $connection) {
	$delete_ni_stmt = delete_ni_stmt($connection);
    $delete_ni_stmt->bind_param("i", $id);
    catch_execution_error($delete_ni_stmt->execute(), $connection);
}

function del_nav_item($connection) {
    $connection->begin_transaction();
    
    $id = $_REQUEST['id'];
    remove_link_item($id, false, $connection);
    remove_section_item($id, false, $connection);
    remove_sub_nav_items($id, $connection);
    remove_nav_item($id, $connection);
    
    $connection->commit();
	
	print('[["SUCCESS"],["Successfully removed navigation item"]]');
}

function del_sub_nav_item($connection) {
    $connection->begin_transaction();
    
    $id = $_REQUEST['id'];
    remove_link_item($id, true, $connection);
    remove_section_item($id, true, $connection);
    remove_sub_nav_item($id, $connection);
    
    $connection->commit();
	
	print('[["SUCCESS"],["Successfully removed sub navigation item"]]');
}
?>