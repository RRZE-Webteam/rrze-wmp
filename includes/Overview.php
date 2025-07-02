<?php

namespace RRZE\WMP;

defined('ABSPATH') || exit;

/**
 * WMP Overview Page
 *
 * Shows detailed WMP information
 *
 * @package RRZE\WMP
 * @since 1.0.0
 */
class Overview
{
    /**
     * @var ApiClient WMP API Client
     */
    protected $apiClient;

    /**
     * Menu page slug for WMP Overview
     *
     * @var string
     */
    protected $menuPage = 'rrze-wmp-overview';

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiClient = new ApiClient();
        add_action('admin_menu', [$this, 'adminMenu']);

    }

    /**
     * Get the menu page slug
     *
     * @return string
     */
    public function getMenuPage(): string
    {
        return $this->menuPage;
    }

    /**
     * Add Admin Menu
     *
     * @return void
     */
    public function adminMenu()
    {
        add_menu_page(
            __('WMP Overview', 'rrze-wmp'),
            __('RRZE', 'rrze-wmp'),
            'read',
            $this->menuPage,
            [$this, 'overviewPage'],
            'dashicons-admin-site-alt3',
            30
        );
    }


    /**
     * Show WMP-Overview
     *
     * @return void
     */
    public function overviewPage()
    {
        $currentDomain = Helper::retrieveSiteUrl();

        echo '<div class="wrap">';
        echo '<h1>' . esc_html(__('RRZE WMP Domain Information', 'rrze-wmp')) . '</h1>';

        if (!$currentDomain) {
            echo '<div class="notice notice-warning"><p>' . __('Could not determine current domain.', 'rrze-wmp') . '</p></div>';
            echo '</div>';
            return;
        }

        $wmpData = $this->apiClient->getDomainData($currentDomain);

        echo '<h2>' . sprintf(__('Information for: %s', 'rrze-wmp'), esc_html($currentDomain)) . '</h2>';

        if (empty($wmpData)) {
            echo '<div class="notice notice-error"><p>' . __('No WMP data available for this domain.', 'rrze-wmp') . '</p></div>';
        } else {
            $this->renderDetailedView($wmpData);
        }

        echo '</div>';
    }

    /**
     * Render detailed WMP data view
     *
     * @param array $data WMP data
     * @return void
     */
    protected function renderDetailedView(array $data)
    {

        // Container
        echo '<div class="rrze-wmp-layout-container">';

        // Basic Information
        echo '<div class="rrze-wmp-section rrze-wmp-basic-content">';
        echo '<h3>' . __('Basic Information', 'rrze-wmp') . '</h3>';
        echo '<table class="rrze-wmp-overview-table">';
        echo '<tr><td>' . __('ID:', 'rrze-wmp') . '</td><td>' . esc_html($data['id'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Customer number:', 'rrze-wmp') . '</td><td>' . esc_html($data['instanz']['kunu'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Server Name:', 'rrze-wmp') . '</td><td>' . esc_html($data ['servername'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Server:', 'rrze-wmp') . '</td><td>' . esc_html($data['server'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Primary Domain:', 'rrze-wmp') . '</td><td>' . esc_html($data ['instanz']['primary_domain']) . '</td></tr>';
        echo '<tr><td>' . __('Website Title:', 'rrze-wmp') . '</td><td>' . esc_html($data ['instanz']['title'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Responsible:', 'rrze-wmp') . '</td><td>' . esc_html($data['persons']['responsible']['name'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Responsible Email:', 'rrze-wmp') . '</td><td>' . esc_html($data['persons']['responsible']['email'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Webmaster:', 'rrze-wmp') . '</td><td>' . esc_html($data['persons']['webmaster']['name'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Webmaster Email:', 'rrze-wmp') . '</td><td>' . esc_html($data['persons']['webmaster']['email'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Active since:', 'rrze-wmp') . '</td><td>' . esc_html($data['aktivseit'] ?? 'N/A') . '</td></tr>';
        echo '</table>';
        echo '</div>';


        // Contact Box
        echo '<div class="rrze-wmp-section rrze-wmp-contact">';
        echo '<h3>' . __('Web Support', 'rrze-wmp') . '</h3>';
        echo '<div class="rrze-wmp-contact-box">';


        echo '<p>' . __('You need help with your website? Please contact us!', 'rrze-wmp') . '</p>';
        echo '<a href="mailto:webmaster@fau.de" class="button button-primary">' . __('Contact', 'rrze-wmp') . '</a>';


        echo '</div>';
        echo '</div>';


        echo '</div>';

        // Domain-Alias
        if (!empty($data['serveralias']) && is_array($data['serveralias'])) {
            echo '<div class="rrze-wmp-section">';
            echo '<h3>' . __('Domain Aliases', 'rrze-wmp') . '</h3>';
            echo '<ul class="rrze-wmp-overview-list">';
            foreach ($data['serveralias'] as $alias) {
                echo '<li>' . esc_html($alias) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }


        // Booked Services
        if (!empty($data['instanz']['dienste']) && is_array($data['instanz']['dienste'])) {
            echo '<div class="rrze-wmp-section">';
            echo '<h3>' . __('Booked Services', 'rrze-wmp') . '</h3>';
            echo '<ul class="rrze-wmp-overview-list">';
            foreach ($data['instanz']['dienste'] as $dienst) {
                echo '<li>' . esc_html($dienst) . '</li>';
            }
            echo '</ul>';
            echo '</div>';

        }
    }


}