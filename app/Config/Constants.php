<?php
if (!defined('ABSPATH')) exit;
/**
 * Defines core plugin constants for versioning, database version, plugin name, API URLs, and Web ID.
 * These constants are used throughout the plugin for configuration and integration.
 */
defined('ASD_P4SSK3Y_VERSION') || define('ASD_P4SSK3Y_VERSION', '1.0.0');
defined('ASD_P4SSK3Y_DB_VERSION') || define('ASD_P4SSK3Y_DB_VERSION', '1.0.0');
defined('ASD_P4SSK3Y_PLUGIN_NAME') || define('ASD_P4SSK3Y_PLUGIN_NAME', 'asd-passkey-login');
defined('ASD_P4SSK3Y_API_URL') || define('ASD_P4SSK3Y_API_URL', 'https://api.alciasolusidigital.com');
defined('ASD_P4SSK3Y_WEBPUSH_URL') || define('ASD_P4SSK3Y_WEBPUSH_URL', 'https://push.alciasolusidigital.com');
defined('ASD_P4SSK3Y_WEBID') || define('ASD_P4SSK3Y_WEBID', ASD_P4SSK3Y_webid());
