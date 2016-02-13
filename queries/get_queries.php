<?php
/* Define prepared statements, and their associated functions */

$get_sub_nav_stmt = null;
function get_sub_nav_items($connection) {
	global $get_sub_nav_stmt;
	if (!isset($get_sub_nav_stmt)) {
		$get_sub_nav_query = <<<SQL
select sni.id, sni.type, sni.text, nitl.url, nits.section_id
from sub_nav_item sni
left join nav_item_to_section nits on nits.nav_item_id = sni.id and nits.nav_item_type = 'SUB_NAV_ITEM'
left join nav_item_to_link nitl on nitl.nav_item_id = sni.id and nitl.nav_item_type = 'SUB_NAV_ITEM'
where sni.nav_item_id = ?
order by sni.order asc;
SQL;

		$get_sub_nav_stmt = $connection->prepare($get_sub_nav_query);
	}
	
	return $get_sub_nav_stmt;
}

$get_section_text_stmt = null;
function get_section_text_stmt($connection) {
	global $get_section_text_stmt;
	if (!isset($get_section_text_stmt)) {
		$get_section_text_query = <<<SQL
select `text`
from section_to_text
where section_id = ?;
SQL;
		$get_section_text_stmt = $connection->prepare($get_section_text_query);
	}
	
	return $get_section_text_stmt;
}

$get_section_button_text_stmt = null;
function get_section_button_text_stmt($connection) {
	global $get_section_button_text_stmt;
	if (!isset($get_section_button_text_stmt)) {
		$get_section_button_text_query = <<<SQL
select `button_text`
from section_to_button_text
where section_id = ?;
SQL;
		$get_section_button_text_stmt = $connection->prepare($get_section_button_text_query);
	}
	
	return $get_section_button_text_stmt;
}

$get_section_images_stmt = null;
function get_section_images_stmt($connection) {
	global $get_section_images_stmt;
	if (!isset($get_section_images_stmt)) {
		$get_section_images_query = <<<SQL
select `id`, `file_path`, `title`
from section_to_image
where section_id = ?
order by `order`;
SQL;
		$get_section_images_stmt = $connection->prepare($get_section_images_query);
	}
	
	return $get_section_images_stmt;
}

$get_image_stmt = null;
function get_image_stmt($connection) {
	global $get_image_stmt;
	if (!isset($get_image_stmt)) {
		$get_image_query = <<<SQL
select `file_path`
from section_to_image
where id = ?;
SQL;
		$get_image_stmt = $connection->prepare($get_image_query);
	}
	
	return $get_image_stmt;
}

$get_section_youtubes_stmt = null;
function get_section_youtubes_stmt($connection) {
	global $get_section_youtubes_stmt;
	if (!isset($get_section_youtubes_stmt)) {
		$get_section_youtubes_query = <<<SQL
select `id`, `channel_name`, `channel_url`
from section_to_youtube
where section_id = ?
order by `order`;
SQL;
		$get_section_youtubes_stmt = $connection->prepare($get_section_youtubes_query);
	}
	
	return $get_section_youtubes_stmt;
}

$section_count_stmt = null;
function section_count_stmt($connection) {
  global $section_count_stmt;
	if (!isset($section_count_stmt)) {
		$section_count_query = <<<SQL
select count(id)
from nav_item_to_section
where section_id = ?;
SQL;
		$section_count_stmt = $connection->prepare($section_count_query);
	}
	
	return $section_count_stmt;
}
?>