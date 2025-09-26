<?php

namespace RRZE\WMP;

defined('ABSPATH') || exit;

class Helper
{
    /**
     * Checks if the current environment is 'local' or 'development'.
     *
     * Uses WordPress' wp_get_environment_type() to determine the context.
     * See: https://developer.wordpress.org/reference/functions/wp_get_environment_type/
     *
     * @return bool True if local or development environment, false otherwise.
     */
    public static function isDevelopmentEnvironment(): bool
    {
        return in_array(
            wp_get_environment_type(),
            ['local', 'development'],
            true
        );
    }


    /**
     * Retrieves the current site's domain for WMP API usage.
     *
     * In a local or development environment, it always returns 'www.wp.rrze.fau.de'
     * so developers can test with valid data.
     *
     * @return string|null The domain part of the site URL (e.g., example.com)
     */
    public static function retrieveSiteUrl(): string|null
    {
        if (self::isDevelopmentEnvironment()) {
            return 'www.wp.rrze.fau.de';
        }

        $siteUrl = get_site_url();
        $parsedUrl = parse_url($siteUrl);

        return $parsedUrl['host'] ?? null;
    }






    /**
     * Logs debug information to WordPress debug log file.
     *
     * Writes formatted debug messages to the WordPress debug log when WP_DEBUG
     * is enabled. Supports different log levels (Error, Info, Debug) and handles
     * arrays/objects by converting them to readable string format. Log entries
     * include timestamp, level, filename and message content.
     *
     * @param mixed $input The data to log (string, array, object)
     * @param string $level Log level: 'e'/'error', 'i'/'info', 'd'/'debug'
     * @return void
     * @since 1.0.0
     */
    public static function debug($input, string $level = 'i')
    {
        if (!WP_DEBUG) {
            return;
        }
        if (in_array(strtolower((string)WP_DEBUG_LOG), ['true', '1'], true)) {
            $logPath = WP_CONTENT_DIR . '/debug.log';
        } elseif (is_string(WP_DEBUG_LOG)) {
            $logPath = WP_DEBUG_LOG;
        } else {
            return;
        }
        if (is_array($input) || is_object($input)) {
            $input = print_r($input, true);
        }
        switch (strtolower($level)) {
            case 'e':
            case 'error':
                $level = 'Error';
                break;
            case 'i':
            case 'info':
                $level = 'Info';
                break;
            case 'd':
            case 'debug':
                $level = 'Debug';
                break;
            default:
                $level = 'Info';
        }
        error_log(
            date("[d-M-Y H:i:s \U\T\C]")
            . " WP $level: "
            . basename(__FILE__) . ' '
            . $input
            . PHP_EOL,
            3,
            $logPath
        );
    }


}
