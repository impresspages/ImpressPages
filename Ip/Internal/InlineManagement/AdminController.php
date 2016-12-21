<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\InlineManagement;

use Ip\Internal\InlineValue\Entity\Scope as Scope;


class AdminController extends \Ip\Controller
{

    var $dao;

    public function __construct()
    {

        $this->dao = new Dao();
    }


    public function getManagementPopupLogo()
    {
        $cssClass = '';
        if (isset($_POST['cssClass'])) {
            $cssClass = $_POST['cssClass'];
        }


        $config = new Config();
        $availableFonts = $config->getAvailableFonts();

        $popupData = array(
            'availableFonts' => $availableFonts
        );

        $html = ipView('view/popup/logo.php', $popupData)->render();

        $logoStr = $this->dao->getGlobalValue(Dao::PREFIX_LOGO, '');
        $logo = new Entity\Logo($logoStr);
        $logoData = array(
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


        $service = new Service();

        $data = array(
            'status' => 'success',
            'logoData' => $logoData,
            'html' => $html,
            'textPreview' => $service->generateTextLogo($cssClass),
            'imagePreview' => $service->generateImageLogo($cssClass)
        );
        return new \Ip\Response\Json($data);
    }


    public function getManagementPopupImage()
    {
        if (!isset($_POST['key'])) {
            throw new \Exception("Required parameter not set");
        }
        $key = $_POST['key'];

        if (!isset($_POST['languageId'])) {
            throw new \Exception("Required parameter not set");
        }
        $languageId = $_POST['languageId'];

        if (!isset($_POST['pageId'])) {
            throw new \Exception("Required parameter not set");
        }
        $pageId = $_POST['pageId'];

        $imageStr = $this->dao->getValue(Dao::PREFIX_IMAGE, $key, $languageId, $pageId);
        $scope = $this->dao->getLastOperationScope();
        $types = [];

        $scopePageTitle = __('Current page and sub-pages', 'Ip-admin', false);
        $scopeParentPageTitle = __('Page "[[page]]" and all sub-pages', 'Ip-admin', false);
        $scopeLanguageTitle = __('All [[language]] pages', 'Ip-admin', false);
        $scopeAllPagesTitle = __('All pages', 'Ip-admin', false);

        $types[Scope::SCOPE_PAGE] = array('title' => $scopePageTitle, 'value' => Scope::SCOPE_PAGE);
        if ($scope && $scope->getType() == Scope::SCOPE_PARENT_PAGE) {
            $pageName = '';
            $scopeParentPageTitle = str_replace('[[page]]', $pageName, $scopeParentPageTitle);
            $types[Scope::SCOPE_PARENT_PAGE] = array(
                'title' => $scopeParentPageTitle,
                'value' => Scope::SCOPE_PARENT_PAGE
            );
        }

        $scopeLanguageTitle = str_replace(
            '[[language]]',
            ipContent()->getLanguage($languageId)->getAbbreviation(),
            $scopeLanguageTitle
        );
        $types[Scope::SCOPE_LANGUAGE] = array('title' => $scopeLanguageTitle, 'value' => Scope::SCOPE_LANGUAGE);
        $types[Scope::SCOPE_GLOBAL] = array('title' => $scopeAllPagesTitle, 'value' => Scope::SCOPE_GLOBAL);


        if ($scope && isset($types[$scope->getType()])) {
            $types[$scope->getType()]['selected'] = true;
        } else {
            $types[Scope::SCOPE_GLOBAL]['selected'] = true;
        }


        $popupData = array(
            'types' => $types,
            'showRemoveLink' => $imageStr !== false
        );

        $html = ipView('view/popup/image.php', $popupData)->render();


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
        return new \Ip\Response\Json($data);
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
        $logo->setFont(isset($_POST['font']) ? $_POST['font'] : null);
        if ($_POST['type'] == Entity\Logo::TYPE_IMAGE) {
            $logo->setType(Entity\Logo::TYPE_IMAGE);
        } else {
            $logo->setType(Entity\Logo::TYPE_TEXT);
        }


        //STORE IMAGE LOGO
        if (isset($_POST['newImage']) && is_file(ipFile('file/repository/' . $_POST['newImage']))) {


            //remove old image
            if ($logo->getImageOrig()) {
                \Ip\Internal\Repository\Model::unbindFile(
                    $logo->getImageOrig(),
                    'developer/inline_management',
                    1
                ); //1 means logo
            }

            \Ip\Internal\Repository\Model::bindFile(
                $_POST['newImage'],
                'developer/inline_management',
                1
            ); //1 means logo
            $logo->setImageOrig($_POST['newImage']);

        }

        if (isset($_POST['cropX1']) && isset($_POST['cropY1']) && isset($_POST['cropX2']) && isset($_POST['cropY2']) && isset($_POST['windowWidth']) && isset($_POST['windowHeight'])) {

            //new small image
            $logo->setX1($_POST['cropX1']);
            $logo->setY1($_POST['cropY1']);
            $logo->setX2($_POST['cropX2']);
            $logo->setY2($_POST['cropY2']);
            $logo->setRequiredWidth($_POST['windowWidth']);
            $logo->setRequiredHeight($_POST['windowHeight']);
        }


        $this->dao->setGlobalValue(Dao::PREFIX_LOGO, '', $logo->getValueStr());


        $inlineManagementService = new Service();


        $cssClass = null;
        if (isset($_POST['cssClass'])) {
            $cssClass = $_POST['cssClass'];
        }

        $data = array(
            "status" => "success",
            "logoHtml" => $inlineManagementService->generateManagedLogo($cssClass)
        );
        return new \Ip\Response\Json($data);
    }


    public function saveText()
    {
        $inlineManagementService = new Service();

        if (!isset($_POST['key']) || !isset($_POST['cssClass']) || !isset($_POST['htmlTag']) || !isset($_POST['value']) || !isset($_POST['languageId'])) {
            throw new \Exception("Required parameters missing");
        }
        $key = $_POST['key'];
        $tag = $_POST['htmlTag'];
        $cssClass = $_POST['cssClass'];
        $value = $_POST['value'];
        $languageId = $_POST['languageId'];

        $this->dao->setLanguageValue(Dao::PREFIX_TEXT, $key, $languageId, $value);

        $data = array(
            "status" => "success",
            "stringHtml" => $inlineManagementService->generateManagedText($key, $tag, null, $cssClass)
        );
        return new \Ip\Response\Json($data);

    }


    public function saveImage()
    {

        if (!isset($_POST['key'])) {
            throw new \Exception("Required parameter not set");
        }
        $key = $_POST['key'];

        if (!isset($_POST['type'])) {
            throw new \Exception("Required parameter not set");
        }
        $type = $_POST['type'];

        if (!isset($_POST['cssClass'])) {
            throw new \Exception("Required parameter not set");
        }
        $cssClass = $_POST['cssClass'];

        if (!isset($_POST['defaultValue'])) {
            throw new \Exception("Required parameter not set");
        }
        $defaultValue = $_POST['defaultValue'];

        if (!isset($_POST['options'])) {
            $options = [];
        } else {
            $options = $_POST['options'];
        }

        if (!isset($_POST['languageId'])) {
            throw new \Exception("Required parameter not set");
        }
        $languageId = $_POST['languageId'];

        if (!isset($_POST['pageId'])) {
            throw new \Exception("Required parameter not set");
        }
        $pageId = $_POST['pageId'];

        $imageStr = $this->dao->getValue(Dao::PREFIX_IMAGE, $key, $languageId, $pageId);
        $image = new Entity\Image($imageStr);
        $scope = $this->dao->getLastOperationScope();

        $sameScope = $scope && $scope->getType() == $type;


        //STORE IMAGE
        if (isset($_POST['newImage']) && is_file(ipFile('file/repository/' . $_POST['newImage']))) {


            //remove old image
            if ($image->getImageOrig() && is_file(ipFile($image->getImageOrig()))) {
                if ($sameScope) { //otherwise we need to leave image for original scope
                    \Ip\Internal\Repository\Model::unbindFile(
                        $image->getImageOrig(),
                        'developer/inline_management',
                        $image->getId()
                    );
                }
            }


            \Ip\Internal\Repository\Model::bindFile(
                $_POST['newImage'],
                'developer/inline_management',
                $image->getId()
            ); //1 means logo
            $image->setImageOrig($_POST['newImage']);
        } else {
            if (!$sameScope) { //duplicate original image if we are resaving it in different scope
                if ($image->getImageOrig() && is_file(ipFile($image->getImageOrig()))) {
                    \Ip\Internal\Repository\Model::bindFile(
                        $image->getImageOrig(),
                        'developer/inline_management',
                        $image->getId()
                    );
                    $image->setImageOrig($image->getImageOrig());
                }
            }
        }

        if (isset($_POST['cropX1']) && isset($_POST['cropY1']) && isset($_POST['cropX2']) && isset($_POST['cropY2']) && isset($_POST['windowWidth']) && isset($_POST['windowHeight'])) {
            //new small image
            $image->setX1($_POST['cropX1']);
            $image->setY1($_POST['cropY1']);
            $image->setX2($_POST['cropX2']);
            $image->setY2($_POST['cropY2']);
            $image->setRequiredWidth($_POST['windowWidth']);
            $image->setRequiredHeight($_POST['windowHeight']);
        } else {
            if (!$sameScope) {
                //in this place cropped image should be duplicated. But after implementation of reflection service it is not used
            }
        }


        if (!$sameScope) {
            //we are trying to save into different scope. We need to delete any images that could exist there
            switch ($type) {
                case Scope::SCOPE_PAGE:
                    //this always should return false. But just in case JS part would change, we implement it.
                    $oldImageStr = $this->dao->getPageValue(Dao::PREFIX_IMAGE, $key, $languageId, $pageId);
                    break;
                case Scope::SCOPE_PARENT_PAGE:
                    trigger_error(
                        "developer/inline_management",
                        "Unexpected situation"
                    ); //there is no option to save to parent if $sameScope is true.
                    break;
                case Scope::SCOPE_LANGUAGE:
                    $oldImageStr = $this->dao->getLanguageValue(Dao::PREFIX_IMAGE, $key, $languageId);
                    break;
                case Scope::SCOPE_GLOBAL:
                    $oldImageStr = $this->dao->getGlobalValue(Dao::PREFIX_IMAGE, $key);
                    break;
            }

            if ($oldImageStr) {
                $oldScope = $this->dao->getLastOperationScope();
                if ($oldScope->getType() == $type
                ) { //if really have old image in this scope. If $oldScope != $type, we got global image - not from the scope we are saving in
                    $oldImage = new Entity\Image($oldImageStr);
                    $this->removeImageRecord($oldImage, $key, $oldScope);
                }
            }
        }


        switch ($type) {
            case Scope::SCOPE_PAGE:
                $this->dao->setPageValue(Dao::PREFIX_IMAGE, $key, $languageId, $pageId, $image->getValueStr());
                break;
            case Scope::SCOPE_PARENT_PAGE:
                $this->dao->setPageValue(
                    Dao::PREFIX_IMAGE,
                    $key,
                    $scope->getLanguageId(),
                    $scope->getPageId(),
                    $image->getValueStr()
                );
                break;
            case Scope::SCOPE_LANGUAGE:
                $this->dao->setLanguageValue(Dao::PREFIX_IMAGE, $key, $languageId, $image->getValueStr());
                break;
            case Scope::SCOPE_GLOBAL:
            default:
                $this->dao->setGlobalValue(Dao::PREFIX_IMAGE, $key, $image->getValueStr());
                break;
        }


        $inlineManagementService = new Service();
        $options['languageId'] = $languageId;
        $options['pageId'] = $pageId;
        $newHtml = $inlineManagementService->generateManagedImage($key, $defaultValue, $options, $cssClass);

        $data = array(
            "status" => "success",
            "newHtml" => $newHtml
        );
        return new \Ip\Response\Json($data);
    }


    public function removeImage()
    {
        if (!isset($_POST['key'])) {
            throw new \Exception("Required parameter not set");
        }
        $key = $_POST['key'];

        if (!isset($_POST['cssClass'])) {
            throw new \Exception("Required parameter not set");
        }
        $cssClass = $_POST['cssClass'];

        if (!isset($_POST['defaultValue'])) {
            throw new \Exception("Required parameter not set");
        }
        $defaultValue = $_POST['defaultValue'];

        if (!isset($_POST['options'])) {
            $options = [];
        } else {
            $options = $_POST['options'];
        }


        if (!isset($_POST['languageId'])) {
            throw new \Exception("Required parameter not set");
        }
        $languageId = $_POST['languageId'];

        if (!isset($_POST['pageId'])) {
            throw new \Exception("Required parameter not set");
        }
        $pageId = $_POST['pageId'];

        $imageStr = $this->dao->getValue(Dao::PREFIX_IMAGE, $key, $languageId, $pageId);
        if ($imageStr) {
            $image = new Entity\Image($imageStr);
            $scope = $this->dao->getLastOperationScope();
            $this->removeImageRecord($image, $key, $scope);
        }


        $inlineManagementService = new Service();
        $options['languageId'] = $languageId;
        $options['pageId'] = $pageId;

        $newHtml = $inlineManagementService->generateManagedImage($key, $defaultValue, $options, $cssClass);


        $data = array(
            "status" => "success",
            "newHtml" => $newHtml
        );
        return new \Ip\Response\Json($data);

    }

    /**
     * @param Entity\Image $image
     * @param string $key
     * @param \Ip\Internal\InlineValue\Entity\Scope $scope
     */
    private function removeImageRecord($image, $key, $scope)
    {
        if ($scope) {
            switch ($scope->getType()) {
                case Scope::SCOPE_PAGE:
                case Scope::SCOPE_PARENT_PAGE:
                    $this->dao->deletePageValue(Dao::PREFIX_IMAGE, $key, $scope->getPageId());
                    break;
                case Scope::SCOPE_LANGUAGE:
                    $this->dao->deleteLanguageValue(Dao::PREFIX_IMAGE, $key, $scope->getLanguageId());
                    break;
                case Scope::SCOPE_GLOBAL:
                    $this->dao->deleteGlobalValue(Dao::PREFIX_IMAGE, $key);
                    break;
            }
            if ($image) {
                if ($image->getImageOrig() && is_file(ipFile($image->getImageOrig()))) {
                    \Ip\Internal\Repository\Model::unbindFile(
                        $image->getImageOrig(),
                        'developer/inline_management',
                        $image->getId()
                    );
                }
            }
        }
    }


    private function jsonError($errorMessage)
    {
        $data = array(
            "status" => "error",
            "error" => $errorMessage
        );
        return new \Ip\Response\Json($data);
    }


}
