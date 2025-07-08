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
    protected string $baseUrl = 'https://www.wmp.rrze.fau.de/api/cms/config/servername/';


    /**
     * Get domain data from WMP API
     *
     * @param string $domain Domain-Name
     * @return array WMP-Daten
     */
    public function getDomainData(string $domain): array
    {
        // get API data
        $url = $this->baseUrl . urlencode($domain);

        $response = wp_safe_remote_get($url, [
            'timeout' => 10,
            'headers' => [
                'User-Agent' => 'RRZE-WMP-Plugin/' . plugin()->getVersion()
            ],
        ]);

        if (is_wp_error($response)) {
            Helper::debug('Fehler beim API-Aufruf: ' . $response->get_error_message(), 'error');
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Extract first (and only) entry from API response
        if (is_array($data) && !empty($data)) {
            $firstEntry = reset($data); // Gets first array element
            return $firstEntry;
        }


        return [];
    }

}
