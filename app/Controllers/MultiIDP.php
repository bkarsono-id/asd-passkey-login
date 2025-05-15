<?php

namespace Asd\Controllers;

use Asd\Models\GeneralModel;
use Asd\Classes\JwtToken;

if (!defined('ABSPATH')) exit;
if (!class_exists(MultiIDP::class)) {
    class MultiIDP extends BaseController
    {
        public function __construct()
        {
            add_action('wp_ajax_asd_passkey_login', [$this, 'handleLogin']);
            add_action('wp_ajax_nopriv_asd_passkey_login', [$this, 'handleLogin']);
            add_action('wp_ajax_asd_google_check_token', [$this, 'handleGoogleLogin']);
            add_action('wp_ajax_nopriv_asd_google_check_token', [$this, 'handleGoogleLogin']);
            add_action('login_form', [$this, 'addPasskeyLoginLink']);
        }
        public function addPasskeyLoginLink()
        {
            $form_style = get_option("asd_admin_login_form_style");
            if ($form_style === "passkey_only") {
                echo '<style>
                        body.login div#login form#loginform p,
                        body.login div#login form#loginform div,
                        body.login div#login form#loginform input {
                            display: none;
                        }
                    </style>';
            }
            if ($form_style === "passkey_only" || $form_style === "form_and_passkey") {
                $rowhide = $form_style === "form_and_passkey" ? "asd-passkey-login-wrapper-hybrid" : "";
                echo '<div class="asd-passkey-login-wrapper ' . $rowhide . '" id="asd-passkey-login-wrapper" style="display: none;">
                <div id="infoMessage" class="notice notice-info" style="display: none;"></div>
                <div id="errorMessage" class="notice notice-error" style="display: none;"></div>
                <div id="successMessage" class="notice notice-success" style="display: none;"></div>
                <button id="login-via-passkey" class="button button-large login-via-passkey">
                    <span id="spinnerText" style="display: none;">' . esc_html__('Login via Passkey...', 'asd-passkey-login') . '</span>
                    <span id="buttonText">' . esc_html__('Login via Passkey', 'asd-passkey-login') . '</span>
                </button>
            </div>
           
            ';
            }
            // if (is_setting_valid("asd_woo_idp_provider", "google")) {
            //     $clientId = get_option("asd_google_client_id");
            //     echo '<div id="g_id_onload"
            //             data-client_id="' . $clientId . '"
            //             data-auto_prompt="true">
            //           </div>';
            // }
            // echo '<iframe 
            //         src="https://fedcm.id" 
            //         allow="identity-credentials-get" 
            //         style="display: none;">
            //     </iframe>';
        }

        /**
         * data handling using AJAX
         */
        public function handleLogin()
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
                    wp_send_json_success(['redirect' => admin_url()]);
                } else {
                    wp_send_json_error(['message' => $user]);
                }
            } else {
                wp_send_json_error(['message' => "Something wrong with token."]);
            }
        }

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
