<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management\widget;

if (!defined('CMS')) exit;

require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget.php');
require_once(BASE_DIR.LIBRARY_DIR.'php/file/functions.php');
require_once(BASE_DIR.LIBRARY_DIR.'php/image/functions.php');


class IpLogoGallery extends \Modules\standard\content_management\Widget{



    public function update($widgetId, $postData, $currentData) {
        global $parametersMod;
        $answer = '';


        $destinationDir = BASE_DIR.IMAGE_DIR;

        $newData = $currentData;

        if (!isset($postData['logos']) && !is_array($postData['logos'])) {//check if logos array is set
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
                    

                    //find logo title
                    if ($logo['title'] == '') {
                        $title = basename($logo['fileName']);
                    } else {
                        $title = $logo['title'];
                    }

                    $newLogo = array(
                        'logoOriginal' => $logoOriginal,
                        'logoSmall' => $logoSmall,
                        'title' => $title,
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
                    

                    //find logo title
                    if ($logo['title'] == '') {
                        $title = basename($logo['fileName']);
                    } else {
                        $title = $logo['title'];
                    }


                    $newLogo = array(
                        'logoOriginal' => $existingLogoData['logoOriginal'],
                        'logoSmall' => $logoSmall,
                        'title' => $title,
                        'cropX1' => $logo['cropX1'],
                        'cropY1' => $logo['cropY1'],
                        'cropX2' => $logo['cropX2'],
                        'cropY2' => $logo['cropY2'],
                    );
                    $newData['logos'][] = $newLogo;


                    break;
                case 'present': //picure not changed. Store new title
                    $existingLogoData = self::_findExistingLogo($logo['fileName'], $currentData['logos']);
                    if (!$existingLogoData) {
                        break; //existing logo not found. Impossible to recalculate coordinates if logo does not exists.
                    }


                    //find logo title
                    if ($logo['title'] == '') {
                        $title = basename($logo['fileName']);
                    } else {
                        $title = $logo['title'];
                    }

                    $newLogo = array(
                        'logoOriginal' => $existingLogoData['logoOriginal'],
                        'logoSmall' => $existingLogoData['logoSmall'],
                        'title' => $title
                    );
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
        $parametersMod->getValue('standard', 'content_management', 'widget_photo_gallery', 'quality'),
        $parametersMod->getValue('standard', 'content_management', 'widget_photo_gallery', 'width'),
        $parametersMod->getValue('standard', 'content_management', 'widget_photo_gallery', 'height')
        );
        $answer = $destinationDir.$destinationFilename;
        return $answer;

    }

    private function _findExistingLogo ($logoOriginalFile, $allLogos) {

        if (!is_array($allLogos)) {
            return false;
        }

        $answer = false;
        foreach ($allLogos as $logoKey => $logo) {
            if ($logo['logoOriginal'] == $logoOriginalFile) {
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



}