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
     * Return a name of the plugin the widget belongs to.
     *
     * @return string Plugin name
     */
    public function getPluginName()
    {
        return $this->pluginName;
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
     * Check if the widget is native ImpressPages widget
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

        return ipFileUrl('Ip/Internal/Content/assets/img/iconWidget.svg');
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
     * @throws \Ip\Exception\Content
     */
    public function getSkins()
    {

        $views = array();


        //collect default view files
        $skinDir = ipFile($this->widgetDir . self::SKIN_DIR . '/');


        if (!is_dir($skinDir)) {
            throw new \Ip\Exception\Content('Skins directory does not exist. ' . esc($skinDir));
        }

        $availableViewFiles = scandir($skinDir);
        foreach ($availableViewFiles as $viewFile) {
            if (is_file($skinDir . $viewFile) && substr($viewFile, -4) == '.php') {
                $views[substr($viewFile, 0, -4)] = 1;
            }
        }
        //collect overridden theme view files
        if ($this->isCore()) {
            $themeViewsFolder = ipThemeFile(
                \Ip\View::OVERRIDE_DIR . '/Ip/Internal/' . $this->pluginName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::SKIN_DIR
            );
        } else {
            $themeViewsFolder = ipThemeFile(
                \Ip\View::OVERRIDE_DIR . '/Plugin/' . $this->pluginName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::SKIN_DIR
            );
        }
        if (is_dir($themeViewsFolder)) {
            $availableViewFiles = scandir($themeViewsFolder);
            foreach ($availableViewFiles as $viewFile) {
                if (is_file($themeViewsFolder . '/' . $viewFile) && substr($viewFile, -4) == '.php') {
                    $views[substr($viewFile, 0, -4)] = 1;
                }
            }
        }

        $skins = array();
        foreach ($views as $viewKey => $view) {
            if ($this->isCore()) {
                $translation = __(ucfirst($viewKey), 'Ip-admin', false);
            } else {
                $translation = __(ucfirst($viewKey), $this->pluginName, false);
            }
            $skins[] = array('name' => $viewKey, 'title' => $translation);
        }

        if (empty($skins)) {
            throw new \Ip\Exception\Content('No skins');
        }

        return $skins;
    }

    /**
     * Update widget data
     *
     * This method is executed each time the widget data is updated.
     *
     * @param int $widgetId Widget ID
     * @param array $postData
     * @param array $currentData
     * @return array Data to be stored to the database
     */
    public function update($widgetId, $postData, $currentData)
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
     * widgetId=actualWidgetId
     *
     * then that post request will be redirected to the specified method.
     *
     * Use return new \Ip\Response\Json($jsonArray) to return JSON.
     *
     * Attention: this method is accessible for website visitors without admin login.
     *
     * @param int $widgetId Widget ID
     * @param array $data Widget Data array
     * @return mixed
     */
    public function post($widgetId, $data)
    {

    }

    /**
     * Duplicate widget action
     *
     * This function is executed after the widget has been duplicated.
     * All widget data is duplicated automatically. This method is used only in case a widget
     * needs to do some maintenance tasks on duplication.
     *
     * @param int $oldId Old widget ID
     * @param int $newId Duplicated widget ID
     * @param array $data Data that has been duplicated from old widget to the new one
     * @return array
     */
    public function duplicate($oldId, $newId, $data)
    {
        return $data;
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


    public function adminHtmlSnippet()
    {
        $snippetDir = ipFile($this->getWidgetDir() . \Ip\Internal\Content\Model::SNIPPET_DIR) . '/';
        if (!is_dir($snippetDir)) {
            return array();
        }
        $snippetFiles = scandir($snippetDir);
        $snippet = '';
        foreach ($snippetFiles as $snippetFile) {
            if (is_file($snippetDir . $snippetFile) && substr($snippetFile, -4) == '.php') {
                $snippet .= ipView($snippetDir . $snippetFile)->render();
            }
        }

        return $snippet;

    }


    /**
     * Renders widget's HTML output
     *
     * You can extend this method when generating widget's HTML.
     *
     * @param int $revisionId Widget revision ID
     * @param int $widgetId Widget ID
     * @param int $widgetId Widget instance ID
     * @param array $data Widget data array
     * @param string $skin Skin name
     * @return string Widget's HTML code
     */

    public function generateHtml($revisionId, $widgetId, $data, $skin)
    {

        try {
            if ($this->core) {
                $skinFile = 'Ip/Internal/' . $this->pluginName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::SKIN_DIR . '/' . $skin . '.php';
            } else {
                $skinFile = 'Plugin/' . $this->pluginName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::SKIN_DIR . '/' . $skin . '.php';
            }
            if (!is_file(ipFile($skinFile)) && !is_file(ipThemeFile(\Ip\View::OVERRIDE_DIR . '/' . $skinFile))) {
                $skin = 'default';
                if ($this->core) {
                    $skinFile = 'Ip/Internal/' . $this->pluginName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::SKIN_DIR . '/' . $skin . '.php';
                } else {
                    $skinFile = 'Plugin/' . $this->pluginName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::SKIN_DIR . '/' . $skin . '.php';
                }
            }

            $answer = ipView($skinFile, $data)->render();

        } catch (\Ip\Exception $e) {
            if (ipIsManagementState()) {
                $answer = $e->getMessage() . "\n " . $e->getTraceAsString();
            } else {
                $answer = '';
            }
        }
        return $answer;
    }

    /**
     * Process data which is passed to widget's JavaScript file for processing
     *
     * @param int $revisionId Widget revision ID
     * @param int $widgetId Widget ID
     * @param int $widgetId Widget instance ID
     * @param array $data Widget data array
     * @param string $skin Widget skin name
     * @return array Data array
     */
    public function dataForJs($revisionId, $widgetId, $data, $skin)
    {
        return $data;
    }


    /**
     * Array 0f menu items to be added to the widget's options menu. (gear box on the left top corner of the widget)
     * @param $revisionId
     * @param $widgetId
     * @param $data
     * @param $skin
     * @return array
     */
    public function optionsMenu($revisionId, $widgetId, $data, $skin)
    {
        return array();

//        example with one menu item
//        $answer = array();
//        $answer[] = array(
//            'title' => 'Menu item title',
//            'attributes' => array(
//                'class' => 'ipsMyMenu', //class to be added. Could be used in JS to bind actions on this button
//                'data-somedata' => json_encode('lorem ipsum'), //data that later can be accessed in js. E.g. $('.ipsMyMenu').first().data('somedata');
//                ...
//            )
//        );
//        return $answer;
    }


}
