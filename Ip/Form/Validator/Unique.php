<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;


class Unique extends \Ip\Form\Validator
{

    /**
     * Constructor
     *
     * @param array $data
     * @param string $errorMessage
     * @throws \Ip\Exception
     */
    public function __construct($data, $errorMessage = null)
    {
        if (empty($data['table'])) {
            throw new \Ip\Exception('Unique validator expect table name');
        }
        parent::__construct($data, $errorMessage);
    }

    /**
     * Get error
     *
     * @param array $values
     * @param int $valueKey
     * @param $environment
     * @return string|bool
     */
    public function getError($values, $valueKey, $environment)
    {
        if (!array_key_exists($valueKey, $values)) {
            return false;
        }

        if ($values[$valueKey] == '' && empty($this->data['allowEmpty'])) {
            return false;
        }

        $table = $this->data['table'];

        $idField = empty($this->data['idField']) ? 'id' : $this->data['idField'];

        $row = ipDb()->selectRow($table, '*', array($valueKey => $values[$valueKey]));

        if (!$row) {
            return false;
        }

        if (isset($values[$idField]) && $values[$idField] == $row[$idField]) {
            return false;
        }

        if ($this->errorMessage !== null) {
            return $this->errorMessage;
        }

        if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
            $errorText = __('The value should be unique', 'Ip-admin');
        } else {
            $errorText = __('The value should be unique', 'Ip');
        }

        return $errorText;
    }

}
