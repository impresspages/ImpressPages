<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model;


abstract class Field
{
    public abstract function preview($data);

    public abstract function createField();

    public abstract function createData($postData);

    /**
     * @param $curData
     * @return \Ip\Form\Field
     */
    public abstract function updateField($curData);

    public abstract function updateData($postData);

    public abstract function searchField();

    public abstract function searchQuery($postData);
}
