<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\developer\inline_management;
if (!defined('CMS')) exit;



class Controller extends \Ip\Controller{

    const MODULE_NAME = 'inline_management';
    const PREFIX_STRING = 'str_';
    const PREFIX_TEXT = 'txt_';
    const PREFIX_IMAGE = 'img_';
    const PREFIX_LOGO = 'logo_';

    public function __construct()
    {
        $this->inlineValueService = new \Modules\developer\inline_value\Service(self::MODULE_NAME);
    }

    public function allowAction($action)
    {
        if (\Ip\Backend::loggedIn()) {
            return \Ip\Backend::userHasPermission(\Ip\Backend::userId(), 'standard', 'content_management');
        } else {
            return false;
        }
    }

    public function getManagementPopup()
    {
        $html = \Ip\View::create('view/popup/logo.php', array())->render();

        $logoStr = $this->inlineValueService->getGlobalValue(self::PREFIX_LOGO);
        $logo = new Entity\Logo($logoStr);
        $logoData = array(
            'image' => $logo->getImage(),
            'imageOrig' => $logo->getImageOrig(),
            'requiredWidth' => $logo->getRequiredWidth(),
            'type' => $logo->getType(),
            'x1' => $logo->getX1(),
            'y1' => $logo->getY1(),
            'x2' => $logo->getX2(),
            'y2' => $logo->getY2(),
            'text' => $logo->getText()
        );

        $data = array(
            "status" => "success",
            "logoData" => $logoData,
            "html" => $html
        );
        $this->returnJson($data);
    }

    public function saveLogo()
    {
        if (!isset($_POST['text']) || !isset($_POST['color']) || !isset($_POST['font'])) {
            $this->jsonError("Missing post data");
        }


        $dao = new Dao();
        $logo = $dao->getValueLogo();

        $logo.setText($_POST['text']);
        $logo.setColor($_POST['color']);
        $logo.setFont($_POST['font']);

        $dao->setValueLogo($logo);

        $data = array(
            "status" => "success"
        );
        $this->returnJson($data);
    }

    public function saveImage()
    {

    }


    public function saveString()
    {

    }


    public function saveText()
    {

    }



    private function jsonError($errorMessage)
    {
        $data = array(
            "status" => "error",
            "error" => $errorMessage
        );
        $this->returnJson($data);
    }


}
