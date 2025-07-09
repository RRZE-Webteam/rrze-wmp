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
     * Constructor for the Overview class.
     * The shared ApiClient is passed in by the Main class.
     *
     * @param ApiClient $apiClient The shared API client instance.
     */
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
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

        echo '<h2>' . sprintf(__('Website: %s', 'rrze-wmp'), esc_html($currentDomain)) . '</h2>';

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
//formatting data from activesince
        $aktivseit = $data['aktivseit'] ?? 'N/A';
        if ($aktivseit !== 'N/A') {
            $date = new \DateTime($aktivseit);
            $formatted_date = $date->format('d.m.Y');
        } else {
            $formatted_date = 'N/A';
        }

        //link to wmp customer number
        $kunu = $data['instanz']['kunu'] ?? 'N/A';
        if ($kunu !== 'N/A') {
            $kunu_link = '<a href="https://www.wmp.rrze.fau.de/search/kunu/' . urlencode($kunu) . '/show" target="_blank">' . esc_html($kunu) . '</a>';
        } else {
            $kunu_link = 'N/A';
        }

        // Layout Container
        echo '<div class="rrze-wmp-layout-container">';

        // Basic Information
        // Responsible
        $responsible_name = $data['persons']['responsible']['name'] ?? 'N/A';
        $responsible_email = $data['persons']['responsible']['email'] ?? 'N/A';
        if ($responsible_email !== 'N/A') {
            $responsible_display = esc_html($responsible_name) . ' (<a href="mailto:' . esc_attr($responsible_email) . '">' . esc_html($responsible_email) . '</a>)';
        } else {
            $responsible_display = esc_html($responsible_name . ' (' . $responsible_email . ')');
        }

// Webmaster
        $webmaster_name = $data['persons']['webmaster']['name'] ?? 'N/A';
        $webmaster_email = $data['persons']['webmaster']['email'] ?? 'N/A';
        if ($webmaster_email !== 'N/A') {
            $webmaster_display = esc_html($webmaster_name) . ' (<a href="mailto:' . esc_attr($webmaster_email) . '">' . esc_html($webmaster_email) . '</a>)';
        } else {
            $webmaster_display = esc_html($webmaster_name . ' (' . $webmaster_email . ')');
        }


        echo '<div class="rrze-wmp-section rrze-wmp-basic-content">';
        echo '<div class="container-header">';
        echo '<h3>' . __('Basic Information', 'rrze-wmp') . '</h3>';
        echo '</div>';
        echo '<div class="container-body">';
        echo '<table class="rrze-wmp-overview-table">';
        echo '<tr><td>' . __('ID:', 'rrze-wmp') . '</td><td>' . esc_html($data['id'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Customer number:', 'rrze-wmp') . '</td><td>' . $kunu_link . '</td></tr>';
        echo '<tr><td>' . __('Server Name:', 'rrze-wmp') . '</td><td>' . esc_html($data ['servername'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Server:', 'rrze-wmp') . '</td><td>' . esc_html($data['server'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Host Name:', 'rrze-wmp') . '</td><td>' . esc_html($data['instanz']['hostname'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Primary Domain:', 'rrze-wmp') . '</td><td>' . esc_html($data ['instanz']['primary_domain'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Website Title:', 'rrze-wmp') . '</td><td>' . esc_html($data ['instanz']['title'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Administration Email:', 'rrze-wmp') . '</td><td>' . esc_html($data ['instanz']['adminemail'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Responsible:', 'rrze-wmp') . '</td><td>' . $responsible_display . '</td></tr>';
        echo '<tr><td>' . __('Webmaster:', 'rrze-wmp') . '</td><td>' . $webmaster_display . '</td></tr>';
        echo '<tr><td>' . __('Active since:', 'rrze-wmp') . '</td><td>' . esc_html($formatted_date) . '</td></tr>';
        echo '<tr><td>' . __('Booked Services:', 'rrze-wmp') . '</td><td>';
        if (!empty($data['instanz']['dienste']) && is_array($data['instanz']['dienste'])) {
            echo esc_html(implode(', ', $data['instanz']['dienste']));
        } else {
            echo 'N/A';
        }
        echo '</td></tr>';
        echo '</table>';
        echo '</div>';
        echo '</div>';


        // Contact Box

        // Get domain data for email
        $domain_name = $data['instanz']['primary_domain'] ?? 'N/A';
        $domain_id = $data['id'] ?? 'N/A';

        // Build mailto link with pre-filled subject and body
        $subject = 'Anfrage zur Website ' . $domain_name;
        $body = 'Domain: ' . $domain_name . '%0D%0A' . 'ID: ' . $domain_id . '%0D%0A' . 'Unser Anliegen:';
        $mailto_link = 'mailto:webmaster@fau.de?subject=' . rawurlencode($subject) . '&body=' . $body;


        echo '<div class="rrze-wmp-section rrze-wmp-contact">';
        echo '<div class="container-header">';
        echo '<h3>' . __('Web Support', 'rrze-wmp') . '</h3>';
        echo '</div>';
        echo '<div class="container-body">';
        echo '<div class="rrze-wmp-contact-box">';
        echo '<p>' . __('You need help with your website? Please contact us!', 'rrze-wmp') . '</p>';
        echo '<a href="' . $mailto_link . '" class="button button-primary">' . __('Contact', 'rrze-wmp') . '</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        echo '</div>';

        echo '<div class="rrze-wmp-layout-container">';
        // Domain-Alias
        if (!empty($data['serveralias']) && is_array($data['serveralias'])) {
            echo '<div class="rrze-wmp-section rrze-wmp-alias">';
            echo '<div class="container-header">';
            echo '<h3>' . __('Domain Aliases', 'rrze-wmp') . '</h3>';
            echo '</div>';
            echo '<div class="container-body">';
            echo '<ul class="rrze-wmp-overview-list">';
            foreach ($data['serveralias'] as $alias) {
                echo '<li>' . esc_html($alias) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            echo '</div>';
        }


        // Web Master Portal

        if (!empty($data['instanz']['dienste']) && is_array($data['instanz']['dienste'])) {
            echo '<div class="rrze-wmp-section rrze-wmp-portal">';
            echo '<div class="container-header">';
            echo '<h3 class="portal-container-header">' . __('Web Master Portal', 'rrze-wmp') . '<a href="https://www.wmp.rrze.fau.de" target="_blank" title="' . __('Open WMP', 'rrze-wmp') . '">
        <span class="dashicons dashicons-external"></span>
    </a>' . ' </h3>';

            echo '</div>';
            echo '<div class="container-body">';
            echo '<div class="rrze-wmp-info-box">';
            echo '<h4>' . __('Notes on your domain ', 'rrze-wmp') . '</h4>';
            $domain_id = $data['id'] ?? '';
            if ($domain_id) {
                echo '<p><a href="https://www.wmp.rrze.fau.de/domain/' . $domain_id . '/notiz" target="_blank">' . __('View Notes', 'rrze-wmp') . '</a></p>';
            }
            echo '</div>';
            echo '<div class="rrze-wmp-info-box">';
            echo '<h4>' . __('Logs on your domain ', 'rrze-wmp') . '</h4>';
            $domain_id = $data['id'] ?? '';
            if ($domain_id) {
                echo '<p><a href="https://www.wmp.rrze.fau.de/domain/' . $domain_id . '/log" target="_blank">' . __('View Logs', 'rrze-wmp') . '</a></p>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';

        }
        echo '</div>';
    }


}