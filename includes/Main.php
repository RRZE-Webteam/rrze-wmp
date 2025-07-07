<?php

namespace RRZE\WMP;

use RRZE\WMP\Helper;

defined('ABSPATH') || exit;

/**
 * Main class for the RRZE WMP Plugin.
 *
 * This class serves as the main entry point for the plugin, handling initialization and settings.
 * It sets up hooks, initializes options, and provides a settings link in the plugin action links.
 *
 * @package RRZE\WMP
 * @since 1.0.0
 */
class Main
{
    /**
     * @var Widget Dashboard Widget Instance
     */
    protected $widget;


    /**
     * Initialize the plugin.
     *
     * This method is called when the plugin is loaded.
     * It sets up the necessary hooks and initializes the modules based on the configuration.
     *
     * @return void
     */
    public function loaded()
    {
        $this->apiClient = new ApiClient();
        $this->widget = new Widget($this->apiClient);
        $this->overview = new Overview($this->apiClient);

        //Main organises and registers
        add_action('wp_dashboard_setup', [$this, 'addDashboardWidget']);

        // Register styles
        add_action('admin_enqueue_scripts', [$this, 'rrze_wmp_enqueue_styles']);

    }

    /**
     * Adds the dashboard widget
     *
     * @return void
     */
    public function addDashboardWidget()
    {
        wp_add_dashboard_widget(
            'rrze_wmp_widget',
            'RRZE WMP Domain Information',
            [$this->widget, 'render']
        );
    }

    /**
     * Enqueue plugin styles
     * Called by WordPress hook 'admin_enqueue_scripts'
     */
    public function rrze_wmp_enqueue_styles(): void
    {
        wp_enqueue_style(
            'rrze-wmp-styles',
            plugin_dir_url(__FILE__) . '../assets/rrze-wmp.css',
            array(),
            '1.0.0'
        );
    }

    /**
     * *
     * Retrieves domain data from WMP API with 24-hour caching.
     * Checks for cached data first, returns it if available and not expired.
     * If no cache exists, fetches fresh data from WMP API and stores it
     * in transients for 24 hours to improve performance.
     *
     * @param string $domain The domain name to fetch data for
     * @return array|false Domain data array on success, false on failure
     * @since 1.0.0
     */
    private function get_domain_data($domain)
    {
        $transient_key = 'rrze_wmp_domain_' . md5($domain);
        $cached_data = get_transient($transient_key);

        if ($cached_data !== false) {
            return $cached_data;
        }

        $api_data = $this->call_wmp_api($domain);

        set_transient($transient_key, $api_data, DAY_IN_SECONDS);

        return $api_data;
    }

}
