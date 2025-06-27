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
        $this->widget = new Widget();
        $this->overview = new Overview();

        //Main organises and registers
        add_action('wp_dashboard_setup', [$this, 'addDashboardWidget']);
        $this->widget = new Widget();

        // Register styles
        add_action('admin_enqueue_scripts', [$this, 'rrze_wmp_enqueue_styles']);

//        // load Admin-Styles
//        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);


    }

    public function addDashboardWidget()
    {
        //Main registers the widget
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


}
