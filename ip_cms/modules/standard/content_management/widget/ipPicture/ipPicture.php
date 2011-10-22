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
require_once(BASE_DIR.LIBRARY_DIR.'php/picture/functions.php');

class ipPicture extends \Modules\standard\content_management\Widget{



    public function prepareData($instanceId, $postData, $currentData) {
        global $parametersMod;
        $answer = '';

        $destinationDir = BASE_DIR.IMAGE_DIR;
        
        $newData = $currentData;

        if (isset($postData['newPicture']) && file_exists(BASE_DIR.$postData['newPicture']) && is_file(BASE_DIR.$postData['newPicture'])) {
            //$this->removeOldPictures($currentData);

            //new original picture
            $unocupiedName = \Library\Php\File\Functions::genUnocupiedName($postData['newPicture'], $destinationDir);
            copy($postData['newPicture'], $destinationDir.$unocupiedName);
            $newData['pictureOriginal'] = IMAGE_DIR.$unocupiedName;

            //new small picture
            $smallPictureName = \Library\Php\Picture\Functions::crop (
            $postData['newPicture'],
            $destinationDir,
            $postData['cropX1'],
            $postData['cropY1'],
            $postData['cropX2'],
            $postData['cropY2'],
            $parametersMod->getValue('standard', 'content_management', 'widget_photo', 'quality'));

            $newData['pictureSmall'] = IMAGE_DIR.$smallPictureName;
            
//            copy($postData['newPicture'], BASE_DIR.IMAGE_DIR.$unocupiedName);
//            $newData['pictureOriginal'] = IMAGE_DIR.$unocupiedName;

//            $parametersMod->getValue('standard', 'content_management', 'widget_photo', 'photo_height');
        }

        if (isset($postData['title'])) {
            $newData['title'] = $postData['title'];
        }

        return $newData;
    }

    //you don't need to remove old files
    //private function removeOldPictures($data) {
    //        if (isset($currentData['pictureOriginal'])) {
    //            $this->unlinkPicture(BASE_DIR.$currentData['pictureOriginal']);
    //        }
    //        if (isset($currentData['pictureBig'])) {
    //            $this->unlinkPicture(BASE_DIR.$currentData['pictureOriginal']);
    //        }
    //        if (isset($currentData['pictureSmall'])) {
    //            $this->unlinkPicture(BASE_DIR.$currentData['pictureOriginal']);
    //        }
    //    }

    //    private function unlinkPicture($picture) {
    //        if (file_exists($picture) && is_file($picture)) {
    //            $success = unlink($picture);
    //            return $success;
    //        }
    //        return false;
    //    }




}