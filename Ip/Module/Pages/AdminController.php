<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Module\Pages;





class AdminController extends \Ip\Controller
{

    public function init()
    {
        ipAddJavascript(ipFileUrl('Ip/Module/Pages/assets/js/angular.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Pages/assets/js/pages.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Pages/assets/js/jquery.pageTree.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Pages/assets/jstree/jquery.jstree.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Pages/assets/jstree/jquery.cookie.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Pages/assets/jstree/jquery.hotkeys.js'));

        ipAddCss(ipFileUrl('Ip/Module/Pages/assets/pages.css'));

        ipAddJavascriptVariable('languageList', Helper::languageList());
        ipAddJavascriptVariable('zoneList', Helper::zoneList());
    }

    public function index()
    {
        $layout = \Ip\View::create('view/layout.php');
        return $layout->render();
    }

    public function getPages()
    {
        $data = ipRequest()->getRequest();
        if (empty($data['languageId'])) {
            throw new \Ip\CoreException("Missing required parameters");
        }
        $languageId = null;


        if (empty($data['zoneName'])) {
            throw new \Ip\CoreException("Missing required parameters");
        }
        $zoneName = $data['zoneName'];

        $responseData = array (
            'tree' => JsTreeHelper::getPageTree($languageId, $zoneName)
        );

        return \Ip\Response\Json($responseData);

    }


}