<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip;

use Ip\Internal\Content\Model;

/**
 * Controller for widgets
 * @package Ip
 */
class WidgetController
{
    protected $name;
    protected $pluginName;

    /**
     * @var boolean - true if widget is installed by default
     */
    protected $core;
    const SKIN_DIR = 'skin';

    protected $widgetDir;
    protected $widgetAssetsDir;

    public function __construct($name, $pluginName, $core = false)
    {
        $this->name = $name;
        $this->pluginName = $pluginName;
        $this->core = $core;

        if ($this->core) {

            $this->widgetDir = 'Ip/Internal/' . $pluginName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/';
        } else {
            $this->widgetDir = 'Plugin/' . $pluginName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/';
        }

        $this->widgetAssetsDir = $this->widgetDir . \Ip\Application::ASSETS_DIR . '/';
    }

    /**
     * Gets widget title
     *
     * Override this method to set the widget name displayed in widget toolbar.
     *
     * @return string Widget's title
     */
    public function getTitle()
    {
        return self::getName();
    }

    /**
     * Return a name, which is unique widget identifier
     *
     * @return string Widget's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get widget's administration directory
     *
     * @return string Widget's directory path
     */
    public function getWidgetDir()
    {
        return $this->widgetDir;
    }

    /**
     * Check if the widget is native ImpressPages CMS widget
     *
     * @return bool Returns false, if a widget is provided by installable plugin.
     */
    public function isCore()
    {
        return $this->core;
    }

    /**
     * Get widget icon URL
     *
     * Widget icon is displayed in widget toolbar of administration page.
     *
     * @return string Icon URL
     */
    public function getIcon()
    {
        if ($this->core) {
            if (file_exists(ipFile($this->widgetAssetsDir . 'icon.svg'))) {
                return ipFileUrl($this->widgetAssetsDir . 'icon.svg');
            }
            if (file_exists(ipFile($this->widgetAssetsDir . 'icon.png'))) {
                return ipFileUrl($this->widgetAssetsDir . 'icon.png');
            }
        } else {
            if (file_exists(ipFile($this->widgetAssetsDir . 'icon.svg'))) {
                return ipFileUrl($this->widgetAssetsDir . 'icon.svg');
            }
            if (file_exists(ipFile($this->widgetAssetsDir . 'icon.png'))) {
                return ipFileUrl($this->widgetAssetsDir . 'icon.png');
            }
        }

        return ipFileUrl('Ip/Internal/Content/assets/img/iconWidget.png');
    }

    /**
     * Override this method to set default data of the widget
     *
     * @return array Default data
     */
    public function defaultData()
    {
        return array();
    }

    /**
     * Get all widget skins
     *
     * @return array List of skins
     * @throws Internal\Content\Exception
     */
    public function getSkins()
    {

        $views = array();


        //collect default view files
        $skinDir = ipFile($this->widgetDir . self::SKIN_DIR . '/');


        if (!is_dir($skinDir)) {
            throw new \Ip\Internal\Content\Exception('Skins directory does not exist. ' . $skinDir, \Ip\Internal\Content\Exception::NO_SKIN);
        }

        $availableViewFiles = scandir($skinDir);
        foreach ($availableViewFiles as $viewFile) {
            if (is_file($skinDir . $viewFile) && substr($viewFile, -4) == '.php') {
                $views[substr($viewFile, 0, -4)] = 1;
            }
        }
        //collect overridden theme view files
        $themeViewsFolder = ipThemeFile(\Ip\View::OVERRIDE_DIR . '/' . $this->pluginName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::SKIN_DIR);
        if (is_dir($themeViewsFolder)){
            $availableViewFiles = scandir($themeViewsFolder);
            foreach ($availableViewFiles as $viewFile) {
                if (is_file($themeViewsFolder . '/' . $viewFile) && substr($viewFile, -4) == '.php') {
                    $views[substr($viewFile, 0, -4)] = 1;
                }
            }
        }

        $skins = array();
        foreach ($views as $viewKey => $view) {
            $translation = __('Skin_' . $viewKey, $this->pluginName, false);
            $skins[] = array('name' => $viewKey, 'title' => $translation);
        }

        if (empty($skins)) {
            throw new \Ip\Internal\Content\Exception('No skins', Exception::NO_SKIN);
        }

        return $skins;
    }

    /**
     * Update widget data
     *
     * This method is executed each time the widget data is updated.
     *
     * @param $widgetId Widget ID
     * @param $postData
     * @param $currentData
     * @return array Data to be stored to the database
     */
    public function update ($widgetId, $postData, $currentData)
    {
        return $postData;
    }

    /**
     * Process post data submitted in public mode
     *
     * You can make posts directly to your widget (e.g., when submitting HTML form in public page).
     *
     * If you pass the following parameters:
     * sa=Content.widgetPost
     * securityToken=actualSecurityToken
     * instanceId=actualWidgetInstanceId
     *
     * then that post request will be redirected to the specified method.
     *
     * Use return new \Ip\Response\Json($jsonArray) to return JSON.
     *
     * Attention: this method is accessible for website visitors without admin login.
     *
     * @param int $instanceId Widget instance ID
     * @param array $data Widget Data array
     */
    public function post ($instanceId, $data)
    {

    }

    /**
     * Duplicate widget action
     *
     * This function is executed after the widget is being duplicated.
     * All widget data is duplicated automatically. This method is used only in case a widget
     * needs to do some maintenance tasks on duplication.
     *
     * @param int $oldId Old widget ID
     * @param int $newId Duplicated widget ID
     * @param array $data Data that has been duplicated from old widget to the new one
     */
    public function duplicate($oldId, $newId, $data)
    {

    }

    /**
     * Delete a widget
     *
     * This method is executed before actual deletion of a widget.
     * It is used to remove widget data (e.g., photos, files, additional database records and so on).
     * Standard widget data is being deleted automatically. So you don't need to extend this method
     * if your widget does not upload files or add new records to the database manually.
     * @param int $widgetId Widget ID
     * @param array $data Data that is being stored in the widget
     */
    public function delete($widgetId, $data)
    {

    }



    /**
     * Renders widget's HTML output
     *
     * You can extend this method when generating widget's HTML.
     *
     * @param $revisionId Widget revision ID
     * @param $widgetId Widget ID
     * @param $instanceId Widget instance ID
     * @param array|null $data Widget data array
     * @param string $skin Skin name
     * @return string Widget's HTML code
     */

    public function generateHtml($revisionId, $widgetId, $instanceId, $data, $skin)
    {
        $answer = '';
        try {
            if ($this->core) {
                $answer = ipView(ipFile('Ip/Internal/' . $this->pluginName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::SKIN_DIR.'/'.$skin.'.php'), $data)->render();
            } else {
                $answer = ipView(ipFile('Plugin/' . $this->pluginName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::SKIN_DIR.'/'.$skin.'.php'), $data)->render();
            }
        } catch (\Ip\Exception $e) {
            if (ipIsManagementState()) {
                $answer = $e->getTraceAsString();
            } else {
                $answer = '';
            }
        }
        return $answer;
    }

    /**
     * Process data which is passed to widget's JavaScript file for processing
     *
     * @param $revisionId Widget revision ID
     * @param $widgetId Widget ID
     * @param $instanceId Widget instance ID
     * @param $data Widget data array
     * @param $skin Widget skin name
     * @return array Data array
     */
    public function dataForJs($revisionId, $widgetId, $instanceId, $data, $skin)
    {
        return $data;
    }


}
