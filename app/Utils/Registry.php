<?php
/**
 * Created by PhpStorm.
 * User: lunguandrei
 * Date: 06.08.17
 * Time: 16:38
 */

namespace App\Utils;

class Registry
{
    /**
     * @var array
     */
    protected $array = [];

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->array[$key] = $value;
        return $this;
    }

    /**
     * Return value by key
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return isset($this->array[$key]) ? $this->array[$key] : null;
    }

    /**
     * Remove value by key
     *
     * @param string $key
     * @return void
     */
    final public function remove($key)
    {
        if (array_key_exists($key, $this->array)) {
            unset($this->array[$key]);
        }
    }

    /**
     * Return array size
     * @return int
     */
    public function count()
    {
        return count($this->array);
    }

    public function getData()
    {
        return $this->array;
    }
}