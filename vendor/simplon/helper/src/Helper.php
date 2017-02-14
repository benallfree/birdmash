<?php

namespace Simplon\Helper;

/**
 * Helper
 * @package Simplon\Helper
 * @author  Tino Ehrich (tino@bigpun.me)
 */
class Helper
{
    /**
     * @param string $url
     *
     * @return string
     */
    public static function urlTrim($url)
    {
        return trim($url, '/');
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function pathTrim($path)
    {
        return rtrim($path, '/');
    }

    /**
     * @param string|array $url
     * @param array $pathParams
     * @param array $getParams
     * @param string $paramsKeyPatternLeft
     * @param string $paramsKeyPatternRight
     *
     * @return string
     */
    public static function urlRender($url, array $pathParams = [], array $getParams = [], $paramsKeyPatternLeft = '{{', $paramsKeyPatternRight = '}}')
    {
        if (is_array($url))
        {
            $parts = [];

            foreach ($url as $val)
            {
                $parts[] = trim($val, '/');
            }

            $url = join('/', $parts);
        }

        // replace placeholder within the url paths
        foreach ($pathParams as $key => $val)
        {
            $url = str_replace($paramsKeyPatternLeft . $key . $paramsKeyPatternRight, $val, $url);
        }

        // attach get params
        if (empty($getParams) === false)
        {
            $url = $url . '?' . http_build_query($getParams);
        }

        return (string)$url;
    }

    /**
     * @param int $length
     * @param null|string $prefix
     * @param null|string $customCharacters
     *
     * @return string
     */
    public static function createRandomToken($length = 12, $prefix = null, $customCharacters = null)
    {
        $randomString = '';
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        // set custom characters
        if ($customCharacters !== null && empty($customCharacters) === false)
        {
            $characters = $customCharacters;
        }

        // handle prefix
        if ($prefix !== null)
        {
            $prefixLength = strlen($prefix);
            $length -= $prefixLength;

            if ($length < 0)
            {
                $length = 0;
            }
        }

        // generate token
        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $prefix . $randomString;
    }

    /**
     * @param string $url
     *
     * @return bool|string
     */
    public static function urlGetContent($url)
    {
        // set option to ingore http errors
        $context = stream_context_create([
            'http' => ['ignore_errors' => true],
        ]);

        // fetch contents
        try
        {
            $data = file_get_contents($url, false, $context);
        }
        catch (\Exception $e)
        {
            $data = false;
        }

        if ($data)
        {
            return $data;
        }

        return false;
    }

    /**
     * @param string $filePath
     *
     * @return bool|string
     */
    public static function fileGetContent($filePath)
    {
        if (file_exists($filePath))
        {
            return file_get_contents($filePath);
        }

        return false;
    }

    /**
     * @param mixed $binaryData
     * @param null $typeShouldHave
     *
     * @return bool
     */
    public static function fileIsMimeType($binaryData, $typeShouldHave = null)
    {
        if ($typeShouldHave === null)
        {
            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($binaryData);

        return strpos($mimeType, $typeShouldHave) !== false;
    }
}