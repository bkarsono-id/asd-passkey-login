<?php

namespace bkarsono\asdpasskeylogin\controllers;

use bkarsono\asdpasskeylogin\models\GeneralModel;
use bkarsono\asdpasskeylogin\classes\JwtToken;

if (!defined('ABSPATH')) exit;
if (!class_exists(LoginWoocommerce::class)) {
    class LoginWoocommerce extends BaseController
    {
        /**
         * LoginWoocommerce constructor.
         * Registers AJAX actions for passkey and Google login, and adds the passkey login link to WooCommerce forms.
         *
         * @return void
         */

        public function __construct()
        {
            add_action('wp_ajax_asd_woo_passkey_login', [$this, 'handleLogin']);
            add_action('wp_ajax_nopriv_asd_woo_passkey_login', [$this, 'handleLogin']);
            add_action('woocommerce_login_form', [$this, 'addPasskeyLoginLink']);
            add_action('woocommerce_after_account_navigation', [$this, 'addPasskeyLoginLink']);
        }

        /**
         * Adds the passkey login button and related UI elements to the WooCommerce login and account pages.
         *
         * @return void
         */
        public function addPasskeyLoginLink()
        {
            $form_style = get_option("asd_p4ssk3y_woo_login_form_style");
            if (!is_user_logged_in() && ASD_P4SSK3Y_is_pro_license() && ($form_style === "passkey_only" || $form_style === "form_and_passkey")) {
                echo '<div class="asd-passkey-login-wrapper" id="asd-passkey-login-wrapper" style="display: none;">
                <div id="infoMessage" class="notice notice-info" style="display: none;"></div>
                <div id="errorMessage" class="notice notice-error" style="display: none;"></div>
                <div id="successMessage" class="notice notice-success" style="display: none;"></div>
                <button id="login-via-passkey" class="button button-large login-via-passkey">
                    <span id="spinnerText" style="display: none;">' . esc_html__('Login via Passkey...', 'asd-passkey-login') . '</span>
                    <span id="buttonText">' . esc_html__('Login via Passkey', 'asd-passkey-login') . '</span>
                </button>
            </div>';
            }
            if (ASD_P4SSK3Y_is_setting_valid("asd_p4ssk3y_woo_idp_provider", "google")) {
                $clientId = get_option("asd_google_client_id");
                echo '<div id="g_id_onload"
                        data-client_id="' . esc_attr($clientId) . '"
                        data-auto_prompt="true">
                      </div>';
            }
        }

        /**
         * Handles the AJAX request for WooCommerce login using passkey authentication.
         * Verifies the token, checks user credentials, and logs in the user if valid.
         *
         * @return void
         */
        public function handleLogin()
        {
            if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                wp_send_json_error(['message' => 'Invalid request method']);
                exit;
            }
            if (!isset($_SERVER['REQUEST_URI'])) {
                return;
            }
            $token     = isset($_POST['token']) ? sanitize_text_field(wp_unslash($_POST['token'])) : '';
            $_wpnonce  = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
            if (!isset($_wpnonce) || !wp_verify_nonce($_wpnonce, 'asd_woo_passkey_login_nonce')) {
                wp_send_json_error(['message' => 'Nonce verification failed.']);
                exit;
            }
            if (empty($token)) {
                wp_send_json_error(['message' => 'Token not found.']);
                exit;
            }
            $validToken = new JwtToken();
            $checkToken = $validToken->checkToken($token);
            $userHandle = $checkToken->payload->userHandle;
            if (isset($checkToken) && isset($userHandle)) {
                $passkeyModel = new GeneralModel("passkey_data");
                $validUser = $passkeyModel->getByUserHandle($userHandle);
                if (!$validUser) {
                    wp_send_json_error(['message' => 'User does not have a registered passkey.']);
                    exit;
                }
                $user = get_user_by('login', $validUser["user"]);

                if ($user) {
                    wp_clear_auth_cookie();
                    wp_set_current_user($user->ID);
                    wp_set_auth_cookie($user->ID);
                    wp_send_json_success(['redirect' => home_url()]);
                } else {
                    wp_send_json_error(['message' => $user]);
                }
            } else {
                wp_send_json_error(['message' => "Something wrong with token."]);
            }
        }

        /**
         * Handles the AJAX request for WooCommerce login using Google OAuth.
         * Verifies the Google token, checks user credentials, and logs in the user if valid.
         *
         * @return void
         */
        public function handleGoogleLogin()
        {
            if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                wp_send_json_error(['message' => 'Invalid request method']);
                exit;
            }
            if (!isset($_SERVER['REQUEST_URI'])) {
                exit;
            }
            $token     = isset($_POST['token']) ? sanitize_text_field(wp_unslash($_POST['token'])) : '';
            $_wpnonce  = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
            if (!isset($_wpnonce) || !wp_verify_nonce($_wpnonce, 'asd_passkey_login')) {
                wp_send_json_error(['message' => 'Nonce verification failed ']);
                exit;
            }
            if (empty($token)) {
                wp_send_json_error(['message' => 'Token not found.']);
                exit;
            }
            $validToken = new JwtToken();
            $checkToken = $validToken->checkGoogleToken($token);

            $userEmail = $checkToken["email"];
            if (isset($checkToken) && isset($userEmail)) {

                $user = get_user_by('email', $userEmail);
                if ($user) {
                    if (in_array('customer', (array) $user->roles)) {
                        wp_send_json_error(['message' => "Access denied."]);
                        exit;
                    }
                    wp_clear_auth_cookie();
                    wp_set_current_user($user->ID);
                    wp_set_auth_cookie($user->ID);
                    wp_send_json_success(['redirect' => admin_url()]);
                } else {
                    wp_send_json_error(['message' => $user]);
                }
            } else {
                wp_send_json_error(['message' => "Something wrong with token."]);
                exit;
            }
        }
    }
}
