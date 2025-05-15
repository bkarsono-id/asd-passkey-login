<?php

namespace Asd\Core;

use Asd\Controllers\BaseController;
use Asd\Models\GeneralModel;

if (!defined('ABSPATH')) exit;

if (!class_exists(Events::class)) {
    class Events extends BaseController
    {
        public static  function onActivation()
        {
            if (!current_user_can('activate_plugins')) {
                asdlog('[ASD onActivation] User have not privileges activated plugins.');
                return;
            }

            $passkeyModel = new GeneralModel("passkey_data");
            $result = $passkeyModel->createTable();
            if ($result) {
                asdlog('[ASD onActivation] Table created successfully');
            } else {
                asdlog('[ASD onActivation] Failed to create table');
            }


            // $page_check = get_page_by_path('passkey-login');
            // if (!$page_check) {
            //     $page_data = [
            //         'post_title'   => 'ASD Passkey Login',
            //         'post_content' => '',
            //         'post_status'  => 'publish',
            //         'post_type'    => 'page',
            //         'post_name'    => 'passkey-login',
            //     ];
            //     wp_insert_post($page_data);
            // }


            $data = [
                'domain' => get_bloginfo('url'),
                'wpinfo' => get_bloginfo('name'),
                'admin_email' => get_bloginfo('admin_email'),
                'plugin_name' => 'ASD Passkey for Wordpress',
                'version' => '1.0.0',
            ];

            $response = wp_remote_post(ASD_API_URL . "/wpregister", [
                'method'    => 'POST',
                'body'      => json_encode($data),
                'headers'   => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . ASD_WEBID,
                ],
                'timeout'   => 60,
            ]);

            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                asdlog("[ASD onActivation] API Error: $error_message");
                return;
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            asdlog($data);
            if (isset($data['serverStatus']) && $data['serverStatus'] === 'error') {
                asdlog("[ASD onActivation] Server Error: " . esc_html($data['serverMessage']));
            } else {
                (new self())->initDefaultOptions();

                add_option('asd_key1', $data['key1']);
                add_option('asd_key2', $data['key2']);
                add_option('asd_api_server', $data['apiserver']);
                add_option('asd_eauth_url', $data['eauth_url']);
                add_option('asd_fedcm_url', $data['fedcm_url']);
            }
            add_option('asd_passkey_activation_notice', 1);
            asdlog('[ASD onActivation] Activation completed.');
        }


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
