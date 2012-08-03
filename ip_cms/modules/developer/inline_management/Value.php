<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */


namespace Modules\developer\inline_management\Value;


abstract class General
{
    const TYPE_GLOBAL = 'global';
    const TYPE_LANGUAGE = 'language';
    const TYPE_PARENT_PAGE = 'parent_page';
    const TYPE_PAGE = 'page';

    private $type;
    private $value;

    /**
     * @param string $type
     * @param string $value
     */
    public function __construct($type, $value)
    {
        $this->value = $value;
        $this->type = $type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getType()
    {
        return $this->type;
    }

}