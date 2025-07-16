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
        $apiClient = new ApiClient();
        $this->widget = new Widget($apiClient);
        new Overview($apiClient);

        //Main organises and registers
        add_action('wp_dashboard_setup', [$this, 'addDashboardWidget']);

        // Register styles
        add_action('admin_enqueue_scripts', [$this, 'rrzeWmpEnqueueStyles']);

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
    public function rrzeWmpEnqueueStyles(): void
    {
        wp_enqueue_style(
            'rrze-wmp-styles',
            plugins_url('assets/rrze-wmp.css', plugin()->getBasename()),
            [],
            plugin()->getVersion()
        );
    }

}
