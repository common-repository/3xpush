<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->get_blog_prefix()."xpush_dispatch");
unlink(ABSPATH . 'firebase-messaging-sw.js');
delete_option('xpush_active');
delete_option('xpush_api_key');
delete_option('xpush_settings');
delete_option('xpush_tuned');