<?php
namespace RRZE\WMP;

defined('ABSPATH') || exit;

/**
 * Dashboard Widget Klasse
 *
 * Zeigt kompakte WMP-Informationen im Dashboard
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
     * Dashboard Widget rendern
     *
     * @return void
     */
    public function render()
    {
        // determine current domain
        $currentDomain = $this ->getCurrentDomain();

        if (!$currentDomain) {
            echo '<p>' . __('Could not determine current domain.', 'rrze-wmp') . '</p>';
            return;
        }

        try {
            $data = $this->apiClient->getDomainData($currentDomain);
            $this->renderWidgetContent($data, $currentDomain);
        } catch (Exception $e) {
            echo '<div class="rrze-wmp-error">';
            echo '<p><strong>' . __('Error loading WMP data:', 'rrze-wmp') . '</strong></p>';
            echo '<p>' . esc_html($e->getMessage()) . '</p>';
            echo '</div>';
        }
    }

    /**
     * Widget-Inhalt rendern
     *
     * @param array $data WMP-Daten
     * @param string $domain Aktuelle Domain
     * @return void
     */
    protected function renderWidgetContent(array $data, string $domain)
    {
        echo '<div class="rrze-wmp-widget">';

        // basic information
        echo '<table class="rrze-wmp-widget-table">';
        echo '<tr><td><strong>' . __('ID:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['id'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Domain:', 'rrze-wmp') . '</strong></td><td>' . esc_html($domain) . '</td></tr>';
        echo '<tr><td><strong>' . __('Server:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['server'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Responsible:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['persons']['responsible']['name'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Responsible Email:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['persons']['responsible']['email'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Webmaster:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['persons']['webmaster']['name'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Webmaster Email:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['persons']['webmaster']['email'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Active since:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['aktivseit'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Dienste:', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['instanz']['dienste']['0'] ?? 'N/A') . '</td></tr>';

        echo '</table>';

        // link to admin overview page
        $detailUrl = admin_url('admin.php?page=rrze-wmp-overview');
        echo '<p><a href="' . esc_url($detailUrl) . '" class="button button-secondary">' . __('More', 'rrze-wmp') . '</a></p>';

        echo '</div>';
    }

    /**
     * search for current domain
     *
     * @return string|null
     */

    protected function getCurrentDomain()
    {
        return Helper::retrieveSiteUrl('some_type');
    }

//    protected function getCurrentDomain(): ?string
//    {
//        $siteUrl = get_site_url();
//        $parsedUrl = parse_url($siteUrl);
//        return $parsedUrl['host'] ?? null;
//    }
}