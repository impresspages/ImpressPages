<?php

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace PhpUnit\Mock;

class ParametersMod
{
    protected $parameters;

    public function __construct()
    {
        require(BASE_DIR.'install/parameters.php');
        require(BASE_DIR.'update/Library/Migration/To2_7/newParameters.php');
        $this->parameters = $parameterValue;
    }

    public function getValue($modGroup, $module, $parGroup, $parameter, $languageId = null)
    {
        return $this->parameters[$modGroup][$module][$parGroup][$parameter];
    }

}