<?php
/* Define prepared statements, and their associated functions */

$delete_youtube_by_id_stmt = null;
function delete_youtube_by_id_stmt($connection) {
	global $delete_youtube_by_id_stmt;
	if (!isset($delete_youtube_by_id_stmt)) {
		$delete_youtube_by_id_query = <<<SQL
delete from section_to_youtube
where id = ?;
SQL;
		$delete_youtube_by_id_stmt = $connection->prepare($delete_youtube_by_id_query);
	}
	
	return $delete_youtube_by_id_stmt;
}

$delete_youtubes_by_section_id_stmt = null;
function delete_youtubes_by_section_id_stmt($connection) {
	global $delete_youtubes_by_section_id_stmt;
	if (!isset($delete_youtubes_by_section_id_stmt)) {
		$delete_youtubes_by_section_id_query = <<<SQL
delete from section_to_youtube
where section_id = ?;
SQL;
		$delete_youtubes_by_section_id_stmt = $connection->prepare($delete_youtubes_by_section_id_query);
	}
	
	return $delete_youtubes_by_section_id_stmt;
}

$delete_image_by_id_stmt = null;
function delete_image_by_id_stmt($connection) {
	global $delete_image_by_id_stmt;
	if (!isset($delete_image_by_id_stmt)) {
		$delete_image_by_id_query = <<<SQL
delete from section_to_image
where id = ?;
SQL;
		$delete_image_by_id_stmt = $connection->prepare($delete_image_by_id_query);
	}
	
	return $delete_image_by_id_stmt;
}

$delete_images_by_section_id_stmt = null;
function delete_images_by_section_id_stmt($connection) {
	global $delete_images_by_section_id_stmt;
	if (!isset($delete_images_by_section_id_stmt)) {
		$delete_images_by_section_id_query = <<<SQL
delete from section_to_image
where section_id = ?;
SQL;
		$delete_images_by_section_id_stmt = $connection->prepare($delete_images_by_section_id_query);
	}
	
	return $delete_images_by_section_id_stmt;
}

$delete_text_by_section_id_stmt = null;
function delete_text_by_section_id_stmt($connection) {
	global $delete_text_by_section_id_stmt;
	if (!isset($delete_text_by_section_id_stmt)) {
		$delete_text_by_section_id_query = <<<SQL
delete from section_to_text
where section_id = ?;
SQL;
		$delete_text_by_section_id_stmt = $connection->prepare($delete_text_by_section_id_query);
	}
	
	return $delete_text_by_section_id_stmt;
}

$delete_button_text_by_section_id_stmt = null;
function delete_button_text_by_section_id_stmt($connection) {
	global $delete_button_text_by_section_id_stmt;
	if (!isset($delete_button_text_by_section_id_stmt)) {
		$delete_button_text_by_section_id_query = <<<SQL
delete from section_to_button_text
where section_id = ?;
SQL;
		$delete_button_text_by_section_id_stmt = $connection->prepare($delete_button_text_by_section_id_query);
	}
	
	return $delete_button_text_by_section_id_stmt;
}

$delete_section_stmt = null;
function delete_section_stmt($connection) {
	global $delete_section_stmt;
	if (!isset($delete_section_stmt)) {
		$delete_section_query = <<<SQL
delete from section
where id = ?;
SQL;
		$delete_section_stmt = $connection->prepare($delete_section_query);
	}
	
	return $delete_section_stmt;
}

$delete_nitl_stmt = null;
function delete_get_nitl_stmt($connection) {
    global $delete_nitl_stmt;
    if (!isset($delete_nitl_stmt)) {
        $delete_nitl_query = <<<SQL
delete from nav_item_to_link
where nav_item_type = ? and nav_item_id = ?
SQL;
        $delete_nitl_stmt = $connection->prepare($delete_nitl_query);
    }
    return $delete_nitl_stmt;
}

$delete_nits_stmt = null;
function delete_get_nits_stmt($connection) {
    global $delete_nits_stmt;
    if (!isset($delete_nits_stmt)) {
        $delete_nits_query = <<<SQL
delete from nav_item_to_section
where nav_item_type = ? and nav_item_id = ?
SQL;
        $delete_nits_stmt = $connection->prepare($delete_nits_query);
    }
    return $delete_nits_stmt;
}

$delete_sni_stmt = null;
function delete_sni_stmt($connection) {
    global $delete_sni_stmt;
    if (!isset($delete_sni_stmt)) {
        $delete_sni_query = <<<SQL
delete from sub_nav_item
where id = ?
SQL;
        $delete_sni_stmt = $connection->prepare($delete_sni_query);
    }
    return $delete_sni_stmt;
}

$delete_ni_stmt = null;
function delete_ni_stmt($connection) {
    global $delete_ni_stmt;
    if (!isset($delete_ni_stmt)) {
        $delete_ni_query = <<<SQL
delete from nav_item
where id = ?
SQL;
        $delete_ni_stmt = $connection->prepare($delete_ni_query);
    }
    return $delete_ni_stmt;
}
?>