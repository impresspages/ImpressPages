<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid1\Model;


abstract class Field
{
    public abstract function preview($data);

    public abstract function createField();

    public abstract function createQuery($postData);

    public abstract function updateField();

    public abstract function updateQuery($postData);

    public abstract function searchField();

    public abstract function searchQuery($postData);
}