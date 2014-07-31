<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model\Field;


class Tab extends \Ip\Internal\Grid\Model\Field
{


    public function createField()
    {
        return new \Ip\Form\Field\Info();
    }

    public function createData($postData)
    {
        return array();
    }

    public function updateField($curData)
    {
        return new \Ip\Form\Field\Info();
    }

    public function updateData($postData)
    {
        return array();
    }


    public function searchField($searchVariables)
    {
        return new \Ip\Form\Field\Info();
    }

    public function searchQuery($searchVariables)
    {
        return array();
    }
}
