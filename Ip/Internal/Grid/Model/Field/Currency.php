<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model\Field;


class Currency extends \Ip\Internal\Grid\Model\Field
{

    protected $currency;
    protected $currencyField;

    /**
     * Create field object for grid
     * @param array $fieldFieldConfig config of this particular field
     * @param $wholeConfig whole grid setup config
     * @throws \Ip\Exception
     */
    public function __construct($fieldFieldConfig, $wholeConfig)
    {
        if (!empty($fieldFieldConfig['currency'])) {
            $this->currency = $fieldFieldConfig['currency'];
        } else {
            $this->currency = 'USD';
        }
        if (!empty($fieldFieldConfig['currencyField'])) {
            $this->currencyField = $fieldFieldConfig['currencyField'];
        }

        if (!empty($fieldFieldConfig['defaultValue'])) {
            $fieldFieldConfig['defaultValue'] = $fieldFieldConfig['defaultValue'] / 100;
        }

        return parent::__construct($fieldFieldConfig, $wholeConfig);
    }

    /**
     * Generate field value preview for table view. HTML is allowed
     * @param $recordData
     * @internal param array $data current record data
     * @return string
     */
    public function preview($recordData)
    {
        //$recordData[$this->field] = $recordData[$this->field]/100;

        $currency = $this->getCurrency();
        if ($this->getCurrencyField() && !empty($recordData[$this->getCurrencyField()])) {
            $currency = $recordData[$this->getCurrencyField()];
        }
        return ipFormatPrice($recordData[$this->field], $currency, 'Grid');
    }

    public function createField()
    {
        $field = new \Ip\Form\Field\Currency(array(
            'label' => $this->label,
            'name' => $this->field,
            'layout' => $this->layout,
            'attributes' => $this->attributes
        ));
        $field->setValue($this->defaultValue);
        return $field;
    }

    public function createData($postData)
    {
        if (isset($postData[$this->field])) {
            return array($this->field => $postData[$this->field]*100);
        }
        return [];
    }

    public function updateField($curData)
    {
        $field = new \Ip\Form\Field\Currency(array(
            'label' => $this->label,
            'name' => $this->field,
            'layout' => $this->layout,
            'attributes' => $this->attributes
        ));
        if (!empty($curData[$this->field])) {
            $curData[$this->field] = $curData[$this->field] / 100;
        }
        if (isset($curData[$this->field])){
        $field->setValue($curData[$this->field]);
        }
        return $field;
    }

    public function updateData($postData)
    {
        return array($this->field => $postData[$this->field]*100);
    }


    public function searchField($searchVariables)
    {
        $field = new \Ip\Form\Field\Currency(array(
            'label' => $this->label,
            'name' => $this->field,
            'layout' => $this->layout,
            'attributes' => $this->attributes
        ));
        if (!empty($searchVariables[$this->field])) {
            $field->setValue($searchVariables[$this->field]);
        }
        return $field;
    }

    public function searchQuery($searchVariables)
    {
        if (isset($searchVariables[$this->field]) && $searchVariables[$this->field] !== '') {
            return ' `' . $this->field . '` = ' . ipDb()->getConnection()->quote(
                $searchVariables[$this->field]*100
            ) . '';
        }
        return null;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getCurrencyField()
    {
        return $this->currencyField;
    }
}
