<?php

namespace bkarsono\asdpasskeylogin\controllers;

if (!defined('ABSPATH')) exit;

if (!class_exists(PasskeySettings::class)) {
    class PasskeySettings extends BaseController
    {
        private $settings_group = 'asd_p4ssk3y_passwordless_settings_group';
        private $options = [
            'asd_p4ssk3y_admin_login_form_style',
            'asd_p4ssk3y_admin_password_confirmation',
            'asd_p4ssk3y_woo_login_form_style',
            'asd_p4ssk3y_woo_password_confirmation',
            'asd_p4ssk3y_woo_login_fedcm_form',
            'asd_p4ssk3y_woo_idp_provider',
            'asd_p4ssk3y_google_client_id'
        ];
        private $smtp_group = 'asd_p4ssk3y_passwordless_smtp_group';
        private $smtp_options = [
            'asd_p4ssk3y_smtp_host',
            'asd_p4ssk3y_smtp_port',
            'asd_p4ssk3y_smtp_user',
            'asd_p4ssk3y_smtp_password'
        ];

        private $webpush_group = 'asd_p4ssk3y_push_notification_group';
        private $webpush_options = [
            'asd_p4ssk3y_push_notification',
            'asd_p4ssk3y_webpush_public_key',
        ];

        public function __construct()
        {
            add_action('admin_init', [$this, 'registerSettings']);
            add_action('wp_ajax_asd_passkey_settings', [$this, 'handlePasskeySettings']);
          
            add_action('wp_ajax_asd_passkey_smtp_settings', [$this, 'handlePasskeySMTPSettings']);
            add_action('wp_ajax_asd_passkey_smtp_test', [$this, 'handlePasskeySMTPTesting']);

            add_action('wp_ajax_asd_push_notification_settings', [$this, 'handlePushNotificationSettings']);
            add_action('wp_ajax_asd_push_notification_publickey', [$this, 'handlePushNotificationPublicKey']);
            clean_notices_admin("asd-passkey-settings");
        }

        public function index()
        {
            $this->initDefaultOptions();
            $data = [
                "smtpread" => 'readonly',
                "wooaccount" => class_exists('WooCommerce')
            ];
            ASD_P4SSK3Y_view("asd-passkey-settings", $data);
        }
        public function registerSettings($hook)
        {
            // phpcs:disable WordPress.Security.NonceVerification.Recommended -- No nonce verification needed.
            if (!is_admin() || !isset($_GET['page']) || $_GET['page'] !== 'asd-passkey-settings') {
                return;
            }

            foreach ($this->options as $option) {
                register_setting($this->settings_group, $option, [
                    'sanitize_callback' => [$this, 'sanitize_fields']
                ]);
            }
            foreach ($this->smtp_options as $option) {
                register_setting($this->smtp_group, $option, [
                    'sanitize_callback' => [$this, 'sanitize_fields']
                ]);
            }
            foreach ($this->webpush_options as $option) {
                register_setting($this->webpush_group, $option, [
                    'sanitize_callback' => [$this, 'sanitize_fields']
                ]);
            }
            // phpcs:enable
        }

        public function handlePasskeySettings()
        {
            if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                wp_send_json_error(['message' => 'Invalid request method']);
                exit;
            }

            $_wpnonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
            if (!wp_verify_nonce($_wpnonce, 'asd_passkey_settings_nonce')) {
                wp_send_json_error(['message' => 'Nonce verification failed']);
                exit;
            }
            $fedcmactivated = isset($_POST['asd_p4ssk3y_woo_login_fedcm_form']) ? sanitize_text_field(wp_unslash($_POST['asd_p4ssk3y_woo_login_fedcm_form'])) : '';
            $fedcmclientid = isset($_POST['asd_p4ssk3y_google_client_id']) ? sanitize_text_field(wp_unslash($_POST['asd_p4ssk3y_google_client_id'])) : '';
            $fedcmidp = isset($_POST['asd_p4ssk3y_woo_idp_provider']) ? sanitize_text_field(wp_unslash($_POST['asd_p4ssk3y_woo_idp_provider'])) : '';
            if ($fedcmactivated !== "disabled" && $fedcmidp === "google" && $fedcmclientid === "") {
                wp_send_json_error(['message' => 'Saved failed, please insert your google client id or disabled fedcm login.']);
                exit;
            }
            $updated = [];
            foreach ($this->options as $option) {
                if (isset($_POST[$option])) {
                    $sanitized_value = sanitize_text_field(wp_unslash($_POST[$option]));
                    update_option($option, $sanitized_value);
                    $updated[$option] = $sanitized_value;
                }
            }

            wp_send_json_success(['message' => 'Settings saved successfully.']);
        }

       

        /* SMTP Setting */

        public function sanitize_fields($value)
        {
            if (is_string($value)) {
                return sanitize_text_field($value);
            }

            if (is_array($value)) {
                return array_map('sanitize_text_field', $value);
            }

            return $value;
        }
        

        public function handlePushNotificationPublicKey()
        {
            if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                wp_send_json_error(['message' => 'Invalid request method']);
                exit;
            }

            $_wpnonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
            if (!wp_verify_nonce($_wpnonce, 'asd_webpush_publickey_nonce')) {
                wp_send_json_error(['message' => 'Nonce verification failed']);
                exit;
            }

            $data = [
                'domain' => get_bloginfo('url'),
                'platform' => "wordpress",
                'action' => 'create-publickey',
                'filter' => null,
            ];
            $response = wp_remote_post(ASD_P4SSK3Y_WEBPUSH_URL . "/clientQuery", [
                'method'    => 'POST',
                'body'      => json_encode($data),
                'headers'   => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . get_option('asd_p4ssk3y_key1'),
                ],
                'timeout'   => 30,
            ]);
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                wp_send_json_error(['message' => 'Failed to connect to the API', 'error' => $error_message]);
                exit;
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            $result = $data['serverData'];
            if (json_last_error() !== JSON_ERROR_NONE) {
                wp_send_json_error(['message' => 'Failed to decode API response']);
                exit;
            }

            if (isset($data['serverStatus']) && $data['serverStatus'] === 'error') {
                ASD_P4SSK3Y_asdlog("Create Public Key Error: " . esc_html($data['serverMessage']));
                wp_send_json_error(['message' => 'API Error', 'details' => esc_html($data['serverMessage'])]);
                exit;
            }
            if (!isset($data['serverData']['public_key'])) {
                wp_send_json_error(['message' => 'Invalid Public Key data from API response']);
                exit;
            }
            update_option("asd_p4ssk3y_webpush_public_key", $result['public_key']);

            wp_send_json_success(['message' => 'Create Public Key Success.']);
        }
    }
}
