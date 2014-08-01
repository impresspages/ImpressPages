<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model\Field;


class Tab extends \Ip\Internal\Grid\Model\Field
{


    public function createField()
    {
        return false;
    }

    public function createData($postData)
    {
        return array();
    }

    public function updateField($curData)
    {
        return false;
    }

    public function updateData($postData)
    {
        return array();
    }


    public function searchField($searchVariables)
    {
        return false;
    }

    public function searchQuery($searchVariables)
    {
        return array();
    }
}
