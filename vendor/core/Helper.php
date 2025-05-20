<?php
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
