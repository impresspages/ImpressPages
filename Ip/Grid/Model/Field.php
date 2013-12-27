<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid\Model;


abstract class Field
{
    public abstract function preview($data);

    public abstract function createField();

    public abstract function createQuery($postData);

    public abstract function updateField($curData);

    public abstract function updateQuery($postData);

    public abstract function searchField();

    public abstract function searchQuery($postData);
}