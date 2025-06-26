<?php
namespace RRZE\WMP;

defined('ABSPATH') || exit;

/**
 * WMP API Client
 *
 * Manages the connection to the WMP API
 *
 * @package RRZE\WMP
 * @since 1.0.0
 */
class ApiClient
{
    /**
     * @var string WMP API Base URL
     */
    protected $baseUrl = 'https://www.wmp.rrze.fau.de/api/cms/config/servername/';


    /**
     * Get domain data from WMP API
     *
     * @param string $domain Domain-Name
     * @return array WMP-Daten
     */
    public function getDomainData(string $domain): array
    {
        // API-Aufruf
        $url = $this->baseUrl . urlencode($domain);


        $response = wp_safe_remote_get($url, [
            'timeout' => 10,
            'headers' => [
                'User-Agent' => 'RRZE-WMP-Plugin/' . plugin()->getVersion()
            ],
        ]);
        // Error handling
        if (is_wp_error($response)) {
            return []; // oder throw new Exception()
        }

        $body = wp_remote_retrieve_body($response);
        $asset_path = plugin_dir_path( __DIR__ ) . 'assets/test-data.json';
        $dummy = file_get_contents( $asset_path );
        $data = json_decode($dummy, true);
        Helper::debug($data);

        return $data ?? [];
    }
}

