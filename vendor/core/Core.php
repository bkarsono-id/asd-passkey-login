<?php

namespace bkarsono\asdpasskeylogin\core;

if (!defined('ABSPATH')) exit;

if (!class_exists(Core::class)) {
    class Core
    {
        /**
         * Initialize the plugin by registering WordPress filters and hooks.
         *
         * @return void
         */
        public  function run()
        {

            add_filter("plugin_action_links_" . ASD_P4SSK3Y_PLUGIN_NAME, [self::class, 'settingLink']);
            add_filter('admin_footer_text', [self::class, 'remove_admin_footer_message']);
            add_filter('update_footer', [self::class, 'remove_admin_footer_message'], 9999);
            add_filter('plugins_loaded', [self::class, 'onPluginReady']);
        }

        /**
         * Callback executed when all plugins are loaded.
         * Instantiates controller classes required for the plugin.
         *
         * @return void
         */
        public static function onPluginReady()
        {
            $classNames = ['LoginAdmin', 'PasskeySettings', 'CreatePasskeyAdmin', 'SendNotificationAdmin'];
            if (class_exists('WooCommerce')) {
                $classNames = array_merge($classNames, ['LoginWoocommerce', 'CreatePasskeyWoocommerce', 'PushNotification']);
            }
            $nameSpace = 'bkarsono\\asdpasskeylogin\\controllers\\';
            foreach ($classNames as $className) {
                $fullClassName = $nameSpace . $className;
                if (class_exists($fullClassName)) {
                    new $fullClassName();
                } else {
                    ASD_P4SSK3Y_asdlog("[ASD Boot Plugin] $fullClassName class not found. Please check autoload configuration.");
                }
            }
        }

        /**
         * Add a "Settings" link to the plugin action links on the plugins page.
         *
         * @param array $links Existing plugin action links.
         * @return array Modified plugin action links with the settings link.
         */
        public static function settingLink($links)
        {
            $settingLink = '<a href="admin.php?page=asd-passkey-settings">Settings </a>';
            array_push($links, $settingLink);
            return $links;
        }

        /**
         * Remove the default admin footer message.
         *
         * @return string Empty string to clear the footer.
         */
        public static function remove_admin_footer_message()
        {
            return '';
        }
    }
}
