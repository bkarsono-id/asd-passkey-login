<?php

use bkarsono\asdpasskeylogin\config\Paths;

/**
 * ASD Passkey Login
 *
 * @package           ASD-Passkey-Login
 * @author            Bobby Karsono
 * @copyright         2025 ALCIA SOLUSI DIGITAL
 * @license           GPLv2 or later
 *
 * @wordpress-plugin
 * Plugin Name:       ASD Passkey Login
 * Plugin URI:        https://passwordless.alciasolusidigital.com
 * Description:       ASD Passkey login for WordPress, known as JWT Eauth outside the WordPress ecosystem, is a cutting-edge authentication service designed to enhance online security by replacing traditional passwords with advanced methods like biometrics (fingerprint, facial recognition) or hardware security keys. As cyber threats become increasingly sophisticated, password-based systems are more vulnerable to attacks such as phishing, credential stuffing, and data breaches.       
 * Version:           1.1.0
 * Requires at least: 6.7
 * Requires PHP:      8.2
 * Author:            Alciasolusidigital
 * Author URI:        https://linkedin.com/bobbykarsono
 * Text Domain:       asd-passkey-login
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires Plugins:  
 * 
 * Copyright (C) 2010-2025 PT. ALCIA SOLUSI DIGITAL. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 */


if (!defined('ABSPATH')) exit;
defined('ASD_P4SSK3Y_PUBLICURL') || define('ASD_P4SSK3Y_PUBLICURL', plugin_dir_url(__FILE__) . 'public/');
define('ASD_P4SSK3Y_FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
if (getcwd() . DIRECTORY_SEPARATOR !== ASD_P4SSK3Y_FCPATH) {
    chdir(ASD_P4SSK3Y_FCPATH);
}
require ASD_P4SSK3Y_FCPATH . 'app/Config/Paths.php';
if (!class_exists(Paths::class)) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        wp_die('Class Paths not found. Please check your autoload setup.');
    }
    exit;
}
$paths = new bkarsono\asdpasskeylogin\config\Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/BootPlugin.php';

bkarsono\asdpasskeylogin\core\BootPlugin::start($paths);
register_activation_hook(__FILE__, [bkarsono\asdpasskeylogin\core\Events::class, 'onActivation']);
register_deactivation_hook(__FILE__, [bkarsono\asdpasskeylogin\core\Events::class, 'onDeactivation']);
