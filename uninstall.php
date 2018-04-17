<?php
/**
 * Delete all transients when plugin is removed
 *
 * @package Apison
 */

defined('WP_UNINSTALL_PLUGIN') || exit;

global $wpdb;

$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_apison_%';");
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_apison_%';");

delete_option('apison_endpoints');
