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


class IpImageGallery extends \Modules\standard\content_management\Widget{



    public function prepareData($instanceId, $postData, $currentData) {
        global $parametersMod;
        $answer = '';


        $destinationDir = BASE_DIR.IMAGE_DIR;

        $newData = $currentData;

        if (!isset($postData['images']) && !is_array($postData['images'])) {//check if images array is set
            return $newData;
        }

        $newData['images'] = array(); //we will create new images array.

        foreach($postData['images'] as $imageKey => $image){
            if (!isset($image['title']) || !isset($image['fileName']) || !isset($image['status'])){ //check if all require data present
                continue;
            }

            switch($image['status']){
                case 'new':
                    //just to be sure
                    if (!file_exists(BASE_DIR.$image['fileName'])) {
                        break;
                    }

                    //check if crop coordinates are set
                    if (!isset($image['cropX1']) || !isset($image['cropY1']) || !isset($image['cropX2']) || !isset($image['cropY2'])) {
                        break;
                    }

                    //security check
                    if (TMP_FILE_DIR.basename($image['fileName']) != $image['fileName']) {
                        throw new \Exception("Security notice. Try to access a file (".$image['fileName'].") from a non temporary folder.");
                    }

                    //create a copy of original file
                    $imageOriginal = self::_createOriginalImage($image['fileName'], IMAGE_DIR);

                     
                    //create simplified big image
                    $imageBig = self::_createBigImage($image['fileName'], IMAGE_DIR);

                    //create simplified small image (thumbnail)
                    $imageSmall = self::_createSmallImage(
                    $image['fileName'],
                    $image['cropX1'],
                    $image['cropY1'],
                    $image['cropX2'],
                    $image['cropY2'],
                    IMAGE_DIR
                    );

                    //find image title
                    if ($image['title'] == '') {
                        $title = basename($image['fileName']);
                    } else {
                        $title = $image['title'];
                    }

                    $newImage = array(
                        'imageOriginal' => $imageOriginal,
                        'imageBig' => $imageBig,
                        'imageSmall' => $imageSmall,
                        'title' => $title,
                        'cropX1' => $image['cropX1'],
                        'cropY1' => $image['cropY1'],
                        'cropX2' => $image['cropX2'],
                        'cropY2' => $image['cropY2'],

                    );
                    $newData['images'][] = $newImage;
                     
                    break;
                case 'coordinatesChanged' :
                    if (IMAGE_DIR.basename($image['fileName']) != $image['fileName']) {
                        throw new \Exception("Security notice. Try to access a file (".$image['fileName'].") from a non temporary folder.");
                    }

                    //check if crop coordinates are set
                    if (!isset($image['cropX1']) || !isset($image['cropY1']) || !isset($image['cropX2']) || !isset($image['cropY2'])) {
                        break;
                    }

                    $existingImageData = self::_findExistingImage($image['fileName'], $currentData['images']);
                    if (!$existingImageData) {
                        break; //existing image not found. Impossible to recalculate coordinates if image does not exists.
                    }

                    //create simplified small image (thumbnail)
                    $imageSmall = self::_createSmallImage(
                    $image['fileName'],
                    $image['cropX1'],
                    $image['cropY1'],
                    $image['cropX2'],
                    $image['cropY2'],
                    IMAGE_DIR
                    );

                    //find image title
                    if ($image['title'] == '') {
                        $title = basename($image['fileName']);
                    } else {
                        $title = $image['title'];
                    }


                    $newImage = array(
                        'imageOriginal' => $existingImageData['imageOriginal'],
                        'imageBig' => $existingImageData['imageBig'],
                        'imageSmall' => $imageSmall,
                        'title' => $title,
                        'cropX1' => $image['cropX1'],
                        'cropY1' => $image['cropY1'],
                        'cropX2' => $image['cropX2'],
                        'cropY2' => $image['cropY2'],
                    );
                    $newData['images'][] = $newImage;


                    break;
                case 'present': //picure not changed
                    if (!isset($currentData['images']) || !is_array($currentData['images'])) {
                        break; //possible hack. There is no images yet.
                    }

                    $existingImageData = self::_findExistingImage($image['fileName'], $currentData['images']);
                    if (!$existingImageData) {
                        break; //existing image not found. Impossible to recalculate coordinates if image does not exists.
                    }


                    //find image title
                    if ($image['title'] == '') {
                        $title = basename($image['fileName']);
                    } else {
                        $title = $image['title'];
                    }

                    $newImage = array(
                        'imageOriginal' => $existingImageData['imageOriginal'],
                        'imageBig' => $existingImageData['imageBig'],
                        'imageSmall' => $existingImageData['imageSmall'],
                        'title' => $title
                    );
                    $newData['images'][] = $newImage;

                    break;
                case 'deleted':
                    //do nothing. Files will be deleted when no links to them will be present.
                    break;
            }
        }



        return $newData;
    }


    private function _createOriginalImage ($sourceFile, $destinationDir){
        $destinationFilename = \Library\Php\File\Functions::genUnocupiedName($sourceFile, BASE_DIR.$destinationDir);
        copy($sourceFile, BASE_DIR.$destinationDir.$destinationFilename);
        $answer = $destinationDir.$destinationFilename;
        return $answer;
    }

    private function _createBigImage ($sourceFile, $destinationDir) {
        global $parametersMod;
        $destinationFilename = \Library\Php\Image\Functions::resize(
        $sourceFile,
        $parametersMod->getValue('standard', 'content_management', 'widget_photo_gallery', 'big_width'),
        $parametersMod->getValue('standard', 'content_management', 'widget_photo_gallery', 'big_height'),
        BASE_DIR.$destinationDir,
        \Library\Php\Image\Functions::CROP_TYPE_FIT,
        false,
        $parametersMod->getValue('standard', 'content_management', 'widget_photo_gallery', 'big_quality')
        );
        $answer = $destinationDir.$destinationFilename;
        return $answer;
    }

    private function _createSmallImage ($sourceFile, $x1, $y1, $x2, $y2, $destinationDir) {
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

    private function _findExistingImage ($imageOriginalFile, $allImages) {

        if (!is_array($allImages)) {
            return false;
        }

        $answer = false;
        foreach ($allImages as $imageKey => $image) {
            if ($image['imageOriginal'] == $imageOriginalFile) {
                $answer = $image;
                break;
            }
        }

        return $answer;

    }


    public function managementHtml($instanceId, $data, $layout) {
        global $parametersMod;
        $data['smallImageWidth'] = $parametersMod->getValue('standard', 'content_management', 'widget_photo_gallery', 'width');
        $data['smallImageHeight'] = $parametersMod->getValue('standard', 'content_management', 'widget_photo_gallery', 'height');
        return parent::managementHtml($instanceId, $data, $layout);
    }




}