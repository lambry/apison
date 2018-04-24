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
     * @param array $check
     * @return mixed $isValid
     */
    private function validate(array $checks)
    {
        if (in_array('request', $checks) && ! $this->isValidRequest()) {
            return new \WP_Error('invalid-request', __('This doesn\'t seem right.', 'apison'));
        }

        if (in_array('form', $checks) && ! $this->isValidForm()) {
            return new \WP_Error('invalid-form', __('Please ensure all required fields are populated correctly.', 'apison'));
        }

        if (in_array('unique', $checks) && ! $this->isUnique()) {
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
        $validAction = $this->request['action'] === APISON_KEY . '_save' || $this->request['action'] === APISON_KEY . '_delete';

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
        if ($this->form->id === $this->form->slug) return true;

        $slugs = array_map(function ($endpoint) {
            return $endpoint->slug;
        }, $this->endpoints);

        return array_search(sanitize_title_with_dashes($this->form->slug), $slugs) === false;
    }

    /**
     * Sanitize form data
     *
     * @access private
     * @param string|array $fields
     * @return mixed $form
     */
    private function sanitizeForm($fields)
    {
        if (is_string($fields)) {
            return sanitize_text_field($fields);
        }

        return (object) [
            'id' => sanitize_text_field($fields['id']),
            'title' => sanitize_text_field($fields['title']),
            'slug' => str_replace('-', '', sanitize_key($fields['slug'])),
            'url' => esc_url($fields['url']),
            'path' => sanitize_text_field($fields['path']),
            'cache' => sanitize_text_field($fields['cache']),
            'active' => isset($fields['active']) ? true : false
        ];
    }

}
