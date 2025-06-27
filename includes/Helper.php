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
//    public static function retrieveSiteUrl($type)
//    {
//        $debug = self::isDebug();
//        if ($debug === false) {
//            $remove_char = ["https://", "http://", "/"];
//            $url = str_replace($remove_char, "", $get_site_url());
//        } else {
//            $url = "www.wp.rrze.fau.de";
//        }
//        return $url;
//    }

    public static function retrieveSiteUrl()
    {
        if (self::isDebug()) {
            return "www.wp.rrze.fau.de";
        } else {
            $siteUrl = get_site_url();
            $parsedUrl = parse_url($siteUrl);
            return $parsedUrl['host'] ?? null;
        }
    }

    public static function isDebug()
    {
        return defined('WP_DEBUG') && WP_DEBUG;
    }

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
