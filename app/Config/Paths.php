<?php

namespace bkarsono\asdpasskeylogin\config;

/**
 * Defines core plugin constants for versioning, database version, plugin name, API URLs, and Web ID.
 * These constants are used throughout the plugin for configuration and integration.
 */
class Paths
{
    /**
     * Path to the core system directory.
     * Change this if you move the core folder.
     *
     * @var string
     */
    public string $systemDirectory = __DIR__ . '/../../vendor/core';

    /**
     * Path to the application directory.
     * Change this if you move or rename the app folder.
     *
     * @var string
     */
    public string $appDirectory = __DIR__ . '/..';
    /**
     * Path to the writable directory.
     * This directory should be writable and is used for logs, cache, etc.
     *
     * @var string
     */
    public string $writableDirectory = __DIR__ . '/../../writable';

    /**
     * Path to the tests directory.
     * Change this if you move or rename the tests folder.
     *
     * @var string
     */
    public string $testsDirectory = __DIR__ . '/../../tests';

    /**
     * Path to the views directory.
     * Change this if you move or rename the views folder.
     *
     * @var string
     */
    public string $viewDirectory = __DIR__ . '/../Views';
}
