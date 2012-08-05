<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\developer\inline_management;
if (!defined('CMS')) exit;



class Controller extends \Ip\Controller{


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

        $dao = new Dao();

        $logo = $dao->getValueLogo();
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



        $dao = new Dao();
        $value = new Value($type, $value);
        $dao->setValueLogo($value);
        //switch()
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







}
