<?php

namespace bkarsono\asdpasskeylogin\core;

use bkarsono\asdpasskeylogin\controllers\BaseController;
use bkarsono\asdpasskeylogin\models\GeneralModel;

if (!defined('ABSPATH')) exit;

if (!class_exists(Events::class)) {
    class Events extends BaseController
    {
        /**
         * Handle plugin activation event.
         * Creates necessary database tables, registers the site with the API server,
         * saves API keys and URLs, and initializes default options.
         *
         * @return void
         */
        public static  function onActivation()
        {
            if (!current_user_can('activate_plugins')) {
                ASD_P4SSK3Y_asdlog('[ASD onActivation] User have not privileges activated plugins.');
                return;
            }

            $passkeyModel = new GeneralModel("passkey_data");
            $result = $passkeyModel->createTable();
            if ($result) {
                ASD_P4SSK3Y_asdlog('[ASD onActivation] Table created successfully');
            } else {
                ASD_P4SSK3Y_asdlog('[ASD onActivation] Failed to create table');
            }
            $data = [
                'domain' => get_bloginfo('url'),
                'wpinfo' => get_bloginfo('name'),
                'admin_email' => get_bloginfo('admin_email'),
                'plugin_name' => 'ASD Passkey for Wordpress',
                'version' => '1.1.0',
            ];

            $response = wp_remote_post(ASD_P4SSK3Y_API_URL . "/wpregister", [
                'method'    => 'POST',
                'body'      => json_encode($data),
                'headers'   => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . ASD_P4SSK3Y_WEBID,
                ],
                'timeout'   => 60,
            ]);

            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                ASD_P4SSK3Y_asdlog("[ASD onActivation] API Error: $error_message");
                return;
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            ASD_P4SSK3Y_asdlog($data);
            if (isset($data['serverStatus']) && $data['serverStatus'] === 'error') {
                ASD_P4SSK3Y_asdlog("[ASD onActivation] Server Error: " . esc_html($data['serverMessage']));
            } else {
                (new self())->initDefaultOptions();

                add_option('asd_p4ssk3y_key1', $data['key1']);
                add_option('asd_p4ssk3y_key2', $data['key2']);
                add_option('asd_p4ssk3y_api_server', $data['apiserver']);
                add_option('asd_p4ssk3y_eauth_url', $data['eauth_url']);
                add_option('asd_p4ssk3y_fedcm_url', $data['fedcm_url']);
            }
            add_option('asd_passkey_activation_notice', 1);
            flush_rewrite_rules();
            ASD_P4SSK3Y_asdlog('[ASD onActivation] Activation completed.');
        }

        /**
         * Handle plugin deactivation event.
         * Removes activation notice and deletes the passkey-login page if it exists.
         *
         * @return void
         */
        public static function onDeactivation()
        {
            if (!current_user_can('activate_plugins')) {
                return;
            }
            delete_option('asd_passkey_activation_notice');
            $page = get_page_by_path('passkey-login');
            if ($page) {
                wp_delete_post($page->ID, true);
            }
        }
    }
}
