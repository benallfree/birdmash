<?php

namespace Simplon\Helper;

/**
 * Class DataIoVoTrait
 * @package Simplon\Helper
 */
trait DataIoVoTrait
{
    /**
     * @param array $data
     *
     * @return static
     */
    public function fromArray(array $data)
    {
        foreach ($data as $fieldName => $val)
        {
            // format field name
            if (strpos($fieldName, '_') !== false)
            {
                $fieldName = self::camelCaseString($fieldName);
            }

            $setMethodName = 'set' . ucfirst($fieldName);

            // set by setter
            if (method_exists($this, $setMethodName))
            {
                $this->$setMethodName($val);
                continue;
            }

            // set directly on field
            $this->$fieldName = $val;
        }

        return $this;
    }

    /**
     * @param bool $snakeCase
     *
     * @return array
     */
    public function toArray($snakeCase = true)
    {
        $result = [];
        $processableFields = get_class_vars(get_called_class());

        // render column names
        foreach ($processableFields as $fieldName => $value)
        {
            $getMethodName = 'get' . ucfirst($fieldName);

            // format field name
            if ($snakeCase === true && strpos($fieldName, '_') === false)
            {
                $fieldName = self::snakeCaseString($fieldName);
            }

            // set by getter
            if (method_exists($this, $getMethodName))
            {
                $result[$fieldName] = $this->$getMethodName();
                continue;
            }

            // get from field
            $result[$fieldName] = $this->$fieldName;
        }

        return $result;
    }

    /**
     * @param bool $snakeCase
     *
     * @return string
     */
    public function toJson($snakeCase = true)
    {
        return json_encode(
            $this->toArray($snakeCase)
        );
    }

    /**
     * @param $string
     *
     * @return string
     */
    protected static function snakeCaseString($string)
    {
        return strtolower(preg_replace('/([A-Z])/', '_\\1', $string));
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected static function camelCaseString($string)
    {
        $string = strtolower($string);
        $string = ucwords(str_replace('_', ' ', $string));

        return lcfirst(str_replace(' ', '', $string));
    }
}