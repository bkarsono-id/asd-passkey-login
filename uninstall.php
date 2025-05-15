<?php
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
	exit();
}
$defaultOptions = [
	'asd_web_id',
	'asd_version',
	'asd_db_version',
	'asd_passwordless_active',
	'asd_membership',
	'asd_admin_login_form_style',
	'asd_admin_password_confirmation',
	'asd_woo_login_form_style',
	'asd_woo_password_confirmation',
	'asd_woo_login_fedcm_form',
	'asd_woo_idp_provider',
	'asd_smtp_host',
	'asd_smtp_port',
	'asd_smtp_user',
	'asd_smtp_password',
	'asd_key1',
	'asd_key2',
	'asd_api_server',
	'asd_eauth_url',
	'asd_fedcm_url'
];

foreach ($defaultOptions as $value) {
	delete_option($value);
}

global $wpdb;
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
// phpcs:disable WordPress.DB.DirectDatabaseQuery.SchemaChange
$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "passkey_data");

// phpcs:enable