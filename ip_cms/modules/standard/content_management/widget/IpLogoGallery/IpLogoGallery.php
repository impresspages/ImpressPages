<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\content_management\widget;

if (!defined('CMS')) exit;

require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget.php');
require_once(BASE_DIR.LIBRARY_DIR.'php/file/functions.php');
require_once(BASE_DIR.LIBRARY_DIR.'php/image/functions.php');


class IpLogoGallery extends \Modules\standard\content_management\Widget{


    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('standard', 'content_management', 'widget_logo_gallery', 'logo_gallery');
    }
    

    public function update($widgetId, $postData, $currentData) {

        $newData = $currentData;

        //check if logos array is set
        if (!isset($postData['logos']) || !is_array($postData['logos'])) {
            return $newData;
        }

        $newData['logos'] = array(); //we will create new logos array.

        foreach($postData['logos'] as $logoKey => $logo){
            if (!isset($logo['title']) || !isset($logo['fileName']) || !isset($logo['status'])){ //check if all require data present
                continue;
            }

            switch($logo['status']){
                case 'new':
                    //just to be sure
                    if (!file_exists(BASE_DIR.$logo['fileName'])) {
                        break;
                    }

                    //check if crop coordinates are set
                    if (!isset($logo['cropX1']) || !isset($logo['cropY1']) || !isset($logo['cropX2']) || !isset($logo['cropY2'])) {
                        break;
                    }


                    //bind to original file
                    \Modules\administrator\repository\Model::bindFile($logo['fileName'], 'standard/content_management', $widgetId);
                    $logoOriginal = $logo['fileName'];

                    

                    if (!isset($logo['title'])) {
                        $logo['title'] = '';
                    }
                    if (!isset($logo['link'])) {
                        $logo['link'] = '';
                    }
                    

                    $newLogo = array(
                        'logoOriginal' => $logoOriginal,
                        'title' => $logo['title'],
                        'link' => $logo['link'],
                        'cropX1' => $logo['cropX1'],
                        'cropY1' => $logo['cropY1'],
                        'cropX2' => $logo['cropX2'],
                        'cropY2' => $logo['cropY2'],

                    );
                    $newData['logos'][] = $newLogo;
                     
                    break;
                case 'coordinatesChanged' :


                    //check if crop coordinates are set
                    if (!isset($logo['cropX1']) || !isset($logo['cropY1']) || !isset($logo['cropX2']) || !isset($logo['cropY2'])) {
                        break;
                    }

                    $existingLogoData = self::_findExistingLogo($logo['fileName'], $currentData['logos']);
                    if (!$existingLogoData) {
                        break; //existing logo not found. Impossible to recalculate coordinates if logo does not exists.
                    }


                    if (!isset($logo['title'])) {
                        $logo['title'] = '';
                    }
                    if (!isset($logo['link'])) {
                        $logo['link'] = '';
                    }


                    $newLogo = array(
                        'logoOriginal' => $existingLogoData['logoOriginal'],
                        'title' => $logo['title'],
                        'link' => $logo['link'],
                        'cropX1' => $logo['cropX1'],
                        'cropY1' => $logo['cropY1'],
                        'cropX2' => $logo['cropX2'],
                        'cropY2' => $logo['cropY2'],
                    );
                    $newData['logos'][] = $newLogo;


                    break;
                case 'present': //picure not changed. Store new title / link
                    $existingLogoData = self::_findExistingLogo($logo['fileName'], $currentData['logos']);
                    if (!$existingLogoData) {
                        break; //existing logo not found. Impossible to recalculate coordinates if logo does not exists.
                    }

                    if (!isset($logo['title'])) {
                        $logo['title'] = '';
                    }
                    if (!isset($logo['link'])) {
                        $logo['link'] = '';
                    }

                    $newLogo = $existingLogoData;
                    $newLogo['title'] = $logo['title'];
                    $newLogo['link'] = $logo['link'];
                    $newData['logos'][] = $newLogo;

                    break;
                case 'deleted':
                    $existingLogoData = self::_findExistingLogo($logo['fileName'], $currentData['logos']);
                    if (!$existingLogoData) {
                        break; //existing logo not found. Impossible to recalculate coordinates if image does not exists.
                    }
                    self::_deleteOneLogo($existingLogoData, $widgetId);
                    break;
            }
        }

        //delete logos that does not exist in posted array
        //Usually it should not happen ever. But just in case we are checking it and deleting unused images.
        if (isset($currentData['logos']) && is_array($currentData['logos'])) {
            //loop all current logos
            foreach ($currentData['logos'] as $curLogo) {
                if (!$this->_findExistingLogo($curLogo, $widgetId)) {
                    $this->_deleteOneLogo($curLogo, $widgetId);
                }
            }
        }


        return $newData;
    }


    private function _findExistingLogo ($logoOriginalFile, $allLogos) {

        if (!is_array($allLogos)) {
            return false;
        }

        $answer = false;
        foreach ($allLogos as $logo) {
            if (isset($logo['logoOriginal']) && $logo['logoOriginal'] == $logoOriginalFile) {
                $answer = $logo;
                break;
            }
        }

        return $answer;

    }


    public function managementHtml($instanceId, $data, $layout) {
        global $parametersMod;
        $data['logoWidth'] = $parametersMod->getValue('standard', 'content_management', 'widget_logo_gallery', 'width');
        $data['logoHeight'] = $parametersMod->getValue('standard', 'content_management', 'widget_logo_gallery', 'height');
        return parent::managementHtml($instanceId, $data, $layout);
    }

    public function previewHtml($instanceId, $data, $layout) {
        global $parametersMod;
        $reflectionService = \Modules\administrator\repository\ReflectionService::instance();


        if (!isset($data['logos']) || !is_array($data['logos'])){
            $data['logos'] = array();
        }
        foreach ($data['logos'] as $logoKey => &$logo) {
            if ($logo['link'] && stripos($logo['link'], 'http') !== 0 && stripos($logo['link'], '#') !== 0) {
                $logo['link'] = 'http://'.$logo['link'];
            }
        }

        if (isset($data['logos']) && is_array($data['logos'])) {
            //loop all current images
            foreach ($data['logos'] as &$curLogo) {

                if (isset($curLogo['cropX1']) && isset($curLogo['cropY1']) && isset($curLogo['cropX2']) && isset($curLogo['cropY2']) ) {
                    $transformSmall = new \Modules\administrator\repository\Transform\ImageFit(
                        $parametersMod->getValue('standard', 'content_management', 'widget_logo_gallery', 'width'),
                        $parametersMod->getValue('standard', 'content_management', 'widget_logo_gallery', 'height'),
                        $parametersMod->getValue('standard', 'content_management', 'widget_logo_gallery', 'quality'),
                        true
                    );
                    try {
                        $curLogo['logoSmall'] = $reflectionService->getReflection($curLogo['logoOriginal'], $curLogo['title'], $transformSmall);
                    } catch (\Modules\administrator\repository\Exception $e) {
                        //do nothing
                    }


                }
            }
        }



        return parent::previewHtml($instanceId, $data, $layout);
    }
    
    public function delete($widgetId, $data) {
        if (!isset($data['logos']) || !is_array($data['logos'])) {
            return;
        }
        
        foreach($data['logos'] as $logoKey => $logo) {
            self::_deleteOneLogo($logo, $widgetId);
        };
    }        

    private function _deleteOneLogo($logo, $widgetId) {
        if (!is_array($logo)) {
            return;
        }
        if (isset($logo['logoOriginal']) && $logo['logoOriginal']) {
            \Modules\administrator\repository\Model::unbindFile($logo['logoOriginal'], 'standard/content_management', $widgetId);
        }
    }
    
    /**
    *
    * Duplicate widget action. This function is executed after the widget is being duplicated.
    * All widget data is duplicated automatically. This method is used only in case a widget
    * needs to do some maintenance tasks on duplication.
    * @param int $oldId old widget id
    * @param int $newId duplicated widget id
    * @param array $data data that has been duplicated from old widget to the new one
    */
    public function duplicate($oldId, $newId, $data) {
        if (!isset($data['logos']) || !is_array($data['logos'])) {
            return;
        }
        
        foreach($data['logos'] as $logoKey => $logo) {
            if (!is_array($logo)) {
                return;
            }
            if (isset($logo['logoOriginal']) && $logo['logoOriginal']) {
                \Modules\administrator\repository\Model::bindFile($logo['logoOriginal'], 'standard/content_management', $newId);
            }
        };
    
    }    



}