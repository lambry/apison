<?php
/**
 * Expose cached data via rest endpoint
 *
 * @package Apison
 */

namespace Lambry\Apison\Frontend;

defined('ABSPATH') || exit;

class Endpoint
{
    const RESERVED = ['slug', 'with', 'limit'];

    /**
     * Register the endpoint
     *
     * @access public
     * @return void
     */
    public function init() : void
    {
        register_rest_route(APISON_KEY, '/(?P<slug>\w+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get']
        ]);
    }

    /**
     * Get the appropriate data for said endpoint
     *
     * @access public
     * @param object $request
     * @return mixed
     */
    public function get(\WP_REST_Request $request)
    {
        $data = $this->getData($request->get_params());

        return rest_ensure_response($data);
    }

    /**
     * Get data via constructed Api query
     *
     * @access private
     * @param array $params
     * @return mixed $data
     */
    private function getData(array $params)
    {
        $api = Api::get($params['slug']);

        foreach ($params as $param => $value) {
            if (in_array($param, self::RESERVED)) continue;

            $query = $this->setQuery($param, $value);

            $api->where($query['key'], $query['comparator'], $query['value']);
        }

        if (isset($params['with'])) {
            $api->with(explode(',', $params['with']));
        }

        return isset($params['limit']) ? $api->limit(...explode(',', $params['limit'])) : $api->all();
    }

    /**
     * Check and set the query key, comparator and value
     * NOTE: . in param is converted to _ by WordPress
     *
     * @access private
     * @param string $param
     * @param string $value
     * @return array $query
     */
    private function setQuery(string $param, string $value) : array
    {
        $query = [];

        if (! strpos($param, '_')) {
            $param = "{$param}_is";
        }

        [0 => $query['key'], 1 => $query['comparator']] = explode ('_', $param);

        $query['value'] = ($query['comparator'] === 'is' || $query['comparator'] === 'not') ? explode(',', $value) : (int) $value;

        return $query;
    }

}
