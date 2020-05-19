<?php
/**
 * Handle transients
 *
 * @package Apison
 */

namespace Lambry\Apison\Shared;

defined('ABSPATH') || exit;

class Transient
{
    private $slug = '';
    private $endpoint = null;
    private $endpoints = [];

    /**
     * Set the slug and endpoint info
     */
    public function __construct(string $slug)
    {
        $this->slug = $slug;
        $this->endpoints = Option::get();
        $this->endpoint = $this->getEndpoint($slug, $this->endpoints);
    }

    /**
     * Get the transients data
     *
     * @access public
     * @return mixed $data
     */
    public function get()
    {
        $data = get_transient(APISON_KEY . "_{$this->slug}");

        return $data ?: $this->set();
    }

    /**
     * Set the transients data
     *
     * @access public
     * @return mixed $data
     */
    public function set()
    {
        $request = wp_remote_get($this->getUrl());
        $code = wp_remote_retrieve_response_code($request);

        if (! is_wp_error($request) && $code === 200) {
            $data = $this->processData(
                wp_remote_retrieve_body($request)
            );

            set_transient(APISON_KEY . "_{$this->slug}", $data, (int) $this->endpoint->cache * MINUTE_IN_SECONDS);
        }

        $this->updateStatus($code);

        return $data ?? [];
    }

    /**
     * Delete the transient data
     *
     * @access public
     * @param string $slug
     * @return bool $success
     */
    public static function delete(string $slug) : bool
    {
        return delete_transient(APISON_KEY . "_{$slug}");
    }

    /**
     * Get the transients expiration
     *
     * @access public
     * @param string $slug
     * @return string $expiration
     */
    public static function expiration(string $slug) : string
    {
        $now = time();
        $expiration = $time = get_option('_transient_timeout_' . APISON_KEY . "_{$slug}");

        return (! empty($expiration) && $expiration > $now) ? human_time_diff($now, $expiration) : __('Empty', 'apison');
    }

    /**
     * Get a parsed url with optional {key}
     *
     * @access private
     * @return string $url
     */
    private function getUrl() : string
    {
        if (strpos($this->endpoint->url, '_key_')) {
            return str_replace('_key_', apply_filters('apison/key', $this->endpoint->slug) ?: '', $this->endpoint->url);
        }

        return $this->endpoint->url;
    }

    /**
     * Decode data and conditionally set it's starting point
     *
     * @access private
     * @param string $data
     * @return mixed $data
     */
    private function processData(string $data)
    {
        $data = json_decode($data);

        if (! $data || ! $this->endpoint->path) {
            return $data;
        }

        return $this->traversePath($this->endpoint->path, $data);
    }

    /**
     * Sets the starting point to supplied path i.e cache data under data.events
     *
     * @access private
     * @param string $path
     * @return mixed $data
     * @return mixed $traversedData
     */
    private function traversePath(string $path, $data)
    {
        foreach (explode('.', $path) as $key) {
            if (property_exists($data, $key)) {
                $data = $data->{$key};
            }
        }

        return $data;
    }

    /**
     * Get an endpoints saved data
     *
     * @access private
     * @return string $slug
     * @return array $endpoints
     * @return object $endpoint
     */
    private function getEndpoint(string $slug, array $endpoints) : \stdClass
    {
        $endpoint = array_filter($endpoints, function ($endpoint) use ($slug) {
            return $endpoint->slug === $slug;
        });

        return reset($endpoint);
    }

    /**
     * Update the endpoints status
     *
     * @access private
     * @param mixed $status
     * @return bool $success
     */
    private function updateStatus($status) : bool
    {
        $updated = array_map(function ($endpoint) use ($status) {
            if ($endpoint->slug === $this->slug) {
                $endpoint->status = ($status === 200) ? 'success' : 'error';
            }
            return $endpoint;
        }, $this->endpoints);

        return Option::set($updated);
    }

}
