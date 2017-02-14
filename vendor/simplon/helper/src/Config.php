<?php

namespace Simplon\Helper;

/**
 * Config
 * @package Simplon\Helper
 * @author Tino Ehrich (tino@bigpun.me)
 */
class Config
{
    /**
     * @var array
     */
    private static $config;

    /**
     * @return array
     */
    public static function getConfig()
    {
        return (array)self::$config;
    }

    /**
     * @param array $configCommon
     * @param array $configEnv
     *
     * @return bool
     */
    public static function setConfig(array $configCommon, array $configEnv = [])
    {
        self::$config = array_replace_recursive($configCommon, $configEnv);

        return true;
    }

    /**
     * @param array $keys
     *
     * @return bool
     */
    public static function hasConfigKeys(array $keys)
    {
        $config = self::getConfig();

        while ($key = array_shift($keys))
        {
            if (isset($config[$key]) === false)
            {
                return false;
            }

            $config = $config[$key];
        }

        if (empty($config) === true)
        {
            return false;
        }

        return true;
    }

    /**
     * @param array $keys
     *
     * @return mixed|null
     * @throws ConfigException
     */
    public static function getConfigByKeys(array $keys)
    {
        $keysString = join(' => ', $keys);
        $config = self::getConfig();

        while ($key = array_shift($keys))
        {
            if (isset($config[$key]) === false)
            {
                throw new ConfigException('Config entry for [' . $keysString . '] is missing.');
            }

            $config = $config[$key];
        }

        if (empty($config) === true)
        {
            return null;
        }

        return $config;
    }
}