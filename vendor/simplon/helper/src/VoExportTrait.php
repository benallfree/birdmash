<?php

namespace Simplon\Helper;

/**
 * VoExportTrait
 * @package Simplon\Helper
 * @author Tino Ehrich (tino@bigpun.me)
 */
trait VoExportTrait
{
    /**
     * @param $vo
     *
     * @return array
     */
    public static function one($vo)
    {
        return [];
    }

    /**
     * @param array $voMany
     *
     * @return array
     */
    public static function many(array $voMany)
    {
        if (empty($voMany) === true)
        {
            return [];
        }

        $export = [];

        foreach ($voMany as $vo)
        {
            $result = static::one($vo);

            if ($result !== null)
            {
                $export[] = $result;
            }
        }

        return $export;
    }
}