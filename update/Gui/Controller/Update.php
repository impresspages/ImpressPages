<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace IpUpdate\Gui\Controller;

class Update extends \IpUpdate\Gui\Controller
{

    public function getStatusAction()
    {
        $this->registerAjaxErrorHandling();
        try {
            
            $updateService = $this->getUpdateService();
            
            $destinationVersion = $updateService->getDestinationVersion();

            if ($destinationVersion) {
                $currentVersion = $updateService->getCurrentVersion();
                $view = \IpUpdate\Gui\View::create('Update/overview.php');
                $view->assign('currentVersion', $currentVersion);
                $view->assign('destinationVersion', $updateService->getDestinationVersion());
                $view->assign('notes', $updateService->getUpdateNotes());
                $html = $view->render();
                $data = array(
                    'html' => $html
                );
                $this->returnJson($data);
            } else {
                $data = array(
                    'status' => 'success',
                    'html' => \IpUpdate\Gui\View::create('Update/completed.php')->render()
                );
                $this->returnJson($data);
            }



        } catch (\IpUpdate\Library\UpdateException $e) {
            $this->returnError($e);
        }
    }


    public function proceedAction()
    {
        $this->registerAjaxErrorHandling();
        
        try {
            $updateService = $this->getUpdateService();
            $updateService->proceed();
            $data = array(
                'status' => 'success',
                'action' => 'reload'
            );
            $this->returnJson($data);
        } catch (\IpUpdate\Library\UpdateException $e) {
            $this->returnError($e);
        }
    }
    
    public function resetLockAction()
    {
        $this->registerAjaxErrorHandling();
        
        try {
            $updateService = $this->getUpdateService();
            $updateService->resetLock();
            $updateService->proceed();
            $data = array(
                'status' => 'success',
                'action' => 'reload'
            );
            $this->returnJson($data);
        } catch (\IpUpdate\Library\UpdateException $e) {
            $this->returnError($e);
        }
    }

    public function rollbackAction()
    {

    }

    private function returnError(\IpUpdate\Library\UpdateException $e)
    {
        $data = array(
            'status' => 'error',
            'errorCode' => $e->getCode(),
            'html' => $this->getErrorHtml($e)
        );

        $this->returnJson($data);
    }
    
    private function getErrorHtml(\IpUpdate\Library\UpdateException $e)
    {
        switch ($e->getCode()) {
            case \IpUpdate\Library\UpdateException::SQL:
            case \IpUpdate\Library\UpdateException::WRONG_CHECKSUM:
            case \IpUpdate\Library\UpdateException::UNKNOWN:
            case \IpUpdate\Library\UpdateException::EXTRACT_FAILURE:
            default:
                $view = \IpUpdate\Gui\View::create('Update/error_unknown.php', array('errorMessage' => $e->getMessage()));
                return $view->render();
                break;
            case \IpUpdate\Library\UpdateException::WRITE_PERMISSION:
                $view = \IpUpdate\Gui\View::create('Update/error_write_permission.php', array('file' => $e->getValue('file'), 'errorMessage' => $e->getMessage()));
                return $view->render();
                break;
            case \IpUpdate\Library\UpdateException::IN_PROGRESS:
                $view = \IpUpdate\Gui\View::create('Update/error_in_progress.php', array('errorMessage' => $e->getMessage()));
                return $view->render();
                break;
            case \IpUpdate\Library\UpdateException::CURL_REQUIRED:
                $view = \IpUpdate\Gui\View::create('Update/error_curl_required.php', array('errorMessage' => $e->getMessage()));
                return $view->render();
                break;
                
        }
    }
    
    /**
     * @return \IpUpdate\Library\Service
     */
    private function getUpdateService()
    {
        return new \IpUpdate\Library\Service(__DIR__.'/../../../');
    }
    

}