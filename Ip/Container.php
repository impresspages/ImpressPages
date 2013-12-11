<?php

namespace Ip;

class Container extends \Pimple\Pimple
{
    protected static $container = null;
    protected static $containerStack = array();

    public function get($id)
    {
        return $this->offsetGet($id);
    }

    public function set($id, $value)
    {
        return $this->offsetSet($id, $value);
    }

    /**
     * @return \Ip\Container
     */
    public static function container()
    {
        return static::$container;
    }

    public static function addContainer($container)
    {
        if (static::$container) {
            static::$containerStack[] = static::$container;
        }
        static::$container = $container;

        return static::$container;
    }

    public static function popContainer()
    {
        static::$container = array_pop(static::$containerStack);
        return static::$container;
    }
}