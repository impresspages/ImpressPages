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


class ipPictureGallery extends \Modules\standard\content_management\Widget{



    public function prepareData($instanceId, $postData, $currentData) {
        global $parametersMod;
        $answer = '';


        $destinationDir = BASE_DIR.IMAGE_DIR;

        $newData = $currentData;

        if (!isset($postData['pictures']) && !is_array($postData['pictures'])) {//check if files array is set
            return $newData;
        }

        $newData['pictures'] = array(); //we will create new pictures array.

        foreach($postData['pictures'] as $filesKey => $file){
            if (!isset($file['title']) || !isset($picture['fileName']) || !isset($file['status'])){ //check if all require data present
                continue;
            }

            switch($file['status']){
                case 'new':
                    //just to be sure
                    if (!file_exists(BASE_DIR.$picture['fileName'])) {
                        break;
                    }

                    //security check
                    if (TMP_IMAGE_DIR.basename($picture['fileName']) != $picture['fileName']) {
                        throw new \Exception("Security notice. Try to access a file (".$picture['fileName'].") from a non temporary folder.");
                    }

                    //create a copy of original file
                    $pictureOriginal = self::_createOriginalPicture($picture['fileName'], IMAGE_DIR);

                     
                    //create simplified big picture
                    $bigPictureFilename = self::_createBigPicture($picture['fileName'], IMAGE_DIR);

                    //create simplified small picture (thumbnail)
                    if (!isset($picture['cropX1']))
                    $smallPictureFilename = self::_createSmallPicture(
                    $picture['fileName'],
                    $picture['cropX1'],
                    $picture['cropY1'],
                    $picture['cropX2'],
                    $picture['cropY2'],
                    IMAGE_DIR
                    );

                    //find picture title
                    if ($picture['title'] == '') {
                        $title = basename($picture['fileName']);
                    } else {
                        $title = $picture['title'];
                    }

                    $newPicture = array(
                        'pictureOriginal' => $pictureOriginal,
                        'pictureBig' => $pictureBig,
                        'pictureSmall' => $pictureSmall,
                        'title' => $title
                    );
                    $newData['pictures'][] = $newPicture;
                     
                    break;
                case 'coordinatesChanged' :
                    if (IMAGE_DIR.basename($picture['fileName']) != $picture['fileName']) {
                        throw new \Exception("Security notice. Try to access a file (".$picture['fileName'].") from a non temporary folder.");
                    }


                    break;
                case 'present':
                    if (!isset($currentData['pictures']) || !is_array($currentData['pictures'])) {
                        break; //possible hack. There is no pictures yet.
                    }
                    $reallyPresent = false;
                    foreach($currentData['pictures'] as $currentFileKey => $currentPicture) {
                        if ($currentPicture['fileName'] == $picture['fileName']) {
                            $reallyPresent = true;
                        }
                    }
                    if ($reallyPresent) {
                        $newPicture = array();
                        $newPicture['fileName'] = $currentPicture['fileName'];
                        $newPicture['title'] = $picture['title'];
                        $newData['pictures'][] = $newPicture;
                    }

                    break;
                case 'deleted':
                    //do nothing. File will be deleted when no links to it will be present.
                    break;
            }
        }



        return $newData;
    }


    private function _createOriginalPicture ($sourceFile, $destinationDir){
        $destinationFilename = \Library\Php\File\Functions::genUnocupiedName($sourceFile, BASE_DIR.$destinationDir);
        copy($picture['fileName'], BASE_DIR.$destinationDir.$destinationFilename);
        $answer = $destinationDir.$destinationFilename;
        return $answer;
    }

    private function _createBigPicture ($sourceFile, $destinationDir) {
        global $parametersMod;
        $destinationFilename = \Library\Php\Picture\Functions::resize(
        $picture['fileName'],
        $parametersMod->getValue('standard', 'content_management', 'widget_photo_gallery', 'big_width'),
        $parametersMod->getValue('standard', 'content_management', 'widget_photo_gallery', 'big_height'),
        BASE_DIR.$destinationDir,
        \Library\Php\Picture\Functions::CROP_TYPE_FIT,
        false,
        $parametersMod->getValue('standard', 'content_management', 'widget_photo_gallery', 'big_quality')
        );
        $answer = $destinationDir.$destinationFilename;
        return $answer;
    }

    private function _createSmallPicture ($sourceFile, $x1, $y1, $x2, $y2, $destinationDir) {
        global $parametersMod;
        $ratio = ($postData['cropX2'] - $postData['cropX1']) / ($postData['cropY2'] - $postData['cropY1']);
        $destinationFilename = \Library\Php\Picture\Functions::crop (
        $newData['pictureOriginal'],
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





}