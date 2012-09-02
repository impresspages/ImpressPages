<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\developer\inline_management;
use Modules\developer\inline_value\Entity\Scope as Scope;



class Controller extends \Ip\Controller{

    var $dao;

    public function __construct()
    {

        $this->dao = new Dao();
    }

    public function allowAction($action)
    {
        if (\Ip\Backend::loggedIn()) {
            return \Ip\Backend::userHasPermission(\Ip\Backend::userId(), 'standard', 'content_management');
        } else {
            return false;
        }
    }



    public function getManagementPopupLogo()
    {

        $config = new Config();
        $availableFonts = $config->getAvailableFonts();

        $popupData = array(
            'availableFonts' => $availableFonts
        );

        $html = \Ip\View::create('view/popup/logo.php', $popupData)->render();

        $logoStr = $this->dao->getGlobalValue(Dao::PREFIX_LOGO, '');
        $logo = new Entity\Logo($logoStr);
        $logoData = array(
            'type' => $logo->getType(),
            'image' => $logo->getImage() ? $logo->getImage() : '',
            'imageOrig' => $logo->getImageOrig() ? $logo->getImageOrig() : '',
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


    public function getManagementPopupString()
    {
        global $site;

        if (!isset($_POST['key'])) {
            throw new \Exception("Required parameter not set");
        }

        $key = $_POST['key'];

        $languages = $site->getLanguages();

        $values = array();
        foreach ($languages as $language) {
            $values[] = array(
                'language' => $language->getCode(),
                'languageId' => $language->getId(),
                'text' => $this->dao->getLanguageValue(Dao::PREFIX_STRING, $key, $language->getId())
            );
        }


        $html = \Ip\View::create('view/popup/string.php', array('values' => $values))->render();

        $data = array(
            "status" => "success",
            "html" => $html
        );
        $this->returnJson($data);
    }

    public function getManagementPopupText()
    {
        global $site;

        if (!isset($_POST['key'])) {
            throw new \Exception("Required parameter not set");
        }

        $key = $_POST['key'];

        $languages = $site->getLanguages();

        $values = array();
        foreach ($languages as $language) {
            $values[] = array(
                'language' => $language->getCode(),
                'languageId' => $language->getId(),
                'text' => $this->dao->getLanguageValue(Dao::PREFIX_TEXT, $key, $language->getId())
            );
        }


        $html = \Ip\View::create('view/popup/string.php', array('values' => $values))->render();

        $data = array(
            "status" => "success",
            "html" => $html
        );
        $this->returnJson($data);
    }

    public function getManagementPopupImage()
    {
        global $site;
        if (!isset($_POST['key'])) {
            throw new \Exception("Required parameter not set");
        }
        $key = $_POST['key'];


        $imageStr = $this->dao->getValue(Dao::PREFIX_IMAGE, $key, $site->getCurrentLanguage()->getId(), $site->getCurrentZone()->getName(), $site->getCurrentElement()->getId());
        $scope = $this->dao->getLastOperationScope();

        $types = array();

        $types[Scope::SCOPE_PAGE] = array('title' => 'Current page and sub-gages', 'value' => Scope::SCOPE_PAGE);
        if ($scope && $scope->getType() == Scope::SCOPE_PARENT_PAGE) {
            $pageName = '';
            $zone = $site->getZone($scope->getZoneName());
            if ($zone) {
                $element = $zone->getElement($scope->getPageId());
                if ($element) {
                    $pageName = $element->getButtonTitle();
                }
            }
            $scopeTitle = 'Page "[[page]]" and all sub-pages';
            $scopeTitle = str_replace('[[page]]', $pageName, $scopeTitle);
            $types[Scope::SCOPE_PARENT_PAGE] = array('title' => $scopeTitle, 'value' => Scope::SCOPE_PARENT_PAGE);
        }

        $scopeTitle = 'All [[language]] pages';
        $scopeTitle = str_replace('[[language]]', $site->getCurrentLanguage()->getLongDescription(), $scopeTitle);
        $types[Scope::SCOPE_LANGUAGE] = array('title' => $scopeTitle, 'value' => Scope::SCOPE_LANGUAGE);
        $types[Scope::SCOPE_GLOBAL] = array('title' => 'All pages', 'value' => Scope::SCOPE_GLOBAL);


        if ($scope && isset($types[$scope->getType()])) {
            $types[$scope->getType()]['selected'] = true;
        } else {
            $types[Scope::SCOPE_GLOBAL]['selected'] = true;
        }


        $popupData = array(
            'types' => $types
        );

        $html = \Ip\View::create('view/popup/image.php', $popupData)->render();




        $image = new Entity\Logo($imageStr);
        $imageData = array(
            'image' => $image->getImage() ? $image->getImage() : '',
            'imageOrig' => $image->getImageOrig() ? $image->getImageOrig() : '',
            'requiredWidth' => $image->getRequiredWidth(),
            'requiredHeight' => $image->getRequiredHeight(),
            'x1' => $image->getX1(),
            'y1' => $image->getY1(),
            'x2' => $image->getX2(),
            'y2' => $image->getY2()
        );

        $data = array(
            "status" => "success",
            "imageData" => $imageData,
            "html" => $html
        );
        $this->returnJson($data);
    }

    public function saveLogo()
    {
        if (!isset($_POST['text']) || !isset($_POST['color']) || !isset($_POST['font']) || !isset($_POST['type'])) {
            $this->jsonError("Missing post data");
        }

        //STORE TEXT LOGO
        $logoStr = $this->dao->getGlobalValue(Dao::PREFIX_LOGO, '');
        $logo = new Entity\Logo($logoStr);

        $logo->setText($_POST['text']);
        $logo->setColor($_POST['color']);
        $logo->setFont($_POST['font']);
        if ($_POST['type'] == Entity\Logo::TYPE_IMAGE) {
            $logo->setType(Entity\Logo::TYPE_IMAGE);
        } else {
            $logo->setType(Entity\Logo::TYPE_TEXT);
        }


        //STORE IMAGE LOGO
        if (isset($_POST['newImage']) && file_exists(BASE_DIR.$_POST['newImage']) && is_file(BASE_DIR.$_POST['newImage'])) {

            if (TMP_FILE_DIR.basename($_POST['newImage']) != $_POST['newImage']) {
                throw new \Exception("Security notice. Try to access an image (".$_POST['newImage'].") from a non temporary folder.");
            }

            //remove old image
            if ($logo->getImageOrig() && file_exists(BASE_DIR.$logo->getImageOrig()) && is_file(BASE_DIR.$logo->getImageOrig())) {
                unlink(BASE_DIR.$logo->getImageOrig());
            }

            $destDir = BASE_DIR.IMAGE_DIR;
            $newName = \Library\Php\File\Functions::genUnoccupiedName($_POST['newImage'], $destDir);
            copy(BASE_DIR.$_POST['newImage'], $destDir.$newName);
            $logo->setImageOrig(IMAGE_DIR.$newName);

        }

        if (isset($_POST['cropX1']) && isset($_POST['cropY1']) && isset($_POST['cropX2']) && isset($_POST['cropY2']) && isset($_POST['windowWidth'])&& isset($_POST['windowHeight'])) {
            //remove old file
            if ($logo->getImage() && file_exists(BASE_DIR.$logo->getImage()) && is_file(BASE_DIR.$logo->getImage())) {
                unlink(BASE_DIR.$logo->getImage());
            }


            //new small image
            $logo->setX1($_POST['cropX1']);
            $logo->setY1($_POST['cropY1']);
            $logo->setX2($_POST['cropX2']);
            $logo->setY2($_POST['cropY2']);
            $logo->setRequiredWidth($_POST['windowWidth']);
            $logo->setRequiredHeight($_POST['windowHeight']);

            $tmpSmallImageName = \Library\Php\Image\Functions::crop (
                BASE_DIR.$logo->getImageOrig(),
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
            $logo->setImage(IMAGE_DIR.$newName);
            unlink(BASE_DIR.TMP_IMAGE_DIR.$tmpSmallImageName);
        }


        $this->dao->setGlobalValue(Dao::PREFIX_LOGO, '', $logo->getValueStr());


        $inlineManagementService = new Service();


        $cssClass = null;
        if (isset($_POST['cssClass'])) {
            $cssClass = $_POST['cssClass'];
        }

        $data = array(
            "status" => "success",
            "logoHtml" => $inlineManagementService->generateManagedLogo(null, $cssClass)
        );
        $this->returnJson($data);
    }

    public function saveString()
    {
        $inlineManagementService = new Service();

        if (!isset($_POST['key']) || !isset($_POST['cssClass']) || !isset($_POST['htmlTag'])  ||  !isset($_POST['values']) || !is_array($_POST['values'])) {
            throw new \Exception("Required parameters missing");
        }
        $key = $_POST['key'];
        $tag = $_POST['htmlTag'];
        $cssClass = $_POST['cssClass'];
        $values = $_POST['values'];


        foreach($values as $languageId => $value) {
            $this->dao->setLanguageValue(Dao::PREFIX_STRING, $key, $languageId, $value);
        }

        $data = array(
            "status" => "success",
            "stringHtml" => $inlineManagementService->generateManagedString($key, $tag, null, $cssClass)
        );
        $this->returnJson($data);

    }


    public function saveText()
    {
        $inlineManagementService = new Service();

        if (!isset($_POST['key']) || !isset($_POST['cssClass']) || !isset($_POST['htmlTag'])  ||  !isset($_POST['values']) || !is_array($_POST['values'])) {
            throw new \Exception("Required parameters missing");
        }
        $key = $_POST['key'];
        $tag = $_POST['htmlTag'];
        $cssClass = $_POST['cssClass'];
        $values = $_POST['values'];


        foreach($values as $languageId => $value) {
            $this->dao->setLanguageValue(Dao::PREFIX_TEXT, $key, $languageId, $value);
        }

        $data = array(
            "status" => "success",
            "stringHtml" => $inlineManagementService->generateManagedText($key, $tag, null, $cssClass)
        );
        $this->returnJson($data);

    }


    public function saveImage()
    {
        global $site;

        if (!isset($_POST['key'])) {
            throw new \Exception("Required parameter not set");
        }
        $key = $_POST['key'];

        if (!isset($_POST['type'])) {
            throw new \Exception("Required parameter not set");
        }
        $type = $_POST['type'];


        $imageStr = $this->dao->getValue(Dao::PREFIX_IMAGE, $key, $site->getCurrentLanguage()->getId(), $site->getCurrentZone()->getName(), $site->getCurrentElement()->getId());
        $image = new Entity\Image($imageStr);
        $scope = $this->dao->getLastOperationScope();

        $sameScope = $scope && $scope->getType() == $type;

//        if ($sameScope) {
//            switch($type) {
//                case Scope::SCOPE_PAGE:
//                    //
//                    $sameScope = $site->getCurrentZone()->getName() == $scope->getZoneName() && $site->getCurrentElement()->getId();
//                    break;
//                case Scope::SCOPE_PARENT_PAGE:
//                    break;
//                case Scope::SCOPE_LANGUAGE:
//                    break;
//                case Scope::SCOPE_GLOBAL:
//                    break;
//            }
//
//        }


        //STORE IMAGE LOGO
        if (isset($_POST['newImage']) && file_exists(BASE_DIR.$_POST['newImage']) && is_file(BASE_DIR.$_POST['newImage'])) {

            if (TMP_FILE_DIR.basename($_POST['newImage']) != $_POST['newImage']) {
                throw new \Exception("Security notice. Try to access an image (".$_POST['newImage'].") from a non temporary folder.");
            }

            //remove old image
            if ($image->getImageOrig() && file_exists(BASE_DIR.$image->getImageOrig()) && is_file(BASE_DIR.$image->getImageOrig())) {
                if ($sameScope) { //otherwise we need to leave image for original scope
                    unlink(BASE_DIR.$image->getImageOrig());
                }
            }

            $destDir = BASE_DIR.IMAGE_DIR;
            $newName = \Library\Php\File\Functions::genUnoccupiedName($_POST['newImage'], $destDir);
            copy(BASE_DIR.$_POST['newImage'], $destDir.$newName);
            $image->setImageOrig(IMAGE_DIR.$newName);

        } else {
            if (!$sameScope) { //duplicate original image if we are resaving it in different scope
                if ($image->getImageOrig() && file_exists(BASE_DIR.$image->getImageOrig()) && is_file(BASE_DIR.$image->getImageOrig())) {
                    $destDir = BASE_DIR.IMAGE_DIR;
                    $newName = \Library\Php\File\Functions::genUnoccupiedName($image->getImageOrig(), $destDir);
                    copy(BASE_DIR.$image->getImageOrig(), $destDir.$newName);
                    $image->setImageOrig(IMAGE_DIR.$newName);
                }
            }
         }

        if (isset($_POST['cropX1']) && isset($_POST['cropY1']) && isset($_POST['cropX2']) && isset($_POST['cropY2']) && isset($_POST['windowWidth'])&& isset($_POST['windowHeight'])) {
            //remove old file
            if ($image->getImage() && file_exists(BASE_DIR.$image->getImage()) && is_file(BASE_DIR.$image->getImage())) {
                if ($sameScope) { //if we are saving in the same scope, replace image. Otherwise leave old one for original scope
                    unlink(BASE_DIR.$image->getImage());
                }
            }


            //new small image
            $image->setX1($_POST['cropX1']);
            $image->setY1($_POST['cropY1']);
            $image->setX2($_POST['cropX2']);
            $image->setY2($_POST['cropY2']);
            $image->setRequiredWidth($_POST['windowWidth']);
            $image->setRequiredHeight($_POST['windowHeight']);

            $tmpSmallImageName = \Library\Php\Image\Functions::crop (
                BASE_DIR.$image->getImageOrig(),
                TMP_IMAGE_DIR,
                $image->getX1(),
                $image->getY1(),
                $image->getX2(),
                $image->getY2(),
                100,
                $image->getRequiredWidth(),
                $image->getRequiredHeight()
            );

            $destDir = BASE_DIR.IMAGE_DIR;
            $newName = \Library\Php\File\Functions::genUnoccupiedName($tmpSmallImageName, $destDir);
            copy(TMP_IMAGE_DIR.$tmpSmallImageName, $destDir.$newName);
            $image->setImage(IMAGE_DIR.$newName);
            unlink(BASE_DIR.TMP_IMAGE_DIR.$tmpSmallImageName);
        } else {
            if (!$sameScope) { //duplicate cropped image
                if ($image->getImage() && file_exists(BASE_DIR.$image->getImage()) && is_file(BASE_DIR.$image->getImage())) {
                    $destDir = BASE_DIR.IMAGE_DIR;
                    $newName = \Library\Php\File\Functions::genUnoccupiedName($image->getImage(), $destDir);
                    copy(BASE_DIR.$image->getImage(), $destDir.$newName);
                    $image->setImage(IMAGE_DIR.$newName);
                }

            }
        }


        switch($type) {
            case Scope::SCOPE_PAGE:
                $this->dao->setPageValue(Dao::PREFIX_IMAGE, $key, $site->getCurrentZone()->getName(), $site->getCurrentElement()->getId(), $image->getValueStr());
                break;
            case Scope::SCOPE_PARENT_PAGE:
                $this->dao->setPageValue(Dao::PREFIX_IMAGE, $key, $scope->getZoneName(), $scope->getPageId(), $image->getValueStr());
                break;
            case Scope::SCOPE_LANGUAGE:
                $this->dao->setLanguageValue(Dao::PREFIX_IMAGE, $key, $site->getCurrentLanguage()->getId(), $image->getValueStr());
                break;
            case Scope::SCOPE_GLOBAL:
            default:
                $this->dao->setGlobalValue(Dao::PREFIX_IMAGE, $key, $image->getValueStr());
                break;
        }




        $data = array(
            "status" => "success",
            "imageSrc" => BASE_URL.$image->getImage()
        );
        $this->returnJson($data);
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
