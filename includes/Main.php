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
     * @var Widget Dashboard Widget Instanz
     */
    protected $widget;

    /**
     * @var Settings WMP-Übersichtsseite (umfunktionierte Settings)
     */
    protected $apiClient;


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
        $this->apiClient = new ApiClient();
        $this->overview = new Overview();

        //Main organisiert und registriert
        add_action('wp_dashboard_setup', [$this, 'addDashboardWidget']);
        $this->widget = new Widget();

//        // Admin-Styles laden
//        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);


    }

    public function addDashboardWidget()
    {
        //Main registriert das Widget
        wp_add_dashboard_widget(
            'rrze_wmp_widget',
            'WMP Domain Information',
            [$this->widget, 'render'] //Widget rendert nur
        );
    }










    }
//        $beispiel = ['string1', 'string2', 'string3'];
//        Helper::debug($beispiel);
//
//        // Optionally, you can load the plugin's options here.
//        // This can be useful if you need to access the options early in the plugin lifecycle.
//        $this->options = (object) Options::getOptions();
//        add_filter('plugin_action_links_' . plugin()->getBaseName(), [$this, 'settingsLink']);
//
//        $this->settings = new Settings();
//        // error_log(print_r($this->settings, true));
//
//
//        // Initialize other modules or components as needed.
//        // For example, you can initialize a custom post type, taxonomy, or any other functionality.
//        // $this->initCustomPostType();
//        // $this->initTaxonomy();
//    }

//    /**
//     * Add a settings link to the plugin action links.
//     *
//     * @param array $links
//     * @return array
//     */
//    public function settingsLink($links)
//    {
//        $settingsLink = sprintf(
//            '<a href="%s">%s</a>',
//            admin_url('options-general.php?page=' . $this->settings->getMenuPage()),
//            __('Settings', 'rrze-wmp')
//        );
//        array_unshift($links, $settingsLink);
//        return $links;
//    }
//}

//class Member
//{
//    private $age; // 18
//    private $name; // Hans
//
//    /**
//     * Runs every time the class is instanticated
//     * @param $age int
//     * @param $name string
//     */
//    public function __construct($age=30, $name = "Mensch"){
//        $this->age = $age;
//        $this->name = $name;
//    }
//
//    //getter
//    public function getAge()
//    {
//        return $this->age;
//    }
//
//    public function getName()
//    {
//        return $this->name;
//    }
//
//    //setter
//    public function setAge($age)
//    {
//        $this->age = $age;
//    }
//
//    public function setName($name)
//    {
//        $this->name = $name;
//    }
//}
