<?php

namespace bkarsono\asdpasskeylogin\core;

if (!defined('ABSPATH')) exit;

if (!class_exists(Menu::class)) {
    class Menu
    {
        private $menu_slug = 'dummy-settings';
        private $page_title = 'Passkey Settings';
        private $menu_title = 'ASD Passkey';
        private $capability = 'manage_options';
        private $allowed_hooks = [
            'asd-create-passkey-admin',
            'asd-passkey-settings',
            'asd-upgrade-package',
            'asd-send-notification-admin'
        ];
        /**
         * Constructor for the Menu class.
         * Defines the plugin name constant if not already defined.
         *
         * @return void
         */
        public function __construct()
        {
            if (!defined('ASD_P4SSK3Y_PLUGIN_NAME') || !ASD_P4SSK3Y_PLUGIN_NAME) {
                defined('ASD_P4SSK3Y_PLUGIN_NAME') || define('ASD_P4SSK3Y_PLUGIN_NAME', 'asd-passkey-login');
            }
        }

        /**
         * Register all menu, admin bar, and script enqueue hooks for the plugin.
         *
         * @return void
         */
        public  function generateMenu()
        {
            add_action('admin_menu', [$this, 'addAdminMenu']);
            add_action('admin_bar_menu', [$this, 'asdAddCreatePasskeyButton'], 100);
            add_action('wp_after_admin_bar_render', [$this, 'asdNavbarShortcode']);
            add_action('admin_enqueue_scripts', [$this, 'asdEnqueueAdminScript']);
            add_action('login_enqueue_scripts', [$this, 'asdEnqueueLoginScript']);
            add_action('wp_enqueue_scripts', [$this, 'asdEnqueueLoginScript']);
            add_action('wp_enqueue_scripts', [$this, 'asdEnqueueWooRegisterScript']);
            add_action('wp_enqueue_scripts', [$this, 'asdWebPushRegistration']);
        }
        /**
         * Render the custom admin navigation bar on allowed pages.
         *
         * @return void
         */
        public function asdNavbarShortcode()
        {
            if (isset($_GET['page']) && in_array($_GET['page'], $this->allowed_hooks, true) && current_user_can('administrator')) {
                $settings_url = admin_url('admin.php?page=asd-passkey-settings');
                $create_passkey_url = admin_url('admin.php?page=asd-create-passkey-admin');
                $send_notification_url = admin_url('admin.php?page=asd-send-notification-admin');
                $docs_url = 'https://passwordless.alciasolusidigital.com';
                $upgrade_url = admin_url('admin.php?page=asd-upgrade-package');
                $navbarHtml = '
                <nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color:#ffffff;">
                    <div class="container-fluid">
                        <a class="navbar-brand asd-navbar-brand" href="#">ASD Passkey Login</a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse justify-content-md-center" id="navbarNav">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="' . esc_url($settings_url) . '">Settings</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="' . esc_url($create_passkey_url) . '">Create Passkey</a>
                                </li>
                                  <li class="nav-item">
                                    <a class="nav-link" href="' . esc_url($send_notification_url) . '">Send Notification</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="' . esc_url($upgrade_url) . '" style="color:blue;">Pricing</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" target="_blank" href="' . esc_url($docs_url) . '">About Passwordless</a>
                                </li>
                                
                            </ul>
                        </div>
                    </div>
                </nav>';
                echo $navbarHtml;
            }
        }

        /**
         * Add a "Create Passkey" button to the WordPress admin bar.
         *
         * @param WP_Admin_Bar $wp_admin_bar The WordPress admin bar object.
         * @return void
         */
        public function asdAddCreatePasskeyButton($wp_admin_bar)
        {
            if (is_user_logged_in()) {
                $wp_admin_bar->add_node([
                    'id'    => 'create_passkey',
                    'title' => '<span class="ab-icon dashicons dashicons-shield"></span>Create Passkey',
                    'href'  => admin_url('admin.php?page=asd-create-passkey-admin'),
                    'meta'  => [
                        'class' => 'create-passkey-class',
                        'title' => 'Create a new passkey for your account',
                    ],
                ]);
            }
        }

        /**
         * Register the main menu and submenu pages for the plugin in the WordPress admin.
         *
         * @return void
         */
        public function addAdminMenu()
        {
            add_menu_page(
                $this->page_title,
                $this->menu_title,
                $this->capability,
                $this->menu_slug,
                [$this, 'renderSettingsPage'],
                'dashicons-admin-network',
                100
            );
            add_submenu_page(
                $this->menu_slug,
                __('ASD Passkey For Wordpress', ASD_P4SSK3Y_PLUGIN_NAME),
                __('Settings', 'asd-passwordless'),
                'manage_options',
                'dummy-settings',
                ''
            );
            remove_submenu_page('dummy-settings', 'dummy-settings');
            add_submenu_page(
                $this->menu_slug,
                __('ASD Passkey For Wordpress', ASD_P4SSK3Y_PLUGIN_NAME),
                __('Settings', ASD_P4SSK3Y_PLUGIN_NAME),
                'manage_options',
                'asd-passkey-settings',
                [new \bkarsono\asdpasskeylogin\controllers\PasskeySettings(), 'index']
            );
            add_submenu_page(
                $this->menu_slug,
                __('ASD Passkey For Wordpress', ASD_P4SSK3Y_PLUGIN_NAME),
                __('Create Passkey', ASD_P4SSK3Y_PLUGIN_NAME),
                'read',
                'asd-create-passkey-admin',
                [new \bkarsono\asdpasskeylogin\controllers\CreatePasskeyAdmin(), 'index']
            );
            add_submenu_page(
                $this->menu_slug,
                __('ASD Passkey For Wordpress', ASD_P4SSK3Y_PLUGIN_NAME),
                __('Send Notification', ASD_P4SSK3Y_PLUGIN_NAME),
                'read',
                'asd-send-notification-admin',
                [new \bkarsono\asdpasskeylogin\controllers\SendNotificationAdmin(), 'index']
            );
            add_submenu_page(
                $this->menu_slug,
                __('ASD Passkey For Wordpress', ASD_P4SSK3Y_PLUGIN_NAME),
                __('Upgrade', ASD_P4SSK3Y_PLUGIN_NAME),
                'manage_options',
                'asd-upgrade-package',
                [new \bkarsono\asdpasskeylogin\controllers\UpgradePackage(), 'index']
            );
        }

        /**
         * Enqueue styles and scripts for the plugin's admin pages.
         *
         * @return void
         */
        public function asdEnqueueAdminScript()
        {
            $page = $_GET['page'] ?? '';

            if (isset($_GET['page']) && in_array($_GET['page'], $this->allowed_hooks, true)) {
                wp_enqueue_style(
                    'bootstrap-css',
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
                    [],
                    time()
                );
                wp_enqueue_style(
                    'asd-login-style',
                    ASD_P4SSK3Y_PUBLICURL . 'css/admin-style.css',
                    [],
                    time()
                );
                wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', ['jquery'], [], true);
                wp_enqueue_script('sweetalert-js', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js', array('jquery'), [], true);
                wp_enqueue_script('asd-passwordless-js', ASD_P4SSK3Y_PUBLICURL . 'js/asd-passwordless.js', ['jquery'], ASD_P4SSK3Y_VERSION, true);
                if ($page !== 'asd-upgrade-package') {
                    $EAuthUrl = get_option("asd_p4ssk3y_eauth_url");
                    wp_enqueue_script('asd-sync-passwordless-js', $EAuthUrl, ['jquery'], null, true);
                }
                if ($page === 'asd-create-passkey-admin') {
                    wp_enqueue_script(
                        'asd-admin-create-passkey-script',
                        ASD_P4SSK3Y_PUBLICURL . 'js/admin-create-passkey.js',
                        [],
                        ASD_P4SSK3Y_VERSION,
                        true
                    );
                    $user_data = [
                        'userId'          => get_current_user_id(),
                        'userName'        => wp_get_current_user()->user_login,
                        'userEmail'       => wp_get_current_user()->user_email
                    ];
                    wp_localize_script('asd-admin-create-passkey-script', 'users', $user_data);
                    wp_localize_script(
                        'asd-admin-create-passkey-script',
                        'asd_ajax',
                        [
                            'ajax_url' => admin_url('admin-ajax.php'),
                            'ajax_nonce_register' => wp_create_nonce('asd_nonce_passkey_register'),
                            'ajax_nonce_flagging' => wp_create_nonce('asd_nonce_passkey_flagging'),
                            'api_key' => get_option('asd_p4ssk3y_key1'),
                            'api_url' => get_option('asd_p4ssk3y_api_server'),
                        ]
                    );
                }
                if ($page === 'asd-passkey-settings') {
                    wp_enqueue_media();
                    wp_enqueue_script(
                        'asd-passkey-settings-script',
                        ASD_P4SSK3Y_PUBLICURL . 'js/admin-passkey-settings.js',
                        [],
                        ASD_P4SSK3Y_VERSION,
                        true
                    );
                    wp_localize_script(
                        'asd-passkey-settings-script',
                        'asd_ajax',
                        [
                            'ajax_url' => admin_url('admin-ajax.php'),
                            'ajax_nonce' => wp_create_nonce('asd_passkey_settings_nonce'),
                            'ajax_sync_nonce' => wp_create_nonce('asd_sync_package_nonce'),
                            'ajax_smtp_nonce' => wp_create_nonce('asd_smtp_settings_nonce'),
                            'ajax_smtp_test_nonce' => wp_create_nonce('asd_smtp_test_nonce'),
                            'ajax_webpush_nonce' => wp_create_nonce('asd_webpush_nonce'),
                            'ajax_webpush_publickey_nonce' => wp_create_nonce('asd_webpush_publickey_nonce'),
                        ]
                    );
                }
                if ($page === 'asd-send-notification-admin') {
                    wp_enqueue_script(
                        'asd-admin-send-notification-script',
                        ASD_P4SSK3Y_PUBLICURL . 'js/admin-send-notification.js',
                        [],
                        ASD_P4SSK3Y_VERSION,
                        true
                    );
                    wp_localize_script(
                        'asd-admin-send-notification-script',
                        'asd_ajax',
                        [
                            'ajax_url' => admin_url('admin-ajax.php'),
                            'ajax_nonce' => wp_create_nonce('asd_send_notification'),
                            'ajax_nonce_product' => wp_create_nonce('asd_product_nonce'),
                        ]
                    );
                }
            }
        }

        /**
         * Enqueue scripts and localize data for the WooCommerce passkey registration page.
         *
         * @return void
         */
        public function asdEnqueueWooRegisterScript()
        {

            if (is_account_page() && is_user_logged_in() && strpos($_SERVER['REQUEST_URI'], 'register-passkey') !== false) {
                wp_enqueue_script('sweetalert-js', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js', array('jquery'), [], true);
                wp_enqueue_script('asd-passwordless-js', ASD_P4SSK3Y_PUBLICURL . 'js/asd-passwordless.js', ['jquery'], ASD_P4SSK3Y_VERSION, true);

                $EAuthUrl = get_option("asd_p4ssk3y_eauth_url");
                wp_enqueue_script('asd-sync-passwordless-js', $EAuthUrl, ['jquery'], [], true);

                wp_enqueue_script(
                    'asd-woo-create-passkey-script',
                    ASD_P4SSK3Y_PUBLICURL . 'js/woo-create-passkey.js',
                    [],
                    ASD_P4SSK3Y_VERSION,
                    true
                );
                $user_data = [
                    'userId'          => get_current_user_id(),
                    'userName'        => wp_get_current_user()->user_login,
                    'userEmail'       => wp_get_current_user()->user_email
                ];
                wp_localize_script('asd-woo-create-passkey-script', 'users', $user_data);
                wp_localize_script(
                    'asd-woo-create-passkey-script',
                    'asd_ajax',
                    [
                        'ajax_url' => admin_url('admin-ajax.php'),
                        'ajax_nonce_register' => wp_create_nonce('asd_nonce_passkey_register'),
                        'ajax_nonce_flagging' => wp_create_nonce('asd_nonce_passkey_flagging'),
                        'api_key' => get_option('asd_p4ssk3y_key1'),
                        'api_url' => get_option('asd_p4ssk3y_api_server'),
                    ]
                );
            }
        }

        /**
         * Enqueue styles and scripts for the login and WooCommerce account pages.
         *
         * @return void
         */
        public function asdEnqueueLoginScript()
        {

            if ($this->is_login_page()) {
                $custom_logo_id = get_theme_mod('custom_logo');
                $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
                wp_enqueue_style(
                    'asd-login-style',
                    ASD_P4SSK3Y_PUBLICURL . 'css/login-style.css',
                    [],
                    ASD_P4SSK3Y_VERSION
                );

                wp_enqueue_script(
                    'asd-common-script',
                    ASD_P4SSK3Y_PUBLICURL . 'js/asd-passwordless.js',
                    [],
                    ASD_P4SSK3Y_VERSION,
                    true
                );
                $EAuthUrl = get_option("asd_p4ssk3y_eauth_url");
                wp_enqueue_script('asd-sync-passwordless-js', $EAuthUrl, ['jquery'], [], true);

                if (!ASD_P4SSK3Y_is_setting_valid("asd_p4ssk3y_woo_login_fedcm_form", "disabled")) {
                    if (ASD_P4SSK3Y_is_setting_valid("asd_p4ssk3y_woo_idp_provider", "google")) {
                        wp_enqueue_script('asd-google-gsi', 'https://accounts.google.com/gsi/client', [], null, [
                            'in_footer' => false,
                            'strategy'  => 'defer async',
                        ]);
                    } else {
                        $FedCMUrl = get_option("asd_p4ssk3y_fedcm_url");
                        wp_enqueue_script(
                            'asd-google-gsi',
                            $FedCMUrl,
                            [],
                            ASD_P4SSK3Y_VERSION,
                            true
                        );
                    }
                }


                if (function_exists('is_account_page') && is_account_page()) {
                    wp_enqueue_script(
                        'asd-woo-login-script',
                        ASD_P4SSK3Y_PUBLICURL . 'js/fe-woo-login.js',
                        [],
                        time(),
                        true
                    );
                    $gclientId = '';
                    if (ASD_P4SSK3Y_is_setting_valid("asd_p4ssk3y_woo_login_fedcm_form", "woo_page") || ASD_P4SSK3Y_is_setting_valid("asd_p4ssk3y_woo_login_fedcm_form", "both")) {
                        $gclientId = get_option("asd_p4ssk3y_google_client_id");
                    }
                    wp_localize_script(
                        'asd-woo-login-script',
                        'asd_ajax',
                        [
                            'ajax_url' => admin_url('admin-ajax.php'),
                            'ajax_woo_login_nonce' => wp_create_nonce('asd_woo_passkey_login_nonce'),
                            'google_client_id' => $gclientId,
                            'api_key' => get_option('asd_p4ssk3y_key1'),
                            'api_url' => get_option('asd_p4ssk3y_api_server'),
                            'logo' => $logo_url
                        ]
                    );
                } else {
                    wp_enqueue_script(
                        'asd-login-script',
                        ASD_P4SSK3Y_PUBLICURL . 'js/fe-login.js',
                        [],
                        time(),
                        true
                    );
                    $gclientId = '';
                    if (ASD_P4SSK3Y_is_setting_valid("asd_p4ssk3y_woo_login_fedcm_form", "admin_page") || ASD_P4SSK3Y_is_setting_valid("asd_p4ssk3y_woo_login_fedcm_form", "both")) {
                        $gclientId = get_option("asd_p4ssk3y_google_client_id");
                    }

                    wp_localize_script(
                        'asd-login-script',
                        'asd_ajax',
                        [
                            'ajax_url' => admin_url('admin-ajax.php'),
                            'ajax_nonce' => wp_create_nonce('asd_passkey_login'),
                            'google_client_id' => $gclientId,
                            'api_key' => get_option('asd_p4ssk3y_key1'),
                            'api_url' => get_option('asd_p4ssk3y_api_server'),

                        ]
                    );
                }
            }
        }

        /**
         * Enqueue styles and scripts for web push notification registration.
         *
         * @return void
         */
        public function asdWebPushRegistration()
        {
            // if (is_woocommerce()) {
            if (ASD_P4SSK3Y_is_setting_valid("asd_p4ssk3y_push_notification", "Y") && ASD_P4SSK3Y_is_scale_license() === true) {
                wp_enqueue_style(
                    'animate-alert-css',
                    'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
                    [],
                    time()
                );
                wp_enqueue_script('sweetalert-js', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js', array('jquery'), [], true);
                wp_enqueue_script(
                    'asd-push-notification-script',
                    ASD_P4SSK3Y_PUBLICURL . 'js/asd-notification-service.js',
                    [],
                    time(),
                    true
                );
                wp_localize_script(
                    'asd-push-notification-script',
                    'webpush',
                    [
                        'ajax_url' => admin_url('admin-ajax.php'),
                        'ajax_nonce' => wp_create_nonce('asd_save_subscriber'),
                        'ajax_public_url' => ASD_P4SSK3Y_PUBLICURL,
                        'icon' => get_site_icon_url(),
                        'public_key' => get_option('asd_p4ssk3y_webpush_public_key'),
                    ]
                );
            }
            // }
        }

        /**
         * Check if the current page is the login page or WooCommerce account page (when not logged in).
         *
         * @return bool
         */
        public function is_login_page()
        {
            return basename($_SERVER['PHP_SELF']) === 'wp-login.php' ||  (function_exists('is_account_page') && is_account_page() && !is_user_logged_in());
        }
    }
}
