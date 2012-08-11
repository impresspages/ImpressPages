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
        global $parametersMod;
        $config = new Config();
        $availableFonts = $config->getAvailableFonts();

        $popupData = array(
            'availableFonts' => $availableFonts
        );

        $html = \Ip\View::create('view/popup/logo.php', $popupData)->render();

        $logoStr = $this->inlineValueService->getGlobalValue(self::PREFIX_LOGO);
        $logo = new Entity\Logo($logoStr);
        $logoData = array(
            'image' => IMAGE_DIR.$logo->getImage(),
            'imageOrig' => IMAGE_DIR.$logo->getImageOrig(),
            'requiredWidth' => $logo->getRequiredWidth(),
            'requiredHeight' => $logo->getRequiredHeight(),
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

        //STORE TEXT LOGO
        $logoStr = $this->inlineValueService->getGlobalValue(self::PREFIX_LOGO);
        $logo = new Entity\Logo($logoStr);

        $logo->setText($_POST['text']);
        $logo->setColor($_POST['color']);
        $logo->setFont($_POST['font']);

        //STORE IMAGE LOGO
        if (isset($_POST['newImage']) && file_exists(BASE_DIR.$_POST['newImage']) && is_file(BASE_DIR.$_POST['newImage'])) {

            if (TMP_FILE_DIR.basename($_POST['newImage']) != $_POST['newImage']) {
                throw new \Exception("Security notice. Try to access an image (".$_POST['newImage'].") from a non temporary folder.");
            }

            //remove old image
            if ($logo->getImageOrig() && file_exists(BASE_DIR.IMAGE_DIR.$logo->getImageOrig()) && is_file(BASE_DIR.IMAGE_DIR.$logo->getImageOrig())) {
                unlink(BASE_DIR.IMAGE_DIR.$logo->getImageOrig());
            }

            $destDir = BASE_DIR.IMAGE_DIR;
            $newName = \Library\Php\File\Functions::genUnoccupiedName($_POST['newImage'], $destDir);
            copy(BASE_DIR.$_POST['newImage'], $destDir.$newName);
            $logo->setImageOrig($newName);

        }

        if (isset($_POST['cropX1']) && isset($_POST['cropY1']) && isset($_POST['cropX2']) && isset($_POST['cropY2']) && isset($_POST['windowWidth'])&& isset($_POST['windowHeight'])) {
            //remove old file
            if ($logo->getImage() && file_exists(BASE_DIR.IMAGE_DIR.$logo->getImage()) && is_file(BASE_DIR.IMAGE_DIR.$logo->getImage())) {
                unlink(BASE_DIR.IMAGE_DIR.$logo->getImage());
            }


            //new small image
            $logo->setX1($_POST['cropX1']);
            $logo->setY1($_POST['cropY1']);
            $logo->setX2($_POST['cropX2']);
            $logo->setY2($_POST['cropY2']);
            $logo->setRequiredWidth($_POST['windowWidth']);
            $logo->setRequiredHeight($_POST['windowHeight']);

            $tmpSmallImageName = \Library\Php\Image\Functions::crop (
                BASE_DIR.IMAGE_DIR.$logo->getImageOrig(),
                TMP_IMAGE_DIR,
                $logo->getX1(),
                $logo->getY1(),
                $logo->getX2(),
                $logo->getY2(),
                100,
                $logo->getRequiredWidth(),
                $logo->getRequiredHeight()
            );

            $destDir = BASE_DIR.IMAGE_DIR;
            $newName = \Library\Php\File\Functions::genUnoccupiedName($tmpSmallImageName, $destDir);
            copy(TMP_IMAGE_DIR.$tmpSmallImageName, $destDir.$newName);
            $logo->setImage($newName);
            unlink(BASE_DIR.TMP_IMAGE_DIR.$tmpSmallImageName);
        }


        $this->inlineValueService->setGlobalValue(self::PREFIX_LOGO, $logo->getValueStr());


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
