<?php

namespace Simplon\Helper;

/**
 * SingletonTrait
 * @package Simplon\Helper
 * @author Tino Ehrich (tino@bigpun.me)
 */
trait SingletonTrait
{
    /**
     * @var
     */
    protected static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!static::$instance)
        {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Prevent creation of new instance other than singleton
     */
    protected function __construct()
    {
    }

    /**
     * Prevent cloning
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserialisation
     */
    private function __wakeup()
    {
    }
}