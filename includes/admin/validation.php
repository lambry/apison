<?php
/**
 * Validation methods form admin settings
 *
 * @package Apison
 */

namespace Lambry\Apison\Admin;

defined('ABSPATH') || exit;

trait Validation
{

    /**
     * Validate request, form and data
     *
     * @access private
     * @return mixed $isValid
     */
    private function validate()
    {
        if (! $this->isValidRequest()) {
            return new \WP_Error('invalid-request', __('This doesn\'t seem right.', 'apison'));
        }

        if (! $this->isValidForm()) {
            return new \WP_Error('invalid-form', __('Please ensure all required fields are populated correctly.', 'apison'));
        }

        if (! $this->isUnique()) {
            return new \WP_Error('invalid-slug', __('Please ensure the slug is unique.', 'apison'));
        }

        return true;
    }

    /**
     * Make sure jax request is valid
     *
     * @access private
     * @return bool $validRequest
     */
    private function isValidRequest() : bool
    {
        $validNonce = check_ajax_referer(APISON_KEY . '_nonce', 'nonce', false);
        $validAction = $_REQUEST['action'] === APISON_KEY . '_save' || $_REQUEST['action'] === APISON_KEY . '_delete';

        return $validNonce && $validAction;
    }

    /**
     * Make sure all required fields have been submitted
     *
     * @access private
     * @return bool $validForm
     */
    private function isValidForm() : bool
    {
        return $this->form->title && $this->form->slug && filter_var($this->form->url, FILTER_VALIDATE_URL);
    }

    /**
     * Make sure the option slug is unique
     *
     * @access private
     * @return bool $isUnique
     */
    private function isUnique() : bool
    {
        if ($this->form->id === $this->form->slug) {
            return true;
        }

        $slugs = array_map(function ($endpoint) {
            return $endpoint->slug;
        }, $this->endpoints);

        return array_search(sanitize_title_with_dashes($this->form->slug), $slugs) === false;
    }

    /**
     * Sanitize form data
     *
     * @access private
     * @param array $fields
     * @return object $form
     */
    private function sanitizeForm(array $fields) : \stdClass
    {
        return (object) [
            'id' => sanitize_text_field($fields['id']),
            'title' => sanitize_text_field($fields['title']),
            'slug' => sanitize_key($fields['slug']),
            'url' => esc_url($fields['url']),
            'path' => sanitize_text_field($fields['path']),
            'cache' => sanitize_text_field($fields['cache']),
            'active' => isset($fields['active']) ? true : false
        ];
    }

}
