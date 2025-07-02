<?php

namespace RRZE\WMP;

defined('ABSPATH') || exit;

class Helper
{

    /**
     * Determine which url should be used and return the string
     *
     * @param string $type
     * @return string
     */
    public static function retrieveSiteUrl(): string|null
    {
        if (self::isDebug()) {
            return "www.wp.rrze.fau.de";
        } else {
            $siteUrl = get_site_url();
            $parsedUrl = parse_url($siteUrl);
            return $parsedUrl['host'] ?? null;
        }
    }


    /**
     * Determine which url should be used and return the string
     *
     * @param string $type
     * @return string
     */
    public static function isDebug()
    {
        return defined('WP_DEBUG') && WP_DEBUG;
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
