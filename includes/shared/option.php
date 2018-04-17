<?php
/**
 * Handle options
 *
 * @package Apison
 */

namespace Lambry\Apison\Shared;

defined('ABSPATH') || exit;

class Option
{
    const NAME = APISON_KEY . '_endpoints';

    /**
     * Get the options value
     *
     * @access public
     * @return array $options
     */
    public static function get() : array
    {
        return get_option(self::NAME, []);
    }

    /**
     * Set the options value
     *
     * @access public
     * @param array $data
     * @return bool $success
     */
    public static function set(array $data) : bool
    {
        return update_option(self::NAME, $data);
    }

    /**
     * Check is a single option is active
     *
     * @access private
     * @param string
     * @return bool $active
     */
    public static function active(string $slug) : bool
    {
        return (bool) array_filter(static::get(), function ($endpoint) use ($slug) {
            return $endpoint->active && ($endpoint->slug === $slug);
        });
    }

}
