<?php

namespace Simplon\Helper;

/**
 * DataValidator
 * @package Simplon\Helper
 * @author  Tino Ehrich (tino@bigpun.me)
 */
class DataValidator
{
    /**
     * @var array
     */
    private $fields = [];

    /**
     * @param string   $fieldName
     * @param \Closure $validationClosure
     * @param \Closure $successClosure
     *
     * @return DataValidator
     */
    public function testField($fieldName, \Closure $validationClosure, \Closure $successClosure)
    {
        $this->fields[$fieldName] = [
            'validation' => $validationClosure,
            'success'    => $successClosure,
        ];

        return $this;
    }

    /**
     * @param array $data
     *
     * @return array|bool
     */
    public function applyOn(array $data)
    {
        // get fields
        $fields = $this->getFields();

        // test data
        $responses = [];

        foreach ($fields as $k => $v)
        {
            // set default value
            $responses[$k] = false;

            if (isset($data[$k]))
            {
                // get response from closure
                $isValid = $fields[$k]['validation']($data[$k]);

                if ($isValid === true)
                {
                    unset($responses[$k]);

                    // run success closure
                    $fields[$k]['success']($data[$k]);

                    continue;
                }

                $responses[$k] = $isValid;
            }
        }

        // anything invalid?
        if (empty($responses) === false)
        {
            return $responses;
        }

        return true;
    }

    /**
     * @return array
     */
    private function getFields()
    {
        return (array)$this->fields;
    }
}