<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid\Model\Field;


class Text extends \Ip\Grid\Model\Field
{
    protected $field = '';

    public function __construct($config)
    {
        if (empty($config['field'])) {
            throw new \Ip\CoreException('\'field\' option required for text field');
        }
        $this->field = $config['field'];
    }

    public function preview($recordData)
    {
        return esc($recordData[$this->field]);
    }

    public function createField()
    {
    }

    public function createQuery($postData)
    {
    }

    public function updateField()
    {
    }

    public function updateQuery($postData)
    {
    }


    public function searchField()
    {
    }

    public function searchQuery($postData)
    {
    }
}