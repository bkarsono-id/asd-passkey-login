<?php

namespace bkarsono\asdpasskeylogin\controllers;

if (!defined('ABSPATH')) exit;

abstract class BaseController
{
    /**
     * BaseController constructor.
     * Can be used to set up common properties or methods.
     */
    protected $defaultOptions = [
        'asd_p4ssk3y_version' => ASD_P4SSK3Y_VERSION,
        'asd_p4ssk3y_db_version' => ASD_P4SSK3Y_VERSION,
        'asd_p4ssk3y_passwordless_active' => true,
        'asd_p4ssk3y_membership' => 'freemium',
        /* settings options */
        'asd_p4ssk3y_admin_login_form_style' => 'form_and_passkey',
        'asd_p4ssk3y_admin_password_confirmation' => 'Y',
        'asd_p4ssk3y_woo_login_form_style' => 'form_and_passkey',
        'asd_p4ssk3y_woo_password_confirmation' => 'Y',
        'asd_p4ssk3y_woo_login_fedcm_form' => 'disabled',
        'asd_p4ssk3y_woo_idp_provider' => "alcia",
        /* smtp options */
        'asd_p4ssk3y_smtp_host' => 'default',
        'asd_p4ssk3y_smtp_port' => '465',
        'asd_p4ssk3y_smtp_user' => 'default',
        'asd_p4ssk3y_smtp_password' => 'default',
        /* push notification */
        'asd_p4ssk3y_push_notification' => 'N',
        'asd_p4ssk3y_snv_notification' => 'silent',
        'asd_p4ssk3y_interaction_notification' => 'N',
        'asd_p4ssk3y_webpush_public_key' => '',
        'asd_p4ssk3y_icon_url' => '',
        'asd_p4ssk3y_badge_url' => ''
    ];
    /**
     * BaseController constructor.
     * Sets up the plugin name constant and registers the admin notice action.
     *
     * @return void
     */
    public function __construct()
    {
        if (!defined('ASD_P4SSK3Y_PLUGIN_NAME') || !ASD_P4SSK3Y_PLUGIN_NAME) {
            defined('ASD_P4SSK3Y_PLUGIN_NAME') || define('ASD_P4SSK3Y_PLUGIN_NAME', 'asd-passkey-login');
        }
        add_action('admin_notices', [self::class, 'showActivatedMessage']);
    }

    /**
     * Initialize all default plugin options if they do not exist.
     *
     * @return void
     */
    public function initDefaultOptions()
    {
        foreach ($this->defaultOptions as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }

    /**
     * Show the activation success notice in the WordPress admin area after plugin activation.
     * Displays a message with links to documentation and benefits page, then removes the notice option.
     *
     * @return void
     */
    public static function showActivatedMessage()
    {
        if (get_option('asd_p4ssk3y_activation_notice')) {
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
            delete_option('asd_p4ssk3y_activation_notice');
        }
    }
}
