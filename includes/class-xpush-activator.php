<?php

class xPush_Activator_Class
{
    public function xpush_activate() {

	  $pluginJsPath = PLUGIN_XPUSH_PATH. 'assets/js/';
	  $rootJs = ABSPATH . 'firebase-messaging-sw.js';
	  $sourceJs = $pluginJsPath . 'firebase-messaging-sw.js';

	  if (!file_exists($sourceJs)){
		$sourceJs = 'https://3xpush.com/firebase-messaging-sw.js';
	  }

	  copy($sourceJs, $rootJs);

	  $this->xpush_create_table();

	  update_option('xpush_active', 1);
	}

    private function xpush_create_table(){
	  global $wpdb;
	  require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	  $table_name = $wpdb->get_blog_prefix() . 'xpush_dispatch';

	  $sql = "CREATE TABLE {$table_name} (
	id  INT(10) unsigned NOT NULL auto_increment,
	p_date DATETIME NOT NULL default '0000-00-00 00:00:00',
	p_status INT(1) NOT NULL default 1,
	p_recipients INT(8) NOT NULL default 1,
	p_content longtext NOT NULL default '',
	PRIMARY KEY (id),
	KEY p_status (p_status)
	)
	DEFAULT CHARACTER SET $wpdb->charset COLLATE $wpdb->collate;";
	  dbDelta($sql);
	}

}
