<?php

namespace bkarsono\asdpasskeylogin\core;

if (!defined('ABSPATH')) exit;

if (!class_exists(Core::class)) {
    class Core
    {
        public  function run()
        {

            add_filter("plugin_action_links_" . ASD_P4SSK3Y_PLUGIN_NAME, [self::class, 'settingLink']);
            add_filter('admin_footer_text', [self::class, 'remove_admin_footer_message']);
            add_filter('update_footer', [self::class, 'remove_admin_footer_message'], 9999);
            add_filter('plugins_loaded', [self::class, 'onPluginReady']);
        }


        public static function onPluginReady()
        {
            $classNames = ['LoginAdmin', 'PasskeySettings', 'CreatePasskeyAdmin', 'SendNotificationAdmin'];
            if (class_exists('WooCommerce')) {
                $classNames = array_merge($classNames, ['LoginWoocommerce', 'CreatePasskeyWoocommerce', 'PushNotification']);
            }
            $nameSpace = 'bkarsono\\asdpasskeylogni\\controllers\\';
            foreach ($classNames as $className) {
                $fullClassName = $nameSpace . $className;
                if (class_exists($fullClassName)) {
                    new $fullClassName();
                } else {
                    ASD_P4SSK3Y_asdlog("[ASD Boot Plugin] $fullClassName class not found. Please check autoload configuration.");
                }
            }
        }
        public static function settingLink($links)
        {
            $settingLink = '<a href="admin.php?page=asd-passkey-settings">Settings </a>';
            array_push($links, $settingLink);
            return $links;
        }
        public static function remove_admin_footer_message()
        {
            return '';
        }
    }
}
