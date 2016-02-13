<?php
/* Define prepared statements, and their associated functions */

/* Nav Items */

/* updates */
$update_nav_order_stmt = null;
function update_nav_order_stmt($connection) {
	global $update_nav_order_stmt;
	if (!isset($update_nav_order_stmt)) {
		$update_nav_order_query = <<<SQL
update nav_item
set `order` = ?
where id = ?;
SQL;
		$update_nav_order_stmt = $connection->prepare($update_nav_order_query);
	}
	return $update_nav_order_stmt;
}

$update_sub_nav_order_stmt = null;
function update_sub_nav_order_stmt($connection) {
	global $update_sub_nav_order_stmt;
	if (!isset($update_sub_nav_order_stmt)) {
		$update_sub_nav_order_query = <<<SQL
update sub_nav_item
set `order` = ?
where id = ?;
SQL;

		$update_sub_nav_order_stmt = $connection->prepare($update_sub_nav_order_query);
	}
	return $update_sub_nav_order_stmt;
}

$update_nav_item_stmt = null;
function update_nav_item_stmt($connection) {
    global $update_nav_item_stmt;
    if (!isset($update_nav_item_stmt)) {
        $update_nav_item_query = <<<SQL
update nav_item
set `text` = ?, `type` = ?
where id = ?
SQL;
        $update_nav_item_stmt = $connection->prepare($update_nav_item_query);
    }
    return $update_nav_item_stmt;
}

$update_sub_nav_item_stmt = null;
function update_sub_nav_item_stmt($connection) {
    global $update_sub_nav_item_stmt;
    if (!isset($update_sub_nav_item_stmt)) {
        $update_sub_nav_item_query = <<<SQL
update sub_nav_item
set `text` = ?, `type` = ?
where id = ?
SQL;
        $update_sub_nav_item_stmt = $connection->prepare($update_sub_nav_item_query);
    }
    return $update_sub_nav_item_stmt;
}

/* inserts */
$insert_sub_nav_item_stmt = null;
function insert_sub_nav_item_stmt($connection) {
    global $insert_sub_nav_item_stmt;
    if (!isset($insert_sub_nav_item_stmt)) {
        $insert_sub_nav_item_query = <<<SQL
insert into sub_nav_item (`nav_item_id`, `order`, `text`, `type`)
values (?, ?, ?, ?)
SQL;
        $insert_sub_nav_item_stmt = $connection->prepare($insert_sub_nav_item_query);
    }
    return $insert_sub_nav_item_stmt;
}

$insert_nav_item_stmt = null;
function insert_nav_item_stmt($connection) {
    global $insert_nav_item_stmt;
    if (!isset($insert_nav_item_stmt)) {
        $insert_nav_item_query = <<<SQL
insert into nav_item (`order`, `text`, `type`)
values (?, ?, ?)
SQL;
        $insert_nav_item_stmt = $connection->prepare($insert_nav_item_query);
    }
    return $insert_nav_item_stmt;
}

/* insert OR update */
$insert_or_update_nits_stmt = null;
function insert_or_update_nits_stmt($connection) {
    global $insert_or_update_nits_stmt;
    if (!isset($insert_or_update_nits_stmt)) {
        $insert_or_update_nits_query = <<<SQL
insert into nav_item_to_section (`nav_item_type`, `nav_item_id`, `section_id`) values (?, ?, ?)
on duplicate key update `section_id` = ?
SQL;
        $insert_or_update_nits_stmt = $connection->prepare($insert_or_update_nits_query);
    }
    return $insert_or_update_nits_stmt;
}

$insert_or_update_nitl_stmt = null;
function insert_or_update_nitl_stmt($connection) {
    global $insert_or_update_nitl_stmt;
    if (!isset($insert_or_update_nitl_stmt)) {
        $insert_or_update_nitl_query = <<<SQL
insert into nav_item_to_link (`nav_item_type`, `nav_item_id`, `url`) values (?, ?, ?)
on duplicate key update `url` = ?
SQL;
        $insert_or_update_nitl_stmt = $connection->prepare($insert_or_update_nitl_query);
    }
    return $insert_or_update_nitl_stmt;
}

/* Sections */

/* updates */
$update_yt_order_stmt = null;
function update_yt_order_stmt($connection) {
	global $update_yt_order_stmt;
	if (!isset($update_yt_order_stmt)) {
		$update_yt_order_query = <<<SQL
update section_to_youtube
set `order` = ?
where id = ?;
SQL;
		$update_yt_order_stmt = $connection->prepare($update_yt_order_query);
	}
	return $update_yt_order_stmt;
}

$update_section_order_stmt = null;
function update_section_order_stmt($connection) {
	global $update_section_order_stmt;
	if (!isset($update_section_order_stmt)) {
		$update_section_order_query = <<<SQL
update section
set `order` = ?
where id = ?;
SQL;
		$update_section_order_stmt = $connection->prepare($update_section_order_query);
	}
	return $update_section_order_stmt;
}

$update_image_order_stmt = null;
function update_image_order_stmt($connection) {
	global $update_image_order_stmt;
	if (!isset($update_image_order_stmt)) {
		$update_image_order_query = <<<SQL
update section_to_image
set `order` = ?
where id = ?;
SQL;
		$update_image_order_stmt = $connection->prepare($update_image_order_query);
	}
	return $update_image_order_stmt;
}

$update_section_stmt = null;
function update_section_stmt($connection) {
	global $update_section_stmt;
	if (!isset($update_section_stmt)) {
		$update_section_query = <<<SQL
update section
set `type` = ?, `title` = ?
where `id` = ?;
SQL;
		$update_section_stmt = $connection->prepare($update_section_query);
	}
	return $update_section_stmt;
}

$update_section_youtube_stmt = null;
function update_section_youtube_stmt($connection) {
	global $update_section_youtube_stmt;
	if (!isset($update_section_youtube_stmt)) {
		$update_section_youtube_query = <<<SQL
update section_to_youtube
set `channel_name` = ?, `channel_url` = ?
where `id` = ?;
SQL;
		$update_section_youtube_stmt = $connection->prepare($update_section_youtube_query);
	}
	return $update_section_youtube_stmt;
}

$update_section_image_stmt = null;
function update_section_image_stmt($connection) {
	global $update_section_image_stmt;
	if (!isset($update_section_image_stmt)) {
		$update_section_image_query = <<<SQL
update section_to_image
set `file_path` = ?, `title` = ?
where `id` = ?;
SQL;
		$update_section_image_stmt = $connection->prepare($update_section_image_query);
	}
	return $update_section_image_stmt;
}

/* inserts */
$insert_section_stmt = null;
function insert_section_stmt($connection) {
	global $insert_section_stmt;
	if (!isset($insert_section_stmt)) {
		$insert_section_query = <<<SQL
insert into section (`type`, `order`, `title`) values (?,?,?);
SQL;
		$insert_section_stmt = $connection->prepare($insert_section_query);
	}
	return $insert_section_stmt;
}

$insert_section_youtube_stmt = null;
function insert_section_youtube_stmt($connection) {
	global $insert_section_youtube_stmt;
	if (!isset($insert_section_youtube_stmt)) {
		$insert_section_youtube_query = <<<SQL
insert into section_to_youtube (`section_id`, `order`, `channel_name`, `channel_url`) values (?,?,?,?);
SQL;
		$insert_section_youtube_stmt = $connection->prepare($insert_section_youtube_query);
	}
	return $insert_section_youtube_stmt;
}

$insert_section_image_stmt = null;
function insert_section_image_stmt($connection) {
	global $insert_section_image_stmt;
	if (!isset($insert_section_image_stmt)) {
		$insert_section_image_query = <<<SQL
insert into section_to_image (`section_id`, `order`, `file_path`, `title`) values (?,?,?,?);
SQL;
		$insert_section_image_stmt = $connection->prepare($insert_section_image_query);
	}
	return $insert_section_image_stmt;
}

/* insert OR updates */
$insert_update_section_text_stmt = null;
function insert_update_section_text_stmt($connection) {
	global $insert_update_section_text_stmt;
	if (!isset($insert_update_section_text_stmt)) {
		$insert_update_section_text_query = <<<SQL
insert into section_to_text (`section_id`, `text`) values (?,?)
on duplicate key update `text` = ?;
SQL;
		$insert_update_section_text_stmt = $connection->prepare($insert_update_section_text_query);
	}
	return $insert_update_section_text_stmt;
}

$insert_update_section_button_text_stmt = null;
function insert_update_section_button_text_stmt($connection) {
	global $insert_update_section_button_text_stmt;
	if (!isset($insert_update_section_button_text_stmt)) {
		$insert_update_section_button_text_query = <<<SQL
insert into section_to_button_text (`section_id`, `button_text`) values (?,?)
on duplicate key update `button_text` = ?;
SQL;
		$insert_update_section_button_text_stmt = $connection->prepare($insert_update_section_button_text_query);
	}
	return $insert_update_section_button_text_stmt;
}
?>