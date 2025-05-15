<?php

namespace Asd\Controllers;

use Asd\Models\GeneralModel;
use Asd\Classes\JwtToken;

if (!defined('ABSPATH')) exit;
if (!class_exists(CreatePasskeyWoocommerce::class)) {
    class CreatePasskeyWoocommerce extends BaseController
    {
        public function __construct()
        {
            add_action('wp_ajax_asd_woo_passkey_register', [$this, 'handleRegister']);
            add_action('wp_ajax_asd_woo_passkey_flagging', [$this, 'handleFlagging']);
            add_filter('woocommerce_account_menu_items', [$this, 'asdAddEegisterPasskeyMenu']);
            add_action('init', [$this, 'asdRegisterPasskeyEndpoint']);
            add_action('woocommerce_account_register-passkey_endpoint', [$this, 'asdRegisterPasskeyContent']);
        }

        public function handleRegister()
        {
            if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                wp_send_json_error(['message' => 'Invalid request method']);
                exit;
            }

            $useremail  = isset($_POST['useremail']) ? sanitize_text_field(wp_unslash($_POST['useremail'])) : '';
            $password  = isset($_POST['password']) ? sanitize_text_field(wp_unslash($_POST['password'])) : '';
            $displayName  = isset($_POST['displayName']) ? sanitize_text_field(wp_unslash($_POST['displayName'])) : '';

            $_wpnonce  = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
            if (!isset($_wpnonce) || !wp_verify_nonce($_wpnonce, 'asd_nonce_passkey_register')) {
                wp_send_json_error(['message' => 'Nonce verification failed']);
                exit;
            }
            $setting_using_password = is_setting_valid("asd_admin_password_confirmation", "Y");
            if ($setting_using_password) {

                if (empty($useremail) || empty($password)) {
                    wp_send_json_error(['message' => 'useremail and password are required']);
                    exit;
                }
            }
            if (empty($displayName)) {
                wp_send_json_error(['message' => 'Display Name is required']);
                exit;
            }

            $PasskeyData = new GeneralModel("passkey_data");
            $PasskeyData->get_all(['email' => $useremail], 'user', 'ASC');
            if ($PasskeyData->num_rows > 0) {
                wp_send_json_error(['message' => 'You already have a registered passkey. Please use it to log in or contact support for assistance.']);
                exit;
            }
            if ($setting_using_password) {
                $user = get_user_by('email', $useremail);
                if ($user && wp_check_password($password, $user->user_pass, $user->ID)) {
                    wp_send_json_success(['message' => "useremail and password correct."]);
                    return;
                } else {
                    wp_send_json_error(['message' => 'Email and password is not correct.']);
                    return;
                }
            }
            wp_send_json_success(['message' => "useremail and password correct."]);
            return;
        }

        public function handleFlagging()
        {
            if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                wp_send_json_error(['message' => 'Invalid request method']);
                exit;
            }
            // maybe bugs wordpress, always failed when check nonce
            $_wpnonce  = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
            if (!isset($_wpnonce) || !wp_verify_nonce($_wpnonce, 'asd_nonce_passkey_flagging')) {
                wp_send_json_error(['message' => 'Nonce verification failed']);
                return;
            }
            $token     = isset($_POST['token']) ? sanitize_text_field(wp_unslash($_POST['token'])) : '';
            $authenticator     = isset($_POST['authenticator']) ? sanitize_text_field(wp_unslash($_POST['authenticator'])) : '';

            if (empty($token)) {
                wp_send_json_error(['message' => 'Token not found.']);
                exit;
            }

            $validToken = new JwtToken();
            $checkToken = $validToken->checkToken($token);
            if (isset($checkToken) && isset($checkToken->payload->userHandle)) {
                $userHandle = $checkToken->payload->userHandle;
                $data = array(
                    'user'   => wp_get_current_user()->user_login,
                    'email' => wp_get_current_user()->user_email,
                    'user_handle' => $userHandle ?? '',
                    'authenticator' =>  $authenticator
                );
                $format = array('%s', '%s');
                $PasskeyModel = new GeneralModel("passkey_data");
                $result = $PasskeyModel->insert($data, $format);
                if (!$result) {
                    wp_send_json_error(['message' => 'Error while saving passkey.']);
                    exit;
                }

                wp_send_json_success(['message' => 'Passkey saved successfully']);
            } else {
                wp_send_json_error(['message' => "Something wrong with token."]);
            }
        }
        public function asdAddEegisterPasskeyMenu($items)
        {
            $new_items = [];
            foreach ($items as $key => $value) {
                $new_items[$key] = $value;
                if ($key === 'edit-account') {
                    $new_items['register-passkey'] = __('Register Passkey', 'asd-passkey-login');
                }
            }
            return $new_items;
        }

        function asdRegisterPasskeyContent()
        {
            $data = [
                "show" => is_setting_valid("asd_admin_password_confirmation", "N", "none")
            ];
            view("asd-create-passkey-woocommerce", $data);
        }
        public function asdRegisterPasskeyEndpoint()
        {
            add_rewrite_endpoint('register-passkey', EP_PAGES);
        }
    }
}
