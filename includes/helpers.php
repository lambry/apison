<?php
/**
 * Simple reusable helper functions
 *
 * @package Apison
 */

namespace Lambry\Apison;

defined('ABSPATH') || exit;

trait Helpers
{

    /**
     * Include one or more files
     *
     * @access private
     * @param string|array $files
     * @return void
     */
    private function include($files) : void
    {
        foreach ((array) $files as $file) {
            require_once APISON_PATH . "{$file}.php";
        }
    }

    /**
     * New up the supplied class
     *
     * @access private
     * @param string $class
     * @return class $instance
     */
    private function instantiate(string $class)
    {
        $instance = __NAMESPACE__ . '\\' . $class;
        return new $instance;
    }

}
