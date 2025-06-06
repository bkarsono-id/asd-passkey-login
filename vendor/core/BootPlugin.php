<?php

namespace bkarsono\asdpasskeylogin\core;

use bkarsono\asdpasskeylogin\config\Paths;

if (!defined('ABSPATH')) exit;

if (!class_exists(BootPlugin::class)) {
    class BootPlugin
    {
        /**
         * Start the plugin bootstrapping process.
         *
         * @param Paths $paths The Paths configuration object.
         * @return int Returns 1 on successful start.
         */
        public static function start(Paths $paths): int
        {
            static::loadSystemHelper($paths);
            static::definePathConstants($paths);
            if (! defined('ASD_P4SSK3Y_VERSION')) {
                static::loadConstants();
            }
            static::loadAutoloader($paths);
            static::loadCommonFunctions();
            static::runPlugin();
            return 1;
        }

        /**
         * Define path constants for convenient access throughout the application.
         *
         * @param Paths $paths The Paths configuration object.
         * @return void
         */

        protected static function definePathConstants(Paths $paths): void
        {
            // The path to the application directory.
            if (! defined('ASD_P4SSK3Y_APPPATH')) {
                define('ASD_P4SSK3Y_APPPATH', realpath(rtrim($paths->appDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
            }

            // The path to the project root directory. Just above APPPATH.
            if (! defined('ASD_P4SSK3Y_ROOTPATH')) {
                define('ASD_P4SSK3Y_ROOTPATH', realpath(ASD_P4SSK3Y_APPPATH . '../') . DIRECTORY_SEPARATOR);
            }

            // The path to the project public directory. Just above APPPATH.
            if (! defined('ASD_P4SSK3Y_PUBLICPATH')) {
                define('ASD_P4SSK3Y_PUBLICPATH', realpath(ASD_P4SSK3Y_ROOTPATH . 'public/') . DIRECTORY_SEPARATOR);
            }

            // The path to the system directory.
            if (! defined('ASD_P4SSK3Y_SYSTEMPATH')) {
                define('ASD_P4SSK3Y_SYSTEMPATH', realpath(rtrim($paths->systemDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
            }


            // The path to the writable directory.
            if (! defined('ASD_P4SSK3Y_WRITEPATH')) {
                define('ASD_P4SSK3Y_WRITEPATH', realpath(rtrim($paths->writableDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
            }

            // The path to the tests directory
            if (! defined('ASD_P4SSK3Y_TESTPATH')) {
                define('ASD_P4SSK3Y_TESTPATH', realpath(rtrim($paths->testsDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
            }

            if (! defined('ASD_P4SSK3Y_VIEWSPATH')) {
                define('ASD_P4SSK3Y_VIEWSPATH', realpath(rtrim($paths->viewDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
            }
        }

        /**
         * Load the plugin constants file.
         *
         * @return void
         */
        protected static function loadConstants(): void
        {
            require_once ASD_P4SSK3Y_APPPATH . 'Config/Constants.php';
        }

        /**
         * Load the Composer autoloader.
         *
         * @return void
         */
        protected static function loadAutoloader(): void
        {
            if (!file_exists(ASD_P4SSK3Y_SYSTEMPATH . '/../autoload.php')) {
                wp_die('Composer autoload file not found. Please run "composer install" in the plugin directory.');
            }
            require_once ASD_P4SSK3Y_SYSTEMPATH . '/../autoload.php';
        }
        /**
         * Load the system helper functions.
         *
         * @param Paths $paths The Paths configuration object.
         * @return void
         */
        protected static function loadSystemHelper(Paths $paths): void
        {
            $path = realpath(rtrim($paths->systemDirectory, '\\/ ')) . DIRECTORY_SEPARATOR;
            require_once $path . 'Helper.php';
        }

        /**
         * Load the common utility functions.
         *
         * @return void
         */
        protected static function loadCommonFunctions(): void
        {
            require_once ASD_P4SSK3Y_SYSTEMPATH . 'Common.php';
        }

        /**
         * Run the main plugin application and generate admin menus.
         *
         * @return void
         */
        protected static function runPlugin(): void
        {
            $app =  new Core();
            $app->run();
            $menus = new Menu();
            $menus->generateMenu();
        }
    }
}
