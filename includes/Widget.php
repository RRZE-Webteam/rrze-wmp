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
        $currentDomain = $this->getCurrentDomain();

        if (empty($currentDomain)) {
            echo '<p>' . __('Could not determine current domain.', 'rrze-wmp') . '</p>';
            return;
        }
        $data = $this->apiClient->getDomainData($currentDomain);
        $this->renderWidgetContent($data, $currentDomain);
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
        echo '<tr><td>' . __('ID:', 'rrze-wmp') . '</td><td>' . esc_html($data['id'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Customer number:', 'rrze-wmp') . '</td><td>' . esc_html($data['instanz']['kunu'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Domain:', 'rrze-wmp') . '</td><td>' . esc_html($data ['servername'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Server:', 'rrze-wmp') . '</td><td>' . esc_html($data['server'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Responsible:', 'rrze-wmp') . '</td><td>' . esc_html($data['persons']['responsible']['name'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Responsible-Email:', 'rrze-wmp') . '</td><td>' . esc_html($data['persons']['responsible']['email'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Webmaster:', 'rrze-wmp') . '</td><td>' . esc_html($data['persons']['webmaster']['name'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Webmaster-Email:', 'rrze-wmp') . '</td><td>' . esc_html($data['persons']['webmaster']['email'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Active since:', 'rrze-wmp') . '</td><td>' . esc_html($data['aktivseit'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Dienste:', 'rrze-wmp') . '</td><td>';
        if (!empty($data['instanz']['dienste']) && is_array($data['instanz']['dienste'])) {
            echo esc_html(implode(', ', $data['instanz']['dienste']));
        } else {
            echo 'N/A';
        }
        echo '</td></tr>';
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
    protected function getCurrentDomain(): string|null
    {
        return Helper::retrieveSiteUrl();
    }

}