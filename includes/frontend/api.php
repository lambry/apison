<?php
/**
 * Allow easy access to cached data via PHP
 *
 * @package Apison
 */

namespace Lambry\Apison\Frontend;

use Lambry\Apison\Shared\{Option, Transient};

defined('ABSPATH') || exit;

class Api
{
    private $slug = [];
    private $data = null;

    /**
     * Return an instance of the class
     *
     * @access public
     * @param string slug
     * @param mixed $data
     * @param object $this
     */
    public function __construct(string $slug, $data)
    {
        $this->slug = $slug;
        $this->data = $data;

        return $this;
    }

    /**
     * Get transient data and return new class instance
     *
     * @access public
     * @param string $slug
     * @return object $this
     */
    public static function get(string $slug) : Api
    {
        $data = Option::active($slug) ? (new Transient($slug))->get() : [];

        return new Api($slug, $data);
    }

    /**
     * Set where 'clause' for filtering results, this only works on arrays of data
     *
     * @access public
     * @param string $key
     * @param string $comparator
     * @param string|array $values
     * @return object $this
     */
    public function where(string $key, ...$params) : Api
    {
        [0 => $comparator, 1 => $values] = $this->setParams($params);

        if (is_array($this->data)) {
            $this->data = array_filter($this->data, function($object) use ($key, $comparator, $values) {
                return $this->hasMatchingProperty($object, $key, $comparator, $values);
            });
        }

        return $this;
    }

    /**
     * Alias for where method
     *
     * @access public
     * @param string $key
     * @param string $comparator
     * @param string|array $values
     * @return object $this
     */
    public function and(string $key, ...$arguments) : Api
    {
        return $this->where($key, ...$arguments);
    }

    /**
     * Only return properties matching the supplied keys
     *
     * @access public
     * @param string|array $keys
     * @return object $this
     */
    public function with($keys = []) : Api
    {
        if (! is_array($this->data)) {
            $this->data = $this->withProperties($this->data, $keys);
        } else {
            $this->data = array_map(function ($object) use ($keys) {
                return $this->withProperties($object, $keys);
            }, $this->data);
        }

        return $this;
    }

    /**
     * Return the first x items from an array with optional offset
     *
     * @access public
     * @param int $limit
     * @param int $offset
     * @return mixed $data
     *
     */
    public function first(int $limit = 10, int $offset = 0)
    {
        return is_array($this->data) ? array_slice($this->data, $offset, $limit) : $this->data;
    }

    /**
     * Return the last x items from an array with optional offset
     *
     * @access public
     * @param int $limit
     * @param int $offset
     * @return mixed $data
     *
     */
    public function last(int $limit = 10, int $offset = 0)
    {
        return is_array($this->data) ? array_slice($this->data, -($offset + $limit), $limit) : $this->data;
    }

    /**
     * Return all results
     *
     * @access public
     * @return mixed $data
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Set params, i.e. set the default comparator to 'is' if need be
     *
     * @access private
     * @return mixed $data
     */
    private function setParams(array $params) : array
    {
        if (count($params) === 1) {
            array_unshift($params, 'is');
        }

        return $params;
    }

    /**
     * Check if an objects property matches supplied values
     *
     * @access private
     * @param object $data
     * @param string $key
     * @param string $comparator
     * @param string|array $values
     * @return array $obj
     */
    private function hasMatchingProperty($data, string $key, string $comparator, $values) : bool
    {
        if (! property_exists($data, $key) || is_object($data->{$key}) || is_array($data->{$key})) {
            return false;
        }

        if ($comparator === 'is' || $comparator === 'not') {
            $intersection = (bool) count(array_intersect((array) $data->{$key}, (array) $values));

            return $comparator === 'is' ? $intersection : ! $intersection;
        }

        return $this->numericComparator($comparator, $data->{$key}, $values);
    }

    /**
     * Create object with only the supplied keys
     *
     * @access private
     * @param object $object
     * @param string|array $keys
     * @return object $obj
     */
    private function withProperties($object, $keys) : \stdClass
    {
        $obj = new \stdClass();

        foreach ((array) $keys as $key) {
            $obj->{$key} = property_exists($object, $key) ? $object->{$key} : null;
        }

        return $obj;
    }

    /**
     * Check the supplied value against the provide match
     *
     * @param string $comparator
     * @param int $value
     * @param int $match
     * @return bool $matches
     */
    private function numericComparator(string $comparator, int $value, int $match) : bool {
        switch ($comparator) {
            case 'gt':
                return $value > $match;

                break;
            case 'gte':
                return $value >= $match;

                break;
            case 'lt':
                return $value < $match;

                break;
            case 'lte':
                return $value <= $match;

                break;
        }
    }

}
