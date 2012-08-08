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
            if ($logo->getImageOrig() && file_exists(BASE_DIR.FILE_DIR.$logo->getImageOrig()) && is_file(BASE_DIR.FILE_DIR.$logo->getImageOrig())) {
                unlink($logo->getImageOrig());
            }

            $fileFunctions = new Library\Php\File\Functions();
            $fileFunctions->
            $logo->setImage();
            //new original image
            $newData['imageOriginal'] = \Modules\administrator\repository\Model::addFile($_POST['newImage'], 'standard/content_management', $widgetId);

            //remove old big image
            if (isset($currentData['imageBig']) && $currentData['imageBig']) {
                \Modules\administrator\repository\Model::unbindFile($currentData['imageBig'], 'standard/content_management', $widgetId);
            }


            //new big image
            $tmpBigImageName = $this->cropBigImage($_POST['newImage']);
            $newData['imageBig'] = \Modules\administrator\repository\Model::addFile(TMP_IMAGE_DIR.$tmpBigImageName, 'standard/content_management', $widgetId);
            //delete temporary file
            unlink(BASE_DIR.TMP_IMAGE_DIR.$tmpBigImageName);
        }

        if (isset($_POST['cropX1']) && isset($_POST['cropY1']) && isset($_POST['cropX2']) && isset($_POST['cropY2']) && isset($_POST['scale']) && isset($_POST['maxWidth'])) {
            //remove old file
            if ($logo->getImage() && file_exists(BASE_DIR.FILE_DIR.$logo->getImage()) && is_file(BASE_DIR.FILE_DIR.$logo->getImage())) {
                unlink($logo->getImage());
            }


            //new small image
            $newData['cropX1'] = $_POST['cropX1'];
            $newData['cropY1'] = $_POST['cropY1'];
            $newData['cropX2'] = $_POST['cropX2'];
            $newData['cropY2'] = $_POST['cropY2'];
            $newData['scale'] = $_POST['scale'];
            $newData['maxWidth'] = $_POST['maxWidth'];

            $tmpSmallImageName = $this->cropImage($newData['imageOriginal'], $newData['cropX1'], $newData['cropY1'], $newData['cropX2'], $newData['cropY2'], $newData['scale'], $_POST['maxWidth']);

            $newData['imageSmall'] = \Modules\administrator\repository\Model::addFile(TMP_IMAGE_DIR.$tmpSmallImageName, 'standard/content_management', $widgetId);

            //delete temporary file
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
