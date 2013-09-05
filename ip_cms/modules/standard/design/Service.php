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
        return $lessCompiler->getCompiledCssUrl($themeName, $filename);
    }

    /**
     * @param $themeName
     * @param $filename
     * @return string url to real time compiled less. Available only with admin login.
     */
    public function getRealTimeUrl($themeName, $filename) {
        $site = \Ip\ServiceLocator::getSite();
        $data = array(
            'g' => 'standard',
            'm' => 'design',
            'ba' => 'realTimeLess',
            'params' => array(
                'filename' => $filename
            ),
            'rpc' => '2.0'
        );
        $site->generateUrl(null, null, array(), $data);
    }

    /**
     * @param string $name
     * @param string $default
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