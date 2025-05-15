<?php
if (! function_exists('webid')) {
    /**
     * Returns the base URL as defined by the App config.
     * Base URLs are trimmed site URLs without the index page.
     *
     * @param array|string $relativePath URI string or array of URI segments.
     * @param string|null  $scheme       URI scheme. E.g., http, ftp. If empty
     *                                   string '' is set, a protocol-relative
     *                                   link is returned.
     */
    function webid(): string
    {
        $webid =  get_option('asd_web_id');
        if (!$webid) {
            $webid = bin2hex(random_bytes(32));
            add_option("asd_web_id", $webid);
        }
        return $webid;
    }
}
