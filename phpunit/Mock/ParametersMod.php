<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace PhpUnit\Mock;

class ParametersMod
{
    protected $parameters;

    public function __construct()
    {
        require(BASE_DIR.'install/parameters.php');
        require(BASE_DIR.'update/Library/Migration/To3_0/newParameters.php');
        $this->parameters = array();
    }

    public function getValue($modGroup, $module, $parGroup, $parameter, $languageId = null)
    {
        return $this->parameters[$modGroup][$module][$parGroup][$parameter];
    }

}