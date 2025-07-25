<?php
if (!defined('ABSPATH')) exit;
if (!function_exists('ASD_P4SSK3Y_view')) {
    /**
     * Load a ASD_P4SSK3Y_view file with the given data.
     *
     * @param string $name Name of the ASD_P4SSK3Y_view file (without `.php` extension).
     * @param array $data Data to be passed to the ASD_P4SSK3Y_view.
     * @param array $options Additional options (e.g., 'return' => true).
     * @return string|void
     */
    function ASD_P4SSK3Y_view(string $name, array $data = [], array $options = [])
    {
        $viewPath = defined('ASD_P4SSK3Y_VIEWSPATH') ? ASD_P4SSK3Y_VIEWSPATH : __DIR__ . '/views/';
        $templatePath = rtrim($viewPath, '/') . '/' . str_replace('.', '/', $name) . '.php';

        if (!file_exists($templatePath)) {
            ASD_P4SSK3Y_asdlog("ASD_P4SSK3Y_view file not found: $templatePath");
            return isset($options['return']) && $options['return'] ? '' : null;
        }

        if (!empty($data)) {
            extract($data, EXTR_SKIP);
        }

        ob_start();
        try {
            include $templatePath;
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $output = ob_get_clean();

        if (isset($options['return']) && $options['return']) {
            return $output;
        }

        echo $output;
    }
}


if (! function_exists('ASD_P4SSK3Y_base_url')) {
    /**
     * Returns the base URL as defined by the App config.
     * Base URLs are trimmed site URLs without the index page.
     *
     * @param array|string $relativePath URI string or array of URI segments.
     * @param string|null  $scheme       URI scheme. E.g., http, ftp. If empty
     *                                   string '' is set, a protocol-relative
     *                                   link is returned.
     */
    function ASD_P4SSK3Y_base_url($relativePath = '', ?string $scheme = null): string
    {
        $baseURL = site_url();

        if ($scheme) {
            $baseURL = set_url_scheme($baseURL, $scheme);
        }

        if (!empty($relativePath)) {
            $baseURL = rtrim($baseURL, '/') . '/' . ltrim($relativePath, '/');
        }
        return $baseURL;
    }
}

if (! function_exists('ASD_P4SSK3Y_asdlog')) {
    /**
     * Logging only DEBUG is on.
     *
     * @param string|null  $logMessage   Log message. If empty string '' is set
     *                                   
     */
    function ASD_P4SSK3Y_asdlog($logMessage = '')
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            if (is_array($logMessage) || is_object($logMessage)) {
                error_log(print_r($logMessage, true));
            } else {
                error_log($logMessage);
            }
        }
    }
}

if (! function_exists('ASD_P4SSK3Y_webid')) {
    /**
     * Returns the base URL as defined by the App config.
     * Base URLs are trimmed site URLs without the index page.
     *
     * @param array|string $relativePath URI string or array of URI segments.
     * @param string|null  $scheme       URI scheme. E.g., http, ftp. If empty
     *                                   string '' is set, a protocol-relative
     *                                   link is returned.
     */
    function ASD_P4SSK3Y_webid(): string
    {
        $ASD_P4SSK3Y_webid =  get_option('asd_p4ssk3y_web_id');
        if (!$ASD_P4SSK3Y_webid) {
            $ASD_P4SSK3Y_webid = bin2hex(random_bytes(32));
            add_option("asd_p4ssk3y_web_id", $ASD_P4SSK3Y_webid);
        }
        return $ASD_P4SSK3Y_webid;
    }
}

if (! function_exists('is_scale_license')) {
    /**
     * Returns the base URL as defined by the App config.
     * Base URLs are trimmed site URLs without the index page.
     *
     * @param array|string $relativePath URI string or array of URI segments.
     * @param string|null  $scheme       URI scheme. E.g., http, ftp. If empty
     *                                   string '' is set, a protocol-relative
     *                                   link is returned.
     */
    function is_scale_license(): bool
    {
        $license =  get_option('asd_p4ssk3y_membership');
        if ($license === "scale") {
            return true;
        }
        return false;
    }
}
if (! function_exists('is_setting_valid')) {
    /**
     * Returns the base URL as defined by the App config.
     * Base URLs are trimmed site URLs without the index page.
     *
     * @param array|string $relativePath URI string or array of URI segments.
     * @param string|null  $scheme       URI scheme. E.g., http, ftp. If empty
     *                                   string '' is set, a protocol-relative
     *                                   link is returned.
     */
    function is_setting_valid($option = '', $value = '',  $callbackvalue = null)
    {
        $settings =  get_option($option);
        if ($settings !== $value) {
            return false;
        }
        if ($callbackvalue !== null) return $callbackvalue;
        return true;
    }
}

if (! function_exists('clean_notices_admin')) {
    /**
     * Returns the base URL as defined by the App config.
     * Base URLs are trimmed site URLs without the index page.
     *
     * @param array|string $relativePath URI string or array of URI segments.
     * @param string|null  $scheme       URI scheme. E.g., http, ftp. If empty
     *                                   string '' is set, a protocol-relative
     *                                   link is returned.
     */
    function clean_notices_admin($slug)
    {
        add_action('admin_notices', function () use ($slug) {
            $current_screen = get_current_screen();
            if ($current_screen->id === $slug) {
                echo '<div class="notice notice-success is-dismissible"><p>Your notification here.</p></div>';
            } else {
                remove_all_actions('admin_notices');
            }
        }, 1);
    }
}
