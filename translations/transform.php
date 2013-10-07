<?php
/**
 * @package   ImpressPages
 */

// $this->escPar(
// $this->par(
// $parametersMod->getValue(

class Transform
{
    public static function firstStep()
    {

    }

    public function par($parameterKey, $variables = null){
        $parts = explode('/', $parameterKey);
        if (count($parts) != 4) {
            if (DEVELOPMENT_ENVIRONMENT) {
                throw new \Ip\CoreException("Can't find parameter: '" . $parameterKey . "'", \Ip\CoreException::VIEW);
            } else {
                return '';
            }
        }
        $value = $this->getValue($parts[0], $parts[1], $parts[2], $parts[3], $this->languageId);

        if (!empty($variables) && is_array($variables)) {
            foreach($variables as $variableKey => $variableValue) {
                $value = str_replace('[[' . $variableKey . ']]', $variableValue, $value);
            }
        }

        return $value;
    }

}

Transform::firstStep();


