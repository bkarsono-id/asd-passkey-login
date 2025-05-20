<?php
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
	wp_die(__('You do not have sufficient permissions to access this page.'));
}
if (!is_super_admin() || !current_user_can('administrator')) {
	wp_die(__('You do not have sufficient permissions to access this page.'));
}
$defaultOptions = [
	'asd_p4ssk3y_web_id',
	'asd_p4ssk3y_version',
	'asd_p4ssk3y_db_version',
	'asd_p4ssk3y_passwordless_active',
	'asd_p4ssk3y_membership',
	'asd_p4ssk3y_admin_login_form_style',
	'asd_p4ssk3y_admin_password_confirmation',
	'asd_p4ssk3y_woo_login_form_style',
	'asd_p4ssk3y_woo_password_confirmation',
	'asd_p4ssk3y_woo_login_fedcm_form',
	'asd_p4ssk3y_woo_idp_provider',
	'asd_p4ssk3y_smtp_host',
	'asd_p4ssk3y_smtp_port',
	'asd_p4ssk3y_smtp_user',
	'asd_p4ssk3y_smtp_password',
	'asd_p4ssk3y_key1',
	'asd_p4ssk3y_key2',
	'asd_p4ssk3y_api_server',
	'asd_p4ssk3y_eauth_url',
	'asd_p4ssk3y_fedcm_url'
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