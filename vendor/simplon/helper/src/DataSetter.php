<?php

namespace Simplon\Helper;

/**
 * DataSetter
 * @package Simplon\Helper
 * @author  Tino Ehrich (tino@bigpun.me)
 */
class DataSetter
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @param string   $fieldName
     * @param \Closure $voMethodClosure
     *
     * @return DataSetter
     */
    public function assignField($fieldName, \Closure $voMethodClosure)
    {
        $this->fields[$fieldName] = $voMethodClosure;

        return $this;
    }

    /**
     * @return array
     */
    protected function getFields()
    {
        return (array)$this->fields;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function applyOn(array $data)
    {
        // get fields
        $fields = $this->getFields();

        // apply data
        foreach ($data as $k => $v)
        {
            if (isset($fields[$k]))
            {
                $fields[$k]($v);
            }
        }

        return true;
    }
} 