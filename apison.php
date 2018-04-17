<?php
/**
 * Plugin Name: Apison
 * Plugin URI: https://github.com/lambry/apison/
 * Description: A simple plugin to fetch, cache and access API data (JSON).
 * Version: 0.1.0
 * Author: Lambry
 * Author URI: https://lambry.com/
 */

namespace Lambry\Apison;

defined('ABSPATH') || exit;

require_once 'helpers.php';

class Init
{
    use Helpers;

    /**
     * Define constants, add get started
     */
    public function __construct()
    {
        define('APISON_VERSION', '0.1.0');
        define('APISON_KEY', 'apison');
        define('APISON_URL', plugin_dir_url(__FILE__) . 'includes/');
        define('APISON_PATH', plugin_dir_path(__FILE__) . 'includes/');

        $this->bootstrap();
    }

    /**
     * Include all autoloaded files and register actions
     *
     * @access public
     * @return void
     */
    public function bootstrap() : void
    {
        $includes = require_once 'autoload.php';

        $this->include($includes['shared']);

        if (is_admin()) {
            $this->include($includes['admin']);

            add_action('init', [$this->instantiate('Admin\Settings'), 'init']);
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'links']);
        } else {
            $this->include($includes['frontend']);

            add_action('rest_api_init', [$this->instantiate('Frontend\Endpoint'), 'init']);
        }
    }

    /**
     * Add action links to plugins page
     *
     * @access public
     * @param array $links
     * @return array $links
     */
    public function links(array $links) : array
    {
        return array_merge([
            '<a href="' . admin_url('options-general.php?page=apison') . '">' . __('Settings', 'apison') . '</a>',
        ], $links);
    }

}

new Init();
