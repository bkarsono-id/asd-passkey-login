<?php
if (! function_exists('ASD_P4SSK3Y_webid')) {
      /**
       * Get or generate a unique Web ID for the site.
       *
       * @return string The unique Web ID for the site.
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
