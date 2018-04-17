<?php
/**
 * Default data
 *
 * @package Apison
 */

namespace Lambry\Apison\Admin;

defined('ABSPATH') || exit;

class Defaults
{

    /**
     * Cache duration options
     *
     * @access public
     * @return array $cache
     */
    public static function cache()
    {
        $cache = [
            '15' => __('15 mins', 'apison'),
            '30' => __('30 mins', 'apison'),
            '60' => __('1 hr', 'apison'),
            '180' => __('3 hrs', 'apison'),
            '360' => __('6 hrs', 'apison'),
            '720' => __('12 hrs', 'apison'),
            '1400' => __('1 day', 'apison')
        ];

        return apply_filters('apison/cache', $cache);
    }

}
