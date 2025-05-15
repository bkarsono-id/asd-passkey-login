<?php

namespace Asd\Models;

if (!defined('ABSPATH')) exit;

class GeneralModel
{
    private $table_name;
    public $num_rows = 0;
    /**
     * Constructor
     *
     * @param string $table_name Custom table name without prefix.
     */
    public function __construct($table_name)
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . $table_name;
    }


    /**
     * Create a custom database table for passkey data.
     *
     * This method defines and creates a custom table in the WordPress database
     * using the `dbDelta` function to ensure compatibility and safe table creation.
     * If the table already exists, it will be updated to match the defined structure.
     *
     * @return bool True on success, false on failure.
     */

    public function createTable()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql = "CREATE TABLE {$this->table_name} (
            id mediumint(11) NOT NULL AUTO_INCREMENT,
            user tinytext NOT NULL,
            email varchar(100) NOT NULL,
            user_handle varchar(255) NOT NULL,
            authenticator varchar(255) NOT NULL,
            roles varchar(255) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta($sql);

        if ($wpdb->last_error) {
            // phpcs:disable WordPress.PHP.DevelopmentFunctions
            asdlog('Database Error: ' . $wpdb->last_error);
            return false;
        }

        return true;
    }
    /**
     * Insert data into the custom table.
     *
     * @param array $data Data to insert as key-value pairs.
     * @param array $format Format for the data types (e.g., %s, %d).
     * @return int|false Insert ID on success, false on failure.
     */
    public function insert($data, $format = [])
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $wpdb->insert($this->table_name, $data, $format);
        if ($wpdb->last_error) {
            asdlog('Database Insert Error: ' . $wpdb->last_error);
            return false;
        }

        return $wpdb->insert_id;
    }

    /**
     * Get a single row by ID.
     *
     * @param int $id Row ID to retrieve.
     * @return array|false The row data as an associative array, or false on failure.
     */
    public function get_by_id($id)
    {
        global $wpdb;
        $cache_key = "custom_query_{$this->table_name}_{$id}";
        $result = wp_cache_get($cache_key, 'custom_cache_group');
        if ($result === false) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            $result = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM %i WHERE id = %d", [$this->table_name, $id]),
                ARRAY_A
            );
            if ($result) {
                wp_cache_set($cache_key, $result, 'custom_cache_group', HOUR_IN_SECONDS);
            }
        }
        if ($wpdb->last_error) {
            asdlog('Database Select Error: ' . $wpdb->last_error);
            return false;
        }

        return $result;
    }

    /**
     * Update a row by ID.
     *
     * @param int   $id Row ID to update.
     * @param array $data Data to update as key-value pairs.
     * @param array $format Format for the data types (e.g., %s, %d).
     * @return int|false Number of rows updated on success, false on failure.
     */
    public function update($id, $data, $format = [])
    {
        global $wpdb;

        $where = ['id' => $id];
        $where_format = ['%d'];
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $result = $wpdb->update($this->table_name, $data, $where, $format, $where_format);

        if ($wpdb->last_error) {
            asdlog('Database Update Error: ' . $wpdb->last_error);
            return false;
        }

        // Cache Invalidation
        $cache_key = "custom_query_{$this->table_name}_{$id}";
        wp_cache_delete($cache_key, 'custom_cache_group');

        $this->num_rows = is_array($result) ? count($result) : 0;
        return $result;
    }


    /**
     * Delete a row by ID.
     *
     * @param int $id Row ID to delete.
     * @return int|false Number of rows deleted on success, false on failure.
     */
    public function delete($id)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $result = $wpdb->delete($this->table_name, ['id' => $id], ['%d']);

        if ($wpdb->last_error) {
            asdlog('Database Delete Error: ' . $wpdb->last_error);
            return false;
        }
        // Cache Invalidation
        $cache_key = "custom_query_{$this->table_name}_{$id}";
        wp_cache_delete($cache_key, 'custom_cache_group');
        return $result;
    }

    /**
     * Get multiple rows with optional conditions.
     *
     * @param array  $where Associative array for WHERE conditions.
     * @param string $orderby Column name to order by.
     * @param string $order Sort order (ASC or DESC).
     * @return array|false List of rows as associative arrays, or false on failure.
     */
    public function get_all($where = [], $orderby = 'id', $order = 'ASC')
    {
        global $wpdb;
        $cache_key = 'get_all_' . md5(json_encode($where) . "_{$orderby}_{$order}");
        $cache_group = 'custom_cache_group';

        // Try to get the results from cache
        $cached_results = wp_cache_get($cache_key, $cache_group);
        if ($cached_results !== false) {
            return $cached_results;
        }
        $conditions = [];
        $values = [];

        foreach ($where as $column => $value) {
            $conditions[] = $column . ' = %s';
            $values[] = $value;
        }

        $where_clause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM %i %s %s ORDER BY %s %s ", [$this->table_name, $where_clause, $orderby, $order, $values]),
            ARRAY_A
        );

        if ($wpdb->last_error) {
            asdlog('Database Select Error: ' . $wpdb->last_error);
            return false;
        }
        wp_cache_set($cache_key, $results, $cache_group, HOUR_IN_SECONDS);

        $this->num_rows = is_array($results) ? count($results) : 0;
        return $results;
    }

    /**
     * Retrieve a single row from the custom table based on the user handle.
     *
     * This method fetches a single row from the database where the `user_handle`
     * matches the provided value. It uses WordPress's `$wpdb->prepare` to ensure
     * the query is secure and prevents SQL injection.
     *
     * @param string $user_handle The user handle to search for in the table.
     * @return array|false An associative array of the retrieved row on success, or false on failure.
     */

    public function getByUserHandle($user_handle)
    {
        global $wpdb;
        // Create a unique cache key
        $cache_key = 'get_by_user_handle_' . md5($user_handle);
        $cache_group = 'custom_cache_group';

        // Check if the result is cached
        $cached_result = wp_cache_get($cache_key, $cache_group);
        if ($cached_result !== false) {
            return $cached_result;
        }
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE user_handle = %s", [$this->table_name, $user_handle]), ARRAY_A);

        if ($wpdb->last_error) {
            asdlog('Database Error: ' . $wpdb->last_error);
            return false;
        }
        // Cache the result for future use
        wp_cache_set($cache_key, $result, $cache_group, HOUR_IN_SECONDS);
        return $result;
    }
}
