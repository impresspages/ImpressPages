<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
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
        global $parametersMod;
        $answer = '';


        $destinationDir = BASE_DIR.IMAGE_DIR;

        $newData = $currentData;

        if (!isset($postData['logos']) || !is_array($postData['logos'])) {//check if logos array is set
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

                    //security check
                    if (TMP_FILE_DIR.basename($logo['fileName']) != $logo['fileName']) {
                        throw new \Exception("Security notice. Try to access a file (".$logo['fileName'].") from a non temporary folder.");
                    }

                    //create a copy of original file
                    $logoOriginal = \Modules\administrator\repository\Model::addFile($logo['fileName'], 'standard/content_management', $widgetId);



                    //create simplified small logo (thumbnail)
                    $tmpLogoSmall = self::_createSmallLogo(
                    $logo['fileName'],
                    $logo['cropX1'],
                    $logo['cropY1'],
                    $logo['cropX2'],
                    $logo['cropY2'],
                    TMP_IMAGE_DIR
                    );
                    $logoSmall = \Modules\administrator\repository\Model::addFile($tmpLogoSmall, 'standard/content_management', $widgetId);
                    unlink(BASE_DIR.$tmpLogoSmall);
                    

                    if (!isset($logo['title'])) {
                        $logo['title'] = '';
                    }
                    if (!isset($logo['link'])) {
                        $logo['link'] = '';
                    }
                    

                    $newLogo = array(
                        'logoOriginal' => $logoOriginal,
                        'logoSmall' => $logoSmall,
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

                    //create simplified small logo (thumbnail)
                    $tmpLogoSmall = self::_createSmallLogo(
                    $logo['fileName'],
                    $logo['cropX1'],
                    $logo['cropY1'],
                    $logo['cropX2'],
                    $logo['cropY2'],
                    TMP_IMAGE_DIR
                    );
                    $logoSmall = \Modules\administrator\repository\Model::addFile($tmpLogoSmall, 'standard/content_management', $widgetId);
                    unlink(BASE_DIR.$tmpLogoSmall);
                    

            
                    if (!isset($logo['title'])) {
                        $logo['title'] = '';
                    }
                    if (!isset($logo['link'])) {
                        $logo['link'] = '';
                    }


                    $newLogo = array(
                        'logoOriginal' => $existingLogoData['logoOriginal'],
                        'logoSmall' => $logoSmall,
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



        return $newData;
    }


    private function _createOriginalLogo ($sourceFile, $destinationDir){
        $destinationFilename = \Library\Php\File\Functions::genUnocupiedName($sourceFile, BASE_DIR.$destinationDir);
        copy($sourceFile, BASE_DIR.$destinationDir.$destinationFilename);
        $answer = $destinationDir.$destinationFilename;
        return $answer;
    }


    private function _createSmallLogo ($sourceFile, $x1, $y1, $x2, $y2, $destinationDir) {
        global $parametersMod;
        $ratio = ($x1 - $x2 / ($y1 - $y2));
        $destinationFilename = \Library\Php\Image\Functions::crop (
        $sourceFile,
        BASE_DIR.$destinationDir,
        $x1,
        $y1,
        $x2,
        $y2,
        $parametersMod->getValue('standard', 'content_management', 'widget_logo_gallery', 'quality'),
        $parametersMod->getValue('standard', 'content_management', 'widget_logo_gallery', 'width'),
        $parametersMod->getValue('standard', 'content_management', 'widget_logo_gallery', 'height')
        );
        $answer = $destinationDir.$destinationFilename;
        return $answer;

    }
    
    
    /**
    * If theme has changed, we need to crop thumbnails again.
    * @see Modules\standard\content_management.Widget::recreate()
    */
    public function recreate($widgetId, $data) {
        global $parametersMod;
        $newData = $data;
    
        
        
        if (!isset($data['logos']) || !is_array($data['logos'])) {
            return $newData;
        }
    
        foreach($newData['logos'] as $logoKey => &$logo) {
    
            if (!isset($logo['logoOriginal'])) {
                continue; //missing data. Better don't do anything
            }
            
            $imageInfo = getimagesize($logo['logoOriginal']);
            if (!$imageInfo) {
                continue; //missing data. Better don't do anything
            }
            $curWidth = $imageInfo[0];
            $curHeight = $imageInfo[1];
            $curRatio = $curWidth / $curHeight;
            
            //2.0 on update missed to store these values. But on IpLogoGallery they can be easily restored
            if (!isset($logo['cropX1']) || true) {
                $logo['cropX1'] = 0;
            }
            if (!isset($logo['cropX2'])|| true) {
                $logo['cropX2'] = $curWidth;
            }
            if (!isset($logo['cropY1'])|| true) {
                $logo['cropY1'] = 0;
            }
            if (!isset($logo['cropY2'])|| true) {
                $logo['cropY2'] = $curHeight;
            }
            
    
            //remove old big image
            if (isset($logo['logoSmall']) && $logo['logoSmall']) {
                \Modules\administrator\repository\Model::unbindFile($logo['logoSmall'], 'standard/content_management', $widgetId);
            }
    
            
            $reqWidth = $parametersMod->getValue('standard', 'content_management', 'widget_logo_gallery', 'width');
            $reqHeight = $parametersMod->getValue('standard', 'content_management', 'widget_logo_gallery', 'height');
            $reqRatio = $reqWidth / $reqHeight;
            if ($reqRatio > $curRatio) {
                $cropY1 = 0;
                $cropY2 = $curHeight;
                $cropX1 = -round(($curHeight * $reqRatio - $curWidth) / 2);
                $cropX2 = $curWidth + round(($curHeight * $reqRatio - $curWidth) / 2);
            } else {
                $cropX1 = 0;
                $cropX2 = $curWidth;
                $cropY1 = -round(($curWidth / $reqRatio - $curHeight) / 2);
                $cropY2 = $curHeight + round(($curWidth / $reqRatio - $curHeight) / 2);
            }
            
            //create simplified small logo
            $tmpLogoSmall = $this->_createSmallLogo(
            $logo['logoOriginal'],
            $cropX1,
            $cropY1,
            $cropX2,
            $cropY2,
            TMP_IMAGE_DIR
            );
            $logoSmall = \Modules\administrator\repository\Model::addFile($tmpLogoSmall, 'standard/content_management', $widgetId);
            unlink(BASE_DIR.$tmpLogoSmall);
            $logo['logoSmall'] = $logoSmall;
    
    
        };
    
    
        return $newData;    
    }

    private function _findExistingLogo ($logoOriginalFile, $allLogos) {

        if (!is_array($allLogos)) {
            return false;
        }

        $answer = false;
        foreach ($allLogos as $logoKey => $logo) {
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
        if (isset($logo['logoSmall']) && $logo['logoSmall']) {
            \Modules\administrator\repository\Model::unbindFile($logo['logoSmall'], 'standard/content_management', $widgetId);
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
            if (isset($logo['logoSmall']) && $logo['logoSmall']) {
                \Modules\administrator\repository\Model::bindFile($logo['logoSmall'], 'standard/content_management', $newId);
            }
        };
    
    }    



}