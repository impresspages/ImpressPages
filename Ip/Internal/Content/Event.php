<?php


namespace Ip\Internal\Content;


class Event
{
    protected static function addWidgetAssets(\Ip\WidgetController $widget)
    {
        $pluginAssetsPath = $widget->getWidgetDir() . \Ip\Application::ASSETS_DIR . '/';
        static::includeResources($pluginAssetsPath);
    }

    private static function includeResources($resourcesFolder)
    {

        if (is_dir(ipFile($resourcesFolder))) {
            $files = scandir(ipFile($resourcesFolder));
            if ($files === false) {
                return;
            }


            foreach ($files as $file) {
                if (is_dir(ipFile($resourcesFolder . $file)) && $file != '.' && $file != '..') {
                    static::includeResources(ipFile($resourcesFolder . $file));
                    continue;
                }
                if (strtolower(substr($file, -3)) == '.js') {
                    ipAddJs($resourcesFolder . $file);
                }
                if (strtolower(substr($file, -4)) == '.css') {
                    ipAddCss($resourcesFolder . $file);
                }
            }
        }
    }

    public static function ipBeforeController()
    {
        $ipUrlOverrides = ipConfig()->get('urlOverrides');
        if (!$ipUrlOverrides) {
            $ipUrlOverrides = [];
        }

        ipAddJsVariable('ipUrlOverrides', $ipUrlOverrides);

        // Add widgets
        //TODO cache found assets to decrease file system usage
        $widgets = Service::getAvailableWidgets();

        if (ipIsManagementState()) {
            foreach ($widgets as $widget) {
                if (!$widget->isCore()) { //core widget assets are included automatically in one minified file
                    static::addWidgetAssets($widget);
                }
            }
            ipAddJsVariable('ipPublishTranslation', __('Publish', 'Ip-admin', false));
        }
    }

    /**
     * Used when management is needed in controller routed using routes.
     * @param $info
     * @return null
     */
    public static function ipBeforeController_70($info)
    {
        if (empty($info['page']) || empty($info['management']) || !ipIsManagementState()) {
            return null;
        }

        //find current page
        $page = $info['page'];

        // change layout if safe mode
        if (\Ip\Internal\Admin\Service::isSafeMode()) {
            ipSetLayout(ipFile('Ip/Internal/Admin/view/safeModeLayout.php'));
        } else {
            ipSetLayout($page->getLayout());
        }

        // initialize management
        if (!ipRequest()->getQuery('ipDesignPreview') && !ipRequest()->getQuery('disableManagement')) {
            Helper::initManagement();
        }

        //show page content
        $response = ipResponse();
        $response->setDescription(\Ip\ServiceLocator::content()->getDescription());
        $response->setKeywords(ipContent()->getKeywords());
        $response->setTitle(ipContent()->getTitle());
    }

    public static function ipAdminLoginSuccessful($info)
    {
        Service::setManagementMode(1);
    }


    public static function ipPageRevisionDuplicated($info)
    {
        Model::duplicateRevision($info['basedOn'], $info['newRevisionId']);
    }

    public static function ipPageRemoved($info)
    {
        Model::removePageRevisions($info['pageId']);
    }

    public static function ipUrlChanged($info)
    {
        $httpExpression = '/^((http|https):\/\/)/i';
        if (!preg_match($httpExpression, $info['oldUrl'])) {
            return;
        }
        if (!preg_match($httpExpression, $info['newUrl'])) {
            return;
        }
        Model::updateUrl($info['oldUrl'], $info['newUrl']);
    }





    public static function ipCronExecute($info)
    {
        if ($info['firstTimeThisDay'] || $info['test']) {
            if (ipGetOption('Config.removeOldRevisions', 0)) {
                \Ip\Internal\Revision::removeOldRevisions(ipGetOption('Config.removeOldRevisionsDays', 720));
            }
        }
    }

}
