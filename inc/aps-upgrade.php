<?php if (!defined('APS_VER')) exit('restricted access');
	
	// create tax meta table if not exist
	function aps_create_term_meta_table() {
		global $wpdb;
		
		$table_name = $wpdb->prefix .'termmeta';
		$charset_collate = $wpdb->get_charset_collate();
		
		// create term meta database table
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			`meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`term_id` bigint(20) unsigned DEFAULT '0',
			`meta_key` varchar(255),
			`meta_value` longtext,
			INDEX term_id (`term_id`),
			INDEX meta_key (`meta_key`)
		) $charset_collate;";
		
		require_once(ABSPATH .'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	