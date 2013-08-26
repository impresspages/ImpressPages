<?php
/**
 * @package   ImpressPages
 */

namespace Modules\standard\design;


class Service
{

    protected function __construct()
    {
    }

    /**
     * @return Service
     */
    public static function instance()
    {
        return new Service();
    }

    public function compileThemeLess($themeName, $filename)
    {
        $lessCompiler = LessCompiler::instance();
        return $lessCompiler->compile($themeName, $filename);
    }

    /**
     * @param string $name
     * @param string $themeName
     * @return string
     */
    public function getThemeOption($name, $default = null, $themeName = THEME)
    {
        $configModel = ConfigModel::instance();
        $value = $configModel->getConfigValue($themeName, $name, $default);
        return $value;
    }
}