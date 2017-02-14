<?php

namespace Simplon\Helper;

/**
 * DataIterator
 * @package Simplon\Helper
 * @author  Tino Ehrich (tino@bigpun.me)
 */
class DataIterator
{
    /**
     * @param array    $data
     * @param \Closure $closure
     * @param bool     $withKey
     *
     * @return array
     */
    public static function iterate(array $data, \Closure $closure, $withKey = false)
    {
        $responses = [];

        foreach ($data as $key => $value)
        {
            $response = self::handleClosureCall($withKey, $closure, $key, $value);

            if ($response !== null)
            {
                $responses[] = $response;
            }
        }

        return $responses;
    }

    /**
     * @param bool     $withKey
     * @param \Closure $closure
     * @param mixed    $key
     * @param mixed    $value
     *
     * @return mixed
     */
    private static function handleClosureCall($withKey, \Closure $closure, $key, $value)
    {
        if ($withKey === true)
        {
            return $closure($key, $value);
        }

        return $closure($value);
    }
}