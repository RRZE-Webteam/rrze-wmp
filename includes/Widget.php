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
     * Constructor for the Widget class.
     * The shared ApiClient instance is passed in from the outside.
     */
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
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

        echo '<div class="rrze-wmp-widget">';

        // Basic information
        echo '<table class="rrze-wmp-widget-table">';
        echo '<tr><td>' . __('ID:', 'rrze-wmp') . '</td><td>' . esc_html($data['id'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>' . __('Customer number:', 'rrze-wmp') . '</td><td>' . $kunu_link . '</td></tr>';
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