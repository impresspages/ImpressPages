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
        ipAddJavascript(ipFileUrl('Ip/Module/Pages/assets/js/jquery.pageProperties.js'));
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

        return new \Ip\Response\Json($responseData);

    }

    public function pagePropertiesForm()
    {
        $data = ipRequest()->getQuery();
        if (empty($data['zoneName'])) {
            throw new \Ip\CoreException("Missing required parameters");
        }
        $zoneName = $data['zoneName'];
        if (empty($data['pageId'])) {
            throw new \Ip\CoreException("Missing required parameters");
        }
        $pageId = $data['pageId'];


        $variables = array(
            'form' => Helper::pagePropertiesForm($zoneName, $pageId)
        );
        $layout = \Ip\View::create('view/pageProperties.php', $variables)->render();

        $data = array (
            'html' => $layout
        );
        return new \Ip\Response\Json($data);
    }

    public function updatePage()
    {
        ipRequest()->mustBePost();
        $data = ipRequest()->getPost();

        if (empty($data['pageId'])) {
            throw new \Ip\CoreException("Missing required parameters");
        }
        $pageId = (int)$data['pageId'];

        if (empty($data['zoneName'])) {
            throw new \Ip\CoreException("Missing required parameters");
        }
        $zoneName = $data['zoneName'];

        $answer = array();



        //make url
        if ($data['url'] == '') {
            if ($data['pageTitle'] != '') {
                $data['url'] = Db::makeUrl($data['pageTitle'], $pageId);
            } else {
                if ($data['navigationTitle'] != '') {
                    $data['url'] = Db::makeUrl($data['navigationTitle'], $pageId);
                }
            }
        } else {
            $tmpUrl = str_replace("/", "-", $data['url']);
            $i = 1;
            while (!Db::availableUrl($tmpUrl, $pageId)) {
                $tmpUrl = $data['url'].'-'.$i;
                $i++;
            }
            $data['url'] = $tmpUrl;
        }
        //end make url

        if (strtotime($data['createdOn']) === false) {
            $answer['errors'][] = array('field' => 'createdOn', 'message' => __('Incorrect date format. Example:', 'ipAdmin', false).date(" Y-m-d"));
        }

        if (strtotime($data['lastModified']) === false) {
            $answer['errors'][] = array('field' => 'lastModified', 'message' => __('Incorrect date format. Example:', 'ipAdmin', false).date(" Y-m-d"));
        }

//      TODOX implement
//        if ($data['type'] == 'redirect' && $data['redirectURL'] == '') {
//            $answer['errors'][] = array('field' => 'redirectURL', 'message' => __('External url can\'t be empty', 'ipAdmin', false));
//        }


        if (empty($answer['errors'])) {
            Service::updatePage($zoneName, $pageId, $data);
            $answer['status'] = 'success';
        } else {
            $answer['status'] = 'error';
        }

        return new \Ip\Response\Json($answer);



    }

}