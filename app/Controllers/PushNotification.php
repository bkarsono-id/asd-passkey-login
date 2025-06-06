<?php

namespace bkarsono\asdpasskeylogin\controllers;

if (!defined('ABSPATH')) exit;
if (!class_exists(PushNotification::class)) {
    class PushNotification extends BaseController
    {
        /**
         * PushNotification constructor.
         * Registers AJAX actions for saving and removing push notification subscribers.
         *
         * @return void
         */
        public function __construct()
        {
            add_action('wp_ajax_asd_save_subscriber', [$this, 'handleSubcriber']);
            add_action('wp_ajax_nopriv_asd_save_subscriber', [$this, 'handleSubcriber']);
            add_action('wp_ajax_asd_save_unsubscriber', [$this, 'handleUnSubcriber']);
            add_action('wp_ajax_nopriv_asd_save_unsubscriber', [$this, 'handleUnSubcriber']);
        }

        /**
         * Handles the AJAX request to save a push notification subscriber.
         * Validates the request, verifies the nonce, sends subscription data to the API, and returns a JSON response.
         *
         * @return void
         */
        public function handleSubcriber()
        {
            if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                wp_send_json_error(['message' => 'Invalid request method']);
                exit;
            }

            $_wpnonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
            if (!wp_verify_nonce($_wpnonce, 'asd_save_subscriber')) {
                wp_send_json_error(['message' => 'Nonce verification failed']);
                exit;
            }
            $subscription = isset($_POST['subcription']) ? sanitize_text_field(wp_unslash($_POST['subcription'])) : '';
            if (!isset($subscription) || empty($subscription)) {
                wp_send_json_error(['message' => 'Subcription data not found.']);
                exit;
            }
            $data = [
                'domain' => get_bloginfo('url'),
                'platform' => "wordpress",
                'action' => 'save-subcriber',
                'filter' => null,
                'data' => $subscription
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
                ASD_P4SSK3Y_asdlog($error_message);
                wp_send_json_error(['message' => 'Failed to connect to the API', 'error' => $error_message]);
                exit;
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                wp_send_json_error(['message' => 'Failed to decode API response']);
                exit;
            }

            if (isset($data['serverStatus']) && $data['serverStatus'] === 'error') {
                ASD_P4SSK3Y_asdlog("Create Public Key Error: " . esc_html($data['serverMessage']));
                wp_send_json_error(['message' => esc_html($data['serverMessage'])]);
                exit;
            }
            wp_send_json_success(['message' => 'Subcription Success.']);
        }

        /**
         * Handles the AJAX request to remove a push notification subscriber.
         * Validates the request, verifies the nonce, sends unsubscription data to the API, and returns a JSON response.
         *
         * @return void
         */
        public function handleUnSubcriber()
        {
            if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                wp_send_json_error(['message' => 'Invalid request method']);
                exit;
            }
            $_wpnonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
            if (!wp_verify_nonce($_wpnonce, 'asd_save_subscriber')) {
                wp_send_json_error(['message' => 'Nonce verification failed.']);
                exit;
            }
            $subscription = isset($_POST['subcription']) ? sanitize_text_field(wp_unslash($_POST['subcription'])) : '';
            if (!isset($subscription) || empty($subscription)) {
                wp_send_json_error(['message' => 'Subcription data not found.']);
                exit;
            }
            $data = [
                'domain' => get_bloginfo('url'),
                'platform' => "wordpress",
                'action' => 'save-unsubcriber',
                'filter' => null,
                'data' => $subscription
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
                wp_send_json_error(['message' => 'Failed to connect to the API.', 'error' => $error_message]);
                exit;
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                wp_send_json_error(['message' => 'Failed to decode API response.']);
                exit;
            }

            if (isset($data['serverStatus']) && $data['serverStatus'] === 'error') {
                ASD_P4SSK3Y_asdlog("Create Public Key Error: " . esc_html($data['serverMessage']));
                wp_send_json_error(['message' => esc_html($data['serverMessage'])]);
                exit;
            }
            wp_send_json_success(['message' => 'Subcription Success.']);
        }
    }
}
