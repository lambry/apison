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

            $api->where($query->key, $query->comparator, $query->value);
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
     * @return object $query
     */
    private function setQuery(string $param, string $value) : \stdClass
    {
        if (! strpos($param, '_')) $param = "{$param}_is";

        [0 => $key, 1 => $comparator] = explode('_', $param);

        $value = $this->castValues($value, $comparator);

        return (object) [
            'key' => $key,
            'comparator' => $comparator,
            'value' => $value
        ];
    }

    /**
     * Cast supplied string values to boolean or int
     *
     * @access private
     * @param string $value
     * @return mixed $value
     */
    private function castValues(string $value, string $comparator)
    {
        if ($comparator === 'is' || $comparator === 'not') {
            return array_map(function ($val) {
                return ($val === 'true' || $val === 'false') ? filter_var($val, FILTER_VALIDATE_BOOLEAN) : $val;
            }, explode(',', $value));
        }

        return (int) $value;
    }

}
