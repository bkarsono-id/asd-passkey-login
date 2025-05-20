<?php

namespace bkarsono\asdpasskeylogin\controllers;

if (!defined('ABSPATH')) exit;
if (!class_exists(SendNotificationAdmin::class)) {
      class SendNotificationAdmin extends BaseController
      {
            public function __construct()
            {
                  add_action('wp_ajax_asd_send_notification', [$this, 'handleSendNotification']);
                  ASD_P4SSK3Y_clean_notices_admin("asd-send-notification-admin");
                  add_action('wp_ajax_asd_woocommerce_products', [$this, 'handleProductList']);
                  add_action('wp_ajax_nopriv_asd_woocommerce_products', [$this, 'handleProductList']);
            }
            public function index()
            {
                  $data = [
                        "logo" => ASD_P4SSK3Y_PUBLICURL . 'img/logo-medium.webp',
                        "show" => ASD_P4SSK3Y_is_setting_valid("asd_admin_password_confirmation", "N", "none"),
                        "url" => get_bloginfo('url')
                  ];
                  ASD_P4SSK3Y_view("asd-send-notification", $data);
            }
            public function handleProductList()
            {
                  if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                        wp_send_json_error(['message' => 'Invalid request method']);
                        exit;
                  }

                  $_wpnonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
                  if (!isset($_wpnonce) || !wp_verify_nonce($_wpnonce, 'asd_product_nonce')) {
                        wp_send_json_error(['message' => 'Nonce verification failed']);
                        exit;
                  }

                  $args = [
                        'post_type'      => 'product',
                        'post_status'    => 'publish',
                        'posts_per_page' => -1,
                  ];
                  $products = get_posts($args);

                  if (empty($products)) {
                        wp_send_json_error(['message' => 'No products found']);
                        return;
                  }

                  $data = [];
                  foreach ($products as $product) {
                        $product_obj = wc_get_product($product->ID);

                        $data[] = [
                              'id'         => $product->ID,
                              'name'       => $product->post_title,
                              'url'        => get_permalink($product->ID),
                              'price'      => $product_obj->get_price() ? wc_price($product_obj->get_price()) : 'N/A', // Format harga
                              'categories' => wp_get_post_terms($product->ID, 'product_cat', ['fields' => 'names']), // Nama kategori
                              'tags'       => wp_get_post_terms($product->ID, 'product_tag', ['fields' => 'names']), // Nama tags
                        ];
                  }

                  wp_send_json_success($data);
            }


            public function handleSendNotification()
            {
                  if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                        wp_send_json_error(['message' => 'Invalid request method']);
                        exit;
                  }

                  $notificationTitle  = isset($_POST['notificationTitle']) ? sanitize_text_field(wp_unslash($_POST['notificationTitle'])) : '';
                  $notificationBody  = isset($_POST['notificationBody']) ? sanitize_text_field(wp_unslash($_POST['notificationBody'])) : '';
                  $notificationUrl  = isset($_POST['notificationUrl']) ? sanitize_text_field(wp_unslash($_POST['notificationUrl'])) : '';

                  $_wpnonce  = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
                  if (!isset($_wpnonce) || !wp_verify_nonce($_wpnonce, 'asd_send_notification')) {
                        wp_send_json_error(['message' => 'Nonce verification failed']);
                        exit;
                  }
                  if (empty($notificationTitle) || empty($notificationBody) || empty($notificationUrl)) {
                        wp_send_json_error(['message' => 'All fields are required.']);
                        exit;
                  }
                  $dataMessage = [
                        "notificationTitle" => $notificationTitle,
                        "notificationBody" => $notificationBody,
                        "notificationUrl" => $notificationUrl,
                        "notificationTime" => "now"
                  ];
                  $data = [
                        'domain' => get_bloginfo('url'),
                        'platform' => "wordpress",
                        'action' => 'sent-notification',
                        'filter' => null,
                        'data' => json_encode($dataMessage)
                  ];
                  $response = wp_remote_post(ASD_P4SSK3Y_WEBPUSH_URL . "/clientQuery", [
                        'method'    => 'POST',
                        'body'      => json_encode($data),
                        'headers'   => [
                              'Content-Type' => 'application/json',
                              'Authorization' => 'Bearer ' . get_option('asd_p4ssk3y_key1'),
                        ],
                        'timeout'   => 30,
                  ]);
                  if (is_wp_error($response)) {
                        $error_message = $response->get_error_message();
                        ASD_P4SSK3Y_asdlog("[ASD onActivation] API Error: $error_message");
                        wp_send_json_error(['message' => 'Failed to connect to the API', 'error' => $error_message]);
                        exit;
                  }

                  $body = wp_remote_retrieve_body($response);
                  $data = json_decode($body, true);
                  if (json_last_error() !== JSON_ERROR_NONE) {
                        wp_send_json_error(['message' => 'Failed to decode API response']);
                        exit;
                  }

                  if (isset($data['serverStatus']) && $data['serverStatus'] === 'error') {
                        ASD_P4SSK3Y_asdlog("Sync Package Error: " . esc_html($data['serverMessage']));
                        wp_send_json_error(['message' => 'API Error', 'details' => esc_html($data['serverMessage'])]);
                        exit;
                  }
                  wp_send_json_success(['message' => "Notification sent succesfull."]);
                  return;
            }
      }
}
