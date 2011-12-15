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


class IpPictureGallery extends \Modules\standard\content_management\Widget{



    public function prepareData($instanceId, $postData, $currentData) {
        global $parametersMod;
        $answer = '';


        $destinationDir = BASE_DIR.IMAGE_DIR;

        $newData = $currentData;

        if (!isset($postData['pictures']) && !is_array($postData['pictures'])) {//check if pictures array is set
            return $newData;
        }

        $newData['pictures'] = array(); //we will create new pictures array.

        foreach($postData['pictures'] as $pictureKey => $picture){
            if (!isset($picture['title']) || !isset($picture['fileName']) || !isset($picture['status'])){ //check if all require data present
                continue;
            }

            switch($picture['status']){
                case 'new':
                    //just to be sure
                    if (!file_exists(BASE_DIR.$picture['fileName'])) {
                        break;
                    }

                    //check if crop coordinates are set
                    if (!isset($picture['cropX1']) || !isset($picture['cropY1']) || !isset($picture['cropX2']) || !isset($picture['cropY2'])) {
                        break;
                    }

                    //security check
                    if (TMP_FILE_DIR.basename($picture['fileName']) != $picture['fileName']) {
                        throw new \Exception("Security notice. Try to access a file (".$picture['fileName'].") from a non temporary folder.");
                    }

                    //create a copy of original file
                    $pictureOriginal = self::_createOriginalPicture($picture['fileName'], IMAGE_DIR);

                     
                    //create simplified big picture
                    $pictureBig = self::_createBigPicture($picture['fileName'], IMAGE_DIR);

                    //create simplified small picture (thumbnail)
                    $pictureSmall = self::_createSmallPicture(
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
                        'title' => $title,
                        'cropX1' => $picture['cropX1'],
                        'cropY1' => $picture['cropY1'],
                        'cropX2' => $picture['cropX2'],
                        'cropY2' => $picture['cropY2'],

                    );
                    $newData['pictures'][] = $newPicture;
                     
                    break;
                case 'coordinatesChanged' :
                    if (IMAGE_DIR.basename($picture['fileName']) != $picture['fileName']) {
                        throw new \Exception("Security notice. Try to access a file (".$picture['fileName'].") from a non temporary folder.");
                    }

                    //check if crop coordinates are set
                    if (!isset($picture['cropX1']) || !isset($picture['cropY1']) || !isset($picture['cropX2']) || !isset($picture['cropY2'])) {
                        break;
                    }

                    $existingPictureData = self::_findExistingPicture($picture['fileName'], $currentData['pictures']);
                    if (!$existingPictureData) {
                        break; //existing picture not found. Impossible to recalculate coordinates if picture does not exists.
                    }

                    //create simplified small picture (thumbnail)
                    $pictureSmall = self::_createSmallPicture(
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
                        'pictureOriginal' => $existingPictureData['pictureOriginal'],
                        'pictureBig' => $existingPictureData['pictureBig'],
                        'pictureSmall' => $pictureSmall,
                        'title' => $title,
                        'cropX1' => $picture['cropX1'],
                        'cropY1' => $picture['cropY1'],
                        'cropX2' => $picture['cropX2'],
                        'cropY2' => $picture['cropY2'],
                    );
                    $newData['pictures'][] = $newPicture;


                    break;
                case 'present': //picure not changed
                    if (!isset($currentData['pictures']) || !is_array($currentData['pictures'])) {
                        break; //possible hack. There is no pictures yet.
                    }

                    $existingPictureData = self::_findExistingPicture($picture['fileName'], $currentData['pictures']);
                    if (!$existingPictureData) {
                        break; //existing picture not found. Impossible to recalculate coordinates if picture does not exists.
                    }


                    //find picture title
                    if ($picture['title'] == '') {
                        $title = basename($picture['fileName']);
                    } else {
                        $title = $picture['title'];
                    }

                    $newPicture = array(
                        'pictureOriginal' => $existingPictureData['pictureOriginal'],
                        'pictureBig' => $existingPictureData['pictureBig'],
                        'pictureSmall' => $existingPictureData['pictureSmall'],
                        'title' => $title
                    );
                    $newData['pictures'][] = $newPicture;

                    break;
                case 'deleted':
                    //do nothing. Files will be deleted when no links to them will be present.
                    break;
            }
        }



        return $newData;
    }


    private function _createOriginalPicture ($sourceFile, $destinationDir){
        $destinationFilename = \Library\Php\File\Functions::genUnocupiedName($sourceFile, BASE_DIR.$destinationDir);
        copy($sourceFile, BASE_DIR.$destinationDir.$destinationFilename);
        $answer = $destinationDir.$destinationFilename;
        return $answer;
    }

    private function _createBigPicture ($sourceFile, $destinationDir) {
        global $parametersMod;
        $destinationFilename = \Library\Php\Picture\Functions::resize(
        $sourceFile,
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
        $ratio = ($x1 - $x2 / ($y1 - $y2));
        $destinationFilename = \Library\Php\Picture\Functions::crop (
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

    private function _findExistingPicture ($pictureOriginalFile, $allPictures) {

        if (!is_array($allPictures)) {
            return false;
        }

        $answer = false;
        foreach ($allPictures as $pictureKey => $picture) {
            if ($picture['pictureOriginal'] == $pictureOriginalFile) {
                $answer = $picture;
                break;
            }
        }

        return $answer;

    }


    public function managementHtml($instanceId, $data, $layout) {
        global $parametersMod;
        $data['smallPictureWidth'] = $parametersMod->getValue('standard', 'content_management', 'widget_photo_gallery', 'width');
        $data['smallPictureHeight'] = $parametersMod->getValue('standard', 'content_management', 'widget_photo_gallery', 'height');
        return parent::managementHtml($instanceId, $data, $layout);
    }




}