<?php
namespace RRZE\WMP;

defined('ABSPATH') || exit;

/**
 * WMP Übersichtsseite
 *
 * Zeigt ausführliche WMP-Informationen an
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
     * Menu page slug für die WMP-Übersichtsseite
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
     * Admin-Menü hinzufügen
     *
     * @return void
     */
    public function adminMenu()
    {
        add_menu_page(
            __('WMP Overview', 'rrze-wmp'),    // Seitentitel
            __('WMP', 'rrze-wmp'),             // Menütitel
            'manage_options',                   // Berechtigung
            $this->menuPage,                   // Slug
            [$this, 'overviewPage'],          // Callback
            'dashicons-admin-site-alt3',      // Icon
            30                                 // Position
        );
    }

    /**
     * WMP-Übersichtsseite anzeigen
     *
     * @return void
     */
    public function overviewPage()
    {
        $currentDomain = Helper::retrieveSiteUrl();

        echo '<div class="wrap">';
        echo '<h1>' . esc_html(__('WMP Domain Information', 'rrze-wmp')) . '</h1>';

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
     * Detaillierte WMP-Datenansicht rendern
     *
     * @param array $data WMP-Daten
     * @return void
     */
    protected function renderDetailedView(array $data)
    {
        echo '<div class="rrze-wmp-overview">';

        // Server-Informationen
        echo '<div class="rrze-wmp-section">';
        echo '<h3>' . __('Server Information', 'rrze-wmp') . '</h3>';
        echo '<table class="widefat">';
        echo '<tr><td><strong>' . __('Server', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['server'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Domain', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['servername'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Webmaster', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['serveradmin'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Status', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['status'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td><strong>' . __('Active since', 'rrze-wmp') . '</strong></td><td>' . esc_html($data['aktiv'] ?? 'N/A') . '</td></tr>';
        echo '</table>';
        echo '</div>';

        // Domain-Aliase
        if (!empty($data['serveralias']) && is_array($data['serveralias'])) {
            echo '<div class="rrze-wmp-section">';
            echo '<h3>' . __('Domain Aliases', 'rrze-wmp') . '</h3>';
            echo '<ul>';
            foreach ($data['serveralias'] as $alias) {
                echo '<li>' . esc_html($alias) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        // Instanz-Informationen
        if (!empty($data['instanz']) && is_array($data['instanz'])) {
            echo '<div class="rrze-wmp-section">';
            echo '<h3>' . __('Instance Information', 'rrze-wmp') . '</h3>';
            echo '<table class="widefat">';

            $instanz = $data['instanz'];
            $fields = [
                'hostname' => __('Hostname', 'rrze-wmp'),
                'primary_domain' => __('Primary Domain', 'rrze-wmp'),
                'title' => __('Title', 'rrze-wmp'),
                'fauidmkennung' => __('FAU ID', 'rrze-wmp'),
                'fauemailaddress' => __('Email', 'rrze-wmp'),
                'faurealname' => __('Real Name', 'rrze-wmp'),
                'givenname' => __('Given Name', 'rrze-wmp'),
                'surname' => __('Surname', 'rrze-wmp'),
            ];

            foreach ($fields as $key => $label) {
                if (!empty($instanz[$key])) {
                    echo '<tr><td><strong>' . esc_html($label) . '</strong></td><td>' . esc_html($instanz[$key]) . '</td></tr>';
                }
            }

            echo '</table>';
            echo '</div>';

            // Dienste
            if (!empty($instanz['dienste']) && is_array($instanz['dienste'])) {
                echo '<div class="rrze-wmp-section">';
                echo '<h3>' . __('Services', 'rrze-wmp') . '</h3>';
                echo '<ul>';
                foreach ($instanz['dienste'] as $dienst) {
                    echo '<li>' . esc_html($dienst) . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
        }

        echo '</div>';
    }

    /**
     * Aktuelle Domain ermitteln
     *
     * @return string|null
     */
    protected function getCurrentDomain(): ?string
    {
        $siteUrl = get_site_url();
        $parsedUrl = parse_url($siteUrl);
        return $parsedUrl['host'] ?? null;
    }
}