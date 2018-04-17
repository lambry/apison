<?php
/**
 * Create the admin settings page
 *
 * @package Apison
 */

namespace Lambry\Apison\Admin;

use Lambry\Apison\Shared\{Option, Transient};

defined('ABSPATH') || exit;

class Settings
{
    use \Lambry\Apison\Helpers, Validation;

    const SCREEN = 'settings_page_' . APISON_KEY;

    private $form = [];
    private $request = null;
    private $endpoints = [];

    /**
     * Add actions
     *
     * @access public
     * @return void
     */
    public function init() : void
    {
        add_action('admin_enqueue_scripts', [$this, 'assets']);
        add_action('admin_menu', [$this, 'menu']);
        add_action('wp_ajax_' . APISON_KEY . '_save', [$this, 'save']);
        add_action('wp_ajax_' . APISON_KEY . '_delete', [$this, 'delete']);
    }

    /**
     * Add admin assets
     *
     * @access public
     * @param  string $hook
     * @return void
     */
    public function assets(string $hook) : void
    {
        if ($hook !== self::SCREEN) return;

        wp_enqueue_style(APISON_KEY . '-style', APISON_URL . 'admin/assets/styles/styles.css', [], APISON_VERSION);

        wp_register_script(APISON_KEY . '-script', APISON_URL . 'admin/assets/scripts/scripts.min.js', ['jquery'], APISON_VERSION, true);
        wp_localize_script(APISON_KEY . '-script', APISON_KEY, ['nonce' => wp_create_nonce(APISON_KEY . '_nonce')]);
        wp_enqueue_script(APISON_KEY . '-script');
    }

    /**
     * Add new admin menu under settings
     *
     * @access public
     * @return void
     */
    public function menu() : void
    {
        $page = add_options_page(__('Apison APIs', 'apsion'), __('Apison', 'apison'), apply_filters('apison/permission', 'manage_options'), APISON_KEY, [$this, 'page']);

        add_action('load-' . $page, [$this, 'help']);
    }

    /**
     * Add new admin page with wp list table
     *
     * @access public
     * @return void
     */
    public function page() : void
    {
        $this->include('admin/table');
        $this->include('admin/views/header');

        $table = new Table();
        $table->prepare_items();
        $table->display();

        $this->include('admin/views/form');

        $this->include('admin/views/footer');
    }

    /**
     * Add admin help tab
     *
     * @access public
     * @return void
     */
    public function help() : void
    {
        $screen = get_current_screen();

        ob_start();
        $this->include('admin/views/help');
        $content = ob_get_clean();

        $screen->add_help_tab([
            'id' => APISON_KEY . '-help',
            'title' => __('Form fields', 'apison'),
            'content' => $content
        ]);
    }

    /**
     * Save the form data
     *
     * @access public
     * @return json $response
     */
    public function save()
    {
        $this->endpoints = Option::get();
        $this->form = $this->sanitizeForm($_REQUEST['fields']);

        $validation = $this->validate();

        if (is_wp_error($validation)) {
            echo json_encode([
                'status' => 'error',
                'message' => $validation->get_error_message()
            ]);

            die();
        }

        $this->saveEndpoint();

        echo json_encode([
            'status' => 'success',
            'data' => $this->getRow()
        ]);

        die();
    }

    /**
     * Delete an existing endpoint
     *
     * @access public
     * @return array $response
     */
    public function delete()
    {
        $this->endpoints = Option::get();
        $this->form = sanitize_text_field($_REQUEST['id']);

        if (! $this->isValidRequest()) {
            echo json_encode([
                'status' => 'error',
                'message' => __('Invalid request', 'apison')
            ]);

            die();
        }

        $this->deleteEndpoint();

        echo json_encode([
            'status' => 'success'
        ]);

        die();
    }

    /**
     * Update or add form data to option, also clears the transient cache
     *
     * @access private
     * @return bool $success
     */
    private function saveEndpoint() : bool
    {
        if ($this->form->id) {
            Transient::delete($this->form->slug);

            return $this->updateEndpoint();
        }

        return $this->addEndpoint();
    }

    /**
     * Add a new endpoint
     *
     * @access private
     * @return bool $success
     */
    private function addEndpoint() : bool
    {
        array_unshift($this->endpoints, $this->form);

        return Option::set($this->endpoints);
    }

    /**
     * Update an existing endpoint
     *
     * @access private
     * @return bool $success
     */
    private function updateEndpoint() : bool
    {
        $updated = array_map(function($endpoint) {
            return $endpoint->slug === $this->form->id ? $this->form : $endpoint;
        }, $this->endpoints);

        return Option::set($updated);
    }

    /**
     * Delete an existing endpoint
     *
     * @access private
     * @return bool $success
     */
    private function deleteEndpoint() : bool
    {
        $updated = array_filter($this->endpoints, function ($endpoint) {
            return $endpoint->slug !== $this->form;
        });

        return Option::set($updated);
    }

    /**
     * Generate a single row from the new form data to return
     *
     * @access private
     * @return string $row
     */
    private function getRow() : string
    {
        $this->include('admin/table');

        ob_start();

        $table = new Table(['screen' => self::SCREEN]);
        $table->single_row($this->form);

        return ob_get_clean();
    }

}
