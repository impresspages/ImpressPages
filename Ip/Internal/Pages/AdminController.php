<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Pages;





class AdminController extends \Ip\Controller
{


    public function index()
    {
        ipAddJs('Ip/Internal/Core/assets/js/angular.js');
        ipAddJs('Ip/Internal/Pages/assets/js/pages.js');
        ipAddJs('Ip/Internal/Pages/assets/js/menuList.js');
        ipAddJs('Ip/Internal/Pages/assets/js/jquery.pageTree.js');
        ipAddJs('Ip/Internal/Pages/assets/js/jquery.pageProperties.js');
        ipAddJs('Ip/Internal/Pages/assets/jstree/jquery.jstree.js');
        ipAddJs('Ip/Internal/Pages/assets/jstree/jquery.cookie.js');
        ipAddJs('Ip/Internal/Pages/assets/jstree/jquery.hotkeys.js');

        ipAddJs('Ip/Internal/Grid/assets/grid.js');
        ipAddJs('Ip/Internal/Grid/assets/gridInit.js');

        ipAddJsVariable('languageList', Helper::languageList());
        ipAddJsVariable('menuList', Model::getMenuList(ipContent()->getCurrentLanguage()->getCode()));

        $variables = array(
            'addPageForm' => Helper::addPageForm(),
            'addMenuForm' => Helper::addMenuForm(),
            'languagesUrl' => ipConfig()->baseUrl() . '?aa=Languages.index'
        );
        $layout = ipView('view/layout.php', $variables);

        ipResponse()->setLayoutVariable('removeAdminContentWrapper',true);

        return $layout->render();
    }

    public function pagesGridGateway()
    {
        $parentId = ipRequest()->getRequest('parentId');
        if (!$parentId) {
            throw new \Ip\Exception('Missing required parameter');
        }

        $worker = new \Ip\Internal\Grid\Worker(Helper::pagesGridConfig($parentId));
        $result = $worker->handleMethod(ipRequest());
        return new \Ip\Response\JsonRpc($result);
    }

    public function getPages()
    {
        $data = ipRequest()->getRequest();
        if (empty($data['languageId'])) {
            throw new \Ip\Exception("Missing required parameters");
        }
        $language = ipContent()->getLanguage($data['languageId']);
        if (!$language) {
            throw new \Ip\Exception("Language doesn't exist. " . $data['languageId']);
        }
        $languageCode = $language->getCode();

        if (empty($data['menuName'])) {
            throw new \Ip\Exception("Missing required parameters");
        }
        $menuName = $data['menuName'];

        $parentId = ipDb()->selectValue('page', 'id', array('languageCode' => $languageCode, 'alias' => $menuName, 'isDeleted' => 0));
        $responseData = array (
            'tree' => JsTreeHelper::getPageTree($languageCode, $parentId)
        );

        return new \Ip\Response\Json($responseData);

    }

    public function pagePropertiesForm()
    {
        $pageId = ipRequest()->getQuery('pageId');
        if (!$pageId) {
            throw new \Ip\Exception("Missing required parameters");
        }

        $variables = array(
            'form' => Helper::pagePropertiesForm($pageId)
        );
        $layout = ipView('view/pageProperties.php', $variables)->render();

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
            throw new \Ip\Exception("Missing required parameters");
        }
        $pageId = (int)$data['pageId'];

        $answer = array();
        if (strtotime($data['createdAt']) === FALSE) {
            $answer['errors']['createdAt'] = __('Incorrect date format. Example:', 'ipAdmin', FALSE).date(" Y-m-d");
        }

        if (strtotime($data['updatedAt']) === FALSE) {
            $answer['errors']['updatedAt'] = __('Incorrect date format. Example:', 'ipAdmin', FALSE).date(" Y-m-d");
        }


        $data['isVisible'] = !empty($data['isVisible']);
        $data['isDisabled'] = !empty($data['isDisabled']);
        $data['isSecured'] = !empty($data['isSecured']);
        $data['isBlank'] = !empty($data['isBlank']);
        if (empty($answer['errors'])) {
            Model::updatePageProperties($pageId, $data);
            Model::changePageUrlPath($pageId, $data['urlPath']);
            $answer['status'] = 'success';
        } else {
            $answer['status'] = 'error';
        }

        return new \Ip\Response\Json($answer);
    }

    public function updateMenuForm()
    {
        $menuId = (int)ipRequest()->getQuery('id');
        if (empty($menuId)) {
            throw new \Ip\Exception("Missing required parameters");
        }

        $form = Helper::menuForm($menuId);
        $html = $form->render();

        $data = array (
            'html' => $html
        );
        return new \Ip\Response\Json($data);
    }





    public function addPage()
    {
        ipRequest()->mustBePost();

        $parentId = ipRequest()->getPost('parentId');
        if (empty($parentId)) {
            throw new \Ip\Exception("Missing required parameters");
        }

        $title = ipRequest()->getPost('title');
        if (empty($title)) {
            $title = __('Untitled', 'ipAdmin', false);
        }

        $isVisible = ipRequest()->getPost('isVisible', 0);

        $pageId = Service::addPage($parentId, $title, array('isVisible' => $isVisible));


        $answer = array(
            'status' => 'success',
            'pageId' => $pageId
        );

        return new \Ip\Response\Json($answer);

    }

    public function deletePage()
    {
        ipRequest()->mustBePost();

        $pageId = (int)ipRequest()->getPost('pageId');
        if (!$pageId) {
            throw new \Ip\Exception("Page id is not set");
        }

        Service::deletePage($pageId);

        $answer = array ();
        $answer['status'] = 'success';

        return new \Ip\Response\Json($answer);
    }

    public function movePage()
    {
        ipRequest()->mustBePost();
        $data = ipRequest()->getPost();


        if (!isset($data['pageId'])) {
            throw new \Ip\Exception("Page id is not set");
        }
        $pageId = (int)$data['pageId'];


        if (!empty($data['destinationParentId'])) {
            $destinationParentId = $data['destinationParentId'];
        } else {
            if (!isset($data['languageId'])) {
                throw new \Ip\Exception("Missing required parameters");
            }
            throw new \Ip\Exception\NotImplemented();
        }


        if (!isset($data['destinationPosition'])) {
            throw new \Ip\Exception("Destination position is not set");
        }
        $destinationPosition = $data['destinationPosition'];


        try {
            Model::movePage($pageId, $destinationParentId, $destinationPosition);
        } catch (\Ip\Exception $e) {
            $answer = array (
                'status' => 'error',
                'error' => $e->getMessage()
            );
            return new \Ip\Response\Json($answer);
        }


        $answer = array (
            'status' => 'success'
        );

        return new \Ip\Response\Json($answer);



    }


    public function copyPage()
    {
            ipRequest()->mustBePost();
            $data = ipRequest()->getPost();


            if (!isset($data['pageId'])) {
                throw new \Ip\Exception("Page id is not set");
            }
            $pageId = (int)$data['pageId'];


            if (!empty($data['destinationParentId'])) {
                $destinationParentId = $data['destinationParentId'];
            }

            if (!isset($data['destinationPosition'])) {
                throw new \Ip\Exception("Destination position is not set");
            }
            $destinationPosition = $data['destinationPosition'];


            try {
                Service::copyPage($pageId, $destinationParentId, $destinationPosition);
            } catch (\Ip\Exception $e) {
                $answer = array (
                    'status' => 'error',
                    'error' => $e->getMessage()
                );
                return new \Ip\Response\Json($answer);
            }


            $answer = array (
                'status' => 'success'
            );

            return new \Ip\Response\Json($answer);


    }

    public function getPageUrl()
    {
        $data = ipRequest()->getQuery();


        if (!isset($data['pageId'])) {
            throw new \Ip\Exception("Page id is not set");
        }
        $pageId = (int)$data['pageId'];

        $page = new \Ip\Page($pageId);

        $answer = array (
            'pageUrl' => $page->getLink()
        );

        return new \Ip\Response\Json($answer);
    }

    public function updateMenu()
    {
        $request = ipRequest();

        $menuId = $request->getPost('id');
        $title = $request->getPost('title');
        $alias = $request->getPost('alias');
        $layout = $request->getPost('layout');
        $type = $request->getPost('type');

        if (empty($menuId) || empty($title) || empty($alias) || empty($layout) || empty($type)) {
            throw new \Ip\Exception('Missing required parameters');
        }

        Service::updateMenu($menuId, $alias, $title, $layout, $type);

        $answer = array(
            'status' => 'success'
        );

        return new \Ip\Response\Json($answer);
    }


    public function createMenu()
    {
        $request = ipRequest();
        $request->mustBePost();
        $languageCode = $request->getPost('languageCode');
        $title = $request->getPost('title');
        $type = $request->getPost('type');

        if (empty($title) || empty($type)) {
            $title = __('Untitled', 'ipAdmin', false);
        }

        $transliterated = \Ip\Internal\Text\Transliteration::transform($title);
        $alias = preg_replace('/[^a-z0-9_\-]/i', '', strtolower($transliterated));

        $menuAlias = Service::createMenu($languageCode, $alias, $title);

        $menu = Service::getMenu($languageCode, $menuAlias);


        ipPageStorage($menu['id'])->set('menuType', $type);


        $answer = array(
            'status' => 'success',
            'menuName' => $menuAlias
        );

        return new \Ip\Response\Json($answer);
    }

    public function changeMenuOrder()
    {
        ipRequest()->mustBePost();
        $data = ipRequest()->getPost();

        $menuId = ipRequest()->getPost('menuId');
        $newIndex = ipRequest()->getPost('newIndex');

        if (empty($menuId) || !isset($newIndex)) {
            throw new \Ip\Exception("Missing required parameters");
        }

        Model::changeMenuOrder($menuId, $newIndex);

        return new \Ip\Response\Json(array(
            'error' => 0
        ));
    }


}
