<?php

namespace Asd\Controllers;

if (!defined('ABSPATH')) exit;

abstract class BaseController
{
    /**
     * BaseController constructor.
     * Can be used to set up common properties or methods.
     */
    protected $defaultOptions = [
        'asd_version' => ASD_VERSION,
        'asd_db_version' => ASD_DB_VERSION,
        'asd_passwordless_active' => true,
        'asd_membership' => 'freemium',
        /* settings options */
        'asd_admin_login_form_style' => 'form_and_passkey',
        'asd_admin_password_confirmation' => 'Y',
        'asd_woo_login_form_style' => 'form_and_passkey',
        'asd_woo_password_confirmation' => 'Y',
        'asd_woo_login_fedcm_form' => 'disabled',
        'asd_woo_idp_provider' => "alcia",
        /* smtp options */
        'asd_smtp_host' => 'default',
        'asd_smtp_port' => '465',
        'asd_smtp_user' => 'default',
        'asd_smtp_password' => 'default',
    ];
    private $adminlinks = [
        'asd-create-passkey-admin',
        'asd-passkey-settings',
        'asd-upgrade-package'
    ];
    public function __construct()
    {
        if (!defined('ASD_PLUGIN_NAME') || !ASD_PLUGIN_NAME) {
            defined('ASD_PLUGIN_NAME') || define('ASD_PLUGIN_NAME', 'asd-passkey-login');
        }
        add_action('admin_init', [$this, 'removeNotices']);
        add_action('admin_notices', [self::class, 'showActivatedMessage']);
    }
    public function removeNotices()
    {
        $current_screen = get_current_screen();
        if (isset($current_screen->id) && in_array($current_screen->id, $this->adminlinks)) {
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
        }
    }
    public function initDefaultOptions()
    {
        foreach ($this->defaultOptions as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
    public static function showActivatedMessage()
    {
        if (get_option('asd_passkey_activation_notice')) {
            echo '<div class="notice notice-success is-dismissible">';
            printf(
                '<p>%s <a href="%s">%s</a> %s <a href="%s">%s</a>.</p>',
                'The <strong>ASD Passkey Login </strong> for WordPress plugin has been successfully installed.',
                esc_url('https://passwordless.alciasolusidigital.com/documentation'),
                esc_html__('visit our documentation', 'asd-passkey-login'),
                esc_html__('or explore the benefits of secure and seamless login methods by accessing the', 'asd-passkey-login'),
                esc_url('https://passwordless.alciasolusidigital.com/'),
                esc_html__('benefits page', 'asd-passkey-login')
            );
            echo '</div>';
            delete_option('asd_passkey_activation_notice');
        }
    }
}
