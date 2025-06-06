<?php

namespace RRZE\WMP;

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
     * @var \RRZE\WMP\Options
     */
    protected $options;

    /**
     * @var \RRZE\WMP\Settings
     */
    protected $settings;

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
        // Optionally, you can load the plugin's options here.
        // This can be useful if you need to access the options early in the plugin lifecycle.
        $this->options = (object) Options::getOptions();

        add_filter('plugin_action_links_' . plugin()->getBaseName(), [$this, 'settingsLink']);

        $this->settings = new Settings();
        // error_log(print_r($this->settings, true));


        // Initialize other modules or components as needed.
        // For example, you can initialize a custom post type, taxonomy, or any other functionality.
        // $this->initCustomPostType();
        // $this->initTaxonomy();
    }

    /**
     * Add a settings link to the plugin action links.
     * 
     * @param array $links
     * @return array
     */
    public function settingsLink($links)
    {
        $settingsLink = sprintf(
            '<a href="%s">%s</a>',
            admin_url('options-general.php?page=' . $this->settings->getMenuPage()),
            __('Settings', 'rrze-wmp')
        );
        array_unshift($links, $settingsLink);
        return $links;
    }
}