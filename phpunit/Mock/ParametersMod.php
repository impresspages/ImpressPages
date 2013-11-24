<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace PhpUnit\Mock;
//TODOX remove
class ParametersMod
{
    protected $parameters;

    public function __construct()
    {
        $parameterValue = array(); // in order to silence IDE :)
        require ipConfig()->coreModuleFile('Install/parameters.php');
        require ipConfig()->baseFile('update/Library/Migration/To3_0/newParameters.php');
        $this->parameters = $parameterValue;
    }

    public function getValue($modGroup, $module, $parGroup, $parameter, $languageId = null)
    {
        return $this->parameters[$modGroup][$module][$parGroup][$parameter];
    }

}