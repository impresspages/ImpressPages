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
        require(BASE_DIR.'install/parameters1.php');
        require(BASE_DIR.'install/parameters2.php');
        require(BASE_DIR.'install/parameters3.php');
        require(BASE_DIR.'install/parameters4.php');
        require(BASE_DIR.'install/parameters5.php');
        require(BASE_DIR.'install/parameters6.php');
        require(BASE_DIR.'install/parameters7.php');
        require(BASE_DIR.'install/parameters8.php');
        require(BASE_DIR.'update/Library/Migration/To3_0/newParameters.php');
        $this->parameters = $parameterValue;
    }

    public function getValue($modGroup, $module, $parGroup, $parameter, $languageId = null)
    {
        return $this->parameters[$modGroup][$module][$parGroup][$parameter];
    }

}