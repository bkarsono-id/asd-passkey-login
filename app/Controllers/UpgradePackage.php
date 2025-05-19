<?php

namespace bkarsono\asdpasskeylogin\controllers;

if (!defined('ABSPATH')) exit;

if (!class_exists(UpgradePackage::class)) {
    class UpgradePackage extends BaseController
    {
        public function __construct()
        {
            add_action('admin_notices', [self::class, 'showActivatedMessage']);
        }

        public function index()
        {
            $paket = get_option('asd_p4ssk3y_membership') ?? '';
            if ($paket === 'freemium') {
                $existing = 1;
                $choose = 3;
            } else if ($paket === 'starter') {
                $existing = 2;
                $choose = 4;
            } else if ($paket === 'growth') {
                $existing = 3;
                $choose = 4;
            } else if ($paket === 'scale') {
                $existing = 4;
                $choose = 5;
            }
            $data["existing"] = $existing;
            $data["choose"] = $choose;
            ASD_P4SSK3Y_view("asd-upgrade-package", $data);
        }
    }
}
