<?php

namespace RRZE\WMP;

defined('ABSPATH') || exit;

/**
 * Dashboard Widget Class
 *
 * Shows compact WMP information on dashboard
 *
 * @package RRZE\WMP
 * @since 1.0.0
 */
class Widget
{
    /**
     * @var ApiClient WMP API Client
     */
    protected $apiClient;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->apiClient = new ApiClient();
    }

    /**
     * Render dashboard widget
     *
     * @return void
     */
    public function render()
    {
        // determine current domain
        $currentDomain = $this->getCurrentDomain();

        if (!$currentDomain) {
            echo '<p>' . __('Could not determine current domain.', 'rrze-wmp') . '</p>';
            return;
        }

        try {
            $data = $this->apiClient->getDomainData($currentDomain);
            $this->renderWidgetContent($data, $currentDomain);
        } catch (Exception $e) {
            echo '<div>';
            echo '<p><strong>' . __('Error loading WMP data:', 'rrze-wmp') . '</strong></p>';
            echo '<p>' . esc_html($e->getMessage()) . '</p>';
            echo '</div>';
        }
    }

    /**
     * Render widget content
     *
     * @param array $data WMP-Daten
     * @param string $domain Aktuelle Domain
     * @return void
     */
    protected function renderWidgetContent(array $data, string $domain)
    {
        echo '<div class="rrze-wmp-widget">';

        // Basic information
        echo '<table class="rrze-wmp-widget-table">';
        echo '<tr><td><strong>' . __('ID:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['id'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Customer number:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['instanz']['kunu'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Domain:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data ['servername'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Server:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['server'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Responsible:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['persons']['responsible']['name'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Responsible-Email:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['persons']['responsible']['email'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Webmaster:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['persons']['webmaster']['name'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Webmaster-Email:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['persons']['webmaster']['email'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Active since:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['aktivseit'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Dienste:', 'rrze-wmp') . '</strong></td><td>';
        if (!empty($data['instanz']['dienste']) && is_array($data['instanz']['dienste'])) {
            echo esc_html(implode(', ', $data['instanz']['dienste']));
        } else {
            echo 'N/A';
        }

        echo '</table>';

        // Link to admin overview page
        $detailUrl = admin_url('admin.php?page=rrze-wmp-overview');
        echo '<p><a href="' . esc_url($detailUrl) . '" class="button button-primary">' . __('More Details', 'rrze-wmp') . '</a></p>';

        echo '</div>';
    }

    /**
     * Search for current domain
     *
     * @return string|null
     */

    protected function getCurrentDomain()
    {
        return Helper::retrieveSiteUrl();
    }

}