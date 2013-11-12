<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content;


require_once \Ip\Config::oldModuleFile('standard/menu_management/db.php');

class SiteController extends \Ip\Controller{


    public function widgetPost()
    {
        global $site;

        if (!isset($_POST['instanceId'])) {
            $this->_errorAnswer('Mising instanceId POST variable');
            return;
        }
        $instanceId = $_POST['instanceId'];

        $widgetRecord = Model::getWidgetFullRecord($instanceId);

        try {
            if ($widgetRecord) {
                $widgetObject = Model::getWidgetObject($widgetRecord['name']);
                if ($widgetObject) {
                    $widgetObject->post($this, $instanceId, $_POST, $widgetRecord['data']);
                } else {
                    throw new Exception("Can't find requested Widget: ".$widgetRecord['name'], Exception::UNKNOWN_WIDGET);
                }
            } else {
                throw new Exception("Can't find requested Widget: ".$instanceId, Exception::UNKNOWN_INSTANCE);
            }
        } catch (Exception $e) {
            $this->_errorAnswer($e->getMessage());
        }
    }
    

    

    private function _errorAnswer($errorMessage) {
        $data = array (
            'status' => 'error',
            'errorMessage' => $errorMessage
        );

        $this->_outputAnswer($data);
    }


    private function _outputAnswer($data) {
        global $site;



        //header('Content-type: text/json; charset=utf-8'); throws save file dialog on firefox if iframe is used
        if (isset($data['managementHtml'])) {
            // $data['managementHtml'] = utf8_encode($data['managementHtml']);
        }
        $answer = json_encode(\Ip\Internal\Text\Utf8::checkEncoding($data));
        $site->setOutput($answer);
    }

}
