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
        return [];
    }

    public function updateField($curData)
    {
        return false;
    }

    public function updateData($postData)
    {
        return [];
    }


    public function searchField($searchVariables)
    {
        return false;
    }

    public function searchQuery($searchVariables)
    {
        return [];
    }
}
