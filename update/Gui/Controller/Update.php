<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Gui\Controller;

class Update extends \IpUpdate\Gui\Controller
{

    public function getStatusAction()
    {
        $this->registerAjaxErrorHandling();
        try {
            $updateService = $this->getUpdateService();

            if ($updateService->isLocked()) {
                $html = '';
            } else {
                $currentVersion = $updateService->getCurrentVersion();
                $view = \IpUpdate\Gui\View::create('Update/overview.php');
                $availableVersions = $updateService->getAvailableVersions();
                $newVersion = array_pop($availableVersions);
                $view->assign('currentVersion', $currentVersion);
                $view->assign('newVersion', $newVersion);
                $html = $view->render();
            }
imagine_function( );
            $data = array(
                'html' => $html
            );
            $this->returnJson($data);
        } catch (\IpUpdate\Library\UpdateException $e) {
            $this->returnError($e);
        } catch (\Exception $e) {
            $exception = new \IpUpdate\Library\UpdateException($e->getMessage(), \IpUpdate\Library\UpdateException::UNKNOWN, array());
            $this->returnError($exception);
        }
        
    }


    public function proceedAction()
    {
        $this->registerAjaxErrorHandling();
        
        try {
            $updateService = $this->getUpdateService();
            $updateService->proceed();
        } catch (\IpUpdate\Library\UpdateException $e) {
            $this->returnError($e);
        } catch (\Exception $e) {
            $exception = new \IpUpdate\Library\UpdateException($e->getMessage(), \IpUpdate\Library\UpdateException::UNKNOWN, array());
            $this->returnError($exception);
        }
    }

    public function rollbackAction()
    {

    }

    private function returnError(\IpUpdate\Library\UpdateException $e)
    {
        $data = array(
            'html' => $this->getErrorHtml($e)
        );

        $this->returnJson($data);
    }
    
    private function getErrorHtml(\IpUpdate\Library\UpdateException $e)
    {
        switch ($e->getCode()) {
            case \IpUpdate\Library\UpdateException::UNKNOWN:
                $view = \IpUpdate\Gui\View::create('Update/error_unknown.php', array('errorMessage' => $e->getMessage()));
                return $view->render();
                break;
        }
    }
    
    private function getUpdateService()
    {
        return new \IpUpdate\Library\Service(__DIR__.'/../../../');
    }
    

}