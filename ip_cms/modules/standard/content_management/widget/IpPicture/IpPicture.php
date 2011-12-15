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

class IpPicture extends \Modules\standard\content_management\Widget{



    public function prepareData($instanceId, $postData, $currentData) {
        global $parametersMod;
        $answer = '';


        $destinationDir = BASE_DIR.IMAGE_DIR;

        $newData = $currentData;
        $newData['pictureWindowWidth'] = $postData['pictureWindowWidth'];

        if (isset($postData['newPicture']) && file_exists(BASE_DIR.$postData['newPicture']) && is_file(BASE_DIR.$postData['newPicture'])) {

            if (TMP_FILE_DIR.basename($postData['newPicture']) != $postData['newPicture']) {
                throw new \Exception("Security notice. Try to access an image (".$postData['newPicture'].") from a non temporary folder.");
            }

            //new original picture
            $unocupiedName = \Library\Php\File\Functions::genUnocupiedName($postData['newPicture'], $destinationDir);
            copy($postData['newPicture'], $destinationDir.$unocupiedName);
            $newData['pictureOriginal'] = IMAGE_DIR.$unocupiedName;



            $bigPictureName = \Library\Php\Picture\Functions::resize(
            $postData['newPicture'],
            $parametersMod->getValue('standard', 'content_management', 'widget_photo', 'big_width'),
            $parametersMod->getValue('standard', 'content_management', 'widget_photo', 'big_height'),
            BASE_DIR.IMAGE_DIR,
            \Library\Php\Picture\Functions::CROP_TYPE_FIT,
            false,
            $parametersMod->getValue('standard', 'content_management', 'widget_photo', 'big_quality')
            );
            $newData['pictureBig'] = IMAGE_DIR.$bigPictureName;
        }

        if (isset($postData['cropX1']) && isset($postData['cropY1']) && isset($postData['cropX2']) && isset($postData['cropY2']) && isset($postData['scale']) ) {
            //new small picture
            $ratio = ($postData['cropX2'] - $postData['cropX1']) / ($postData['cropY2'] - $postData['cropY1']);
            $requiredWidth = round($parametersMod->getValue('standard', 'content_management', 'widget_photo', 'width') * $postData['scale']);
            $requiredHeight = round($requiredWidth / $ratio);
            $smallPictureName = \Library\Php\Picture\Functions::crop (
            $newData['pictureOriginal'],
            $destinationDir,
            $postData['cropX1'],
            $postData['cropY1'],
            $postData['cropX2'],
            $postData['cropY2'],
            $parametersMod->getValue('standard', 'content_management', 'widget_photo', 'quality'),
            $requiredWidth,
            $requiredHeight
            );
            $newData['pictureSmall'] = IMAGE_DIR.$smallPictureName;
            $newData['scale'] = $postData['scale'];
            $newData['cropX1'] = $postData['cropX1'];
            $newData['cropY1'] = $postData['cropY1'];
            $newData['cropX2'] = $postData['cropX2'];
            $newData['cropY2'] = $postData['cropY2'];

        }



        //            copy($postData['newPicture'], BASE_DIR.IMAGE_DIR.$unocupiedName);
        //            $newData['pictureOriginal'] = IMAGE_DIR.$unocupiedName;

        //            $parametersMod->getValue('standard', 'content_management', 'widget_photo', 'photo_height');


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