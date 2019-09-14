<?php
/**
 * Create the admin table
 *
 * @package Apison
 */

namespace Lambry\Apison\Admin;

use Lambry\Apison\Admin\Defaults;
use Lambry\Apison\Shared\{Option, Transient};

defined('ABSPATH') || exit;

class Table extends \WP_List_Table
{

    /**
     * Set column headers and prepare items
     *
     * @access public
     * @return null
     */
    public function prepare_items()
    {
        $this->_column_headers = [$this->get_columns(), [], []];

        $this->items = Option::get();
    }

    /**
     * Display intructional text when no items exist
     *
     * @access public
     * @return void
     */
    public function no_items()
    {
        echo '<h4 class="apison-noitems">' . __('You haven\'t added any api endpoints, click the Add New button above to get started.', 'apison') . '</h4>';
    }

    /**
     * Columns to display in table
     *
     * @access public
     * @return array $columns
     */
    public function get_columns() : array
    {
        return [
            'title' => __('Title', 'apison'),
            'slug' => __('Slug', 'apison'),
            'cache' => __('Cache', 'apison'),
            'active' => __('Active', 'apison'),
            'status' => __('Status', 'apison')
        ];
    }

    /**
     * Get value for title column, add hidden fields and actions
     *
     * @access public
     * @param object $item
     * @return string $column
     */
    public function column_title(\stdClass $item) : string
    {
        $title = '<strong>' . $item->title . '</strong>';

        $actions = [
            'edit' => sprintf('<a href="#" class="apison-edit">%s</a><a href="%s" class="apison-view" target="_blank">%s</a>', __('Edit', 'apison'), rest_url(APISON_KEY . "/$item->slug"),  __('View', 'apison'))
        ];

        return $title . $this->hidden_fields($item) . $this->row_actions($actions);
    }

    /**
     * Get value for slug column
     *
     * @access public
     * @param object $item
     * @return string $column
     */
    public function column_slug(\stdClass $item) : string
    {
        return $item->slug;
    }

    /**
     * Get value for cache column
     *
     * @access public
     * @param object $item
     * @return string $column
     */
    public function column_cache(\stdClass $item) : string
    {
        $expiry = Transient::expiration($item->slug);

        return Defaults::cache()[$item->cache] . "<span class='apison-badge'>${expiry}</span>";
    }

    /**
     * Get value for active column
     *
     * @access public
     * @param object $item
     * @return string $column
     */
    public function column_active(\stdClass $item) : string
    {
        return $item->active ? '<i class="dashicons dashicons-yes"></i>' : '<i class="dashicons dashicons-no"></i>';
    }

    /**
     * Get value for status column
     *
     * @access public
     * @param object $item
     * @return string $column
     */
    public function column_status(\stdClass $item) : string
    {
        $status = $item->status ?? 'pending';

        return "<i class='dashicons dashicons-marker apison-{$status}' title='{$status}'></i>";
    }

    /**
     * Add hidden fields for all values so js can access them
     *
     * @access public
     * @param object $item
     * @return string $fields
     */
    private function hidden_fields(\stdClass $item) : string
    {
        $fields = '';

        foreach($item as $key => $value) {
            $fields .= "<input type='hidden' name='{$key}' value='{$value}'>";
        }

        return $fields;
    }

}
