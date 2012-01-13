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

    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'gallery');
    }

    

    public function update($widgetId, $postData, $currentData) {
        global $parametersMod;
        $answer = '';


        $destinationDir = BASE_DIR.IMAGE_DIR;

        $newData = $currentData;

        //check if images array is set
        if (!isset($postData['images']) && !is_array($postData['images'])) {
            return $newData;
        }
        
        //delete images that does not exist in posted array
        //Usually it should not happen ever. But just in case we are checking it and eleting unused images.
        if (isset($currentData['images']) && is_array($currentData['images'])) {
            //loop all current images 
            foreach ($currentData['images'] as $curImageKey => &$curImage) {
                //loop posted images
                $found = false;
                foreach ($postData['images'] as $postImageKey => &$postImage) {
                    $found = true;
                }
                if (!$found) {
                    //old image does not exist in new posted array. Lets delete it.
                    \Modules\administrator\repository\Model::unbindFile($curImage['fileName'], 'standard/content_management', $widgetId);
                }
            }
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

                    //create a copy of original(uploaded) file
                    $imageOriginal = \Modules\administrator\repository\Model::addFile($image['fileName'], 'standard/content_management', $widgetId);

                    
                    //create simplified big image
                    $tmpImageBig = self::_createBigImage($image['fileName'], TMP_IMAGE_DIR);
                    $imageBig = \Modules\administrator\repository\Model::addFile($tmpImageBig, 'standard/content_management', $widgetId);
                    unlink(BASE_DIR.$tmpImageBig);
                    

                    //create simplified small image (thumbnail)
                    $tmpImageSmall = self::_createSmallImage(
                    $image['fileName'],
                    $image['cropX1'],
                    $image['cropY1'],
                    $image['cropX2'],
                    $image['cropY2'],
                    TMP_IMAGE_DIR
                    );
                    $imageSmall = \Modules\administrator\repository\Model::addFile($tmpImageSmall, 'standard/content_management', $widgetId);
                    unlink(BASE_DIR.$tmpImageSmall);

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
                    //check if crop coordinates are set
                    if (!isset($image['cropX1']) || !isset($image['cropY1']) || !isset($image['cropX2']) || !isset($image['cropY2'])) {
                        break;
                    }

                    $existingImageData = self::_findExistingImage($image['fileName'], $currentData['images']);
                    if (!$existingImageData) {
                        break; //existing image not found. Impossible to recalculate coordinates if image does not exists.
                    }
                    //remove current existing image. New will be created.
                    \Modules\administrator\repository\Model::unbindFile($existingImageData['imageSmall'], 'standard/content_management', $widgetId);

                    //create simplified small image (thumbnail)
                    $tmpImageSmall = self::_createSmallImage(
                    $image['fileName'],
                    $image['cropX1'],
                    $image['cropY1'],
                    $image['cropX2'],
                    $image['cropY2'],
                    TMP_IMAGE_DIR
                    );
                    $imageSmall = \Modules\administrator\repository\Model::addFile($tmpImageSmall, 'standard/content_management', $widgetId);
                    unlink(BASE_DIR.$tmpImageSmall);
                    

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
                    $existingImageData = self::_findExistingImage($image['fileName'], $currentData['images']);
                    if (!$existingImageData) {
                        break; //existing image not found. Impossible to recalculate coordinates if image does not exists.
                    }
                    self::_deleteOneImage($existingImageData, $widgetId);
                    break;
            }
        }



        return $newData;
    }




    private function _createBigImage ($sourceFile, $destinationDir) {
        global $parametersMod;
        $destinationFilename = \Library\Php\Image\Functions::resize(
        $sourceFile,
        $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'big_width'),
        $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'big_height'),
        BASE_DIR.$destinationDir,
        \Library\Php\Image\Functions::CROP_TYPE_FIT,
        false,
        $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'big_quality')
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
        $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'quality'),
        $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'width'),
        $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'height')
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
        $data['smallImageWidth'] = $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'width');
        $data['smallImageHeight'] = $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'height');
        return parent::managementHtml($instanceId, $data, $layout);
    }

    
    public function delete($widgetId, $data) {
        if (!isset($data['images']) || !is_array($data['images'])) {
            return;
        }
        
        foreach($data['images'] as $imageKey => $image) {
            self::_deleteOneImage($image, $widgetId);
        };
    }    

    private function _deleteOneImage($image, $widgetId) {
        if (!is_array($image)) {
            return;
        }
        if (isset($image['imageOriginal']) && $image['imageOriginal']) {
            \Modules\administrator\repository\Model::unbindFile($image['imageOriginal'], 'standard/content_management', $widgetId);
        }
        if (isset($image['imageBig']) && $image['imageBig']) {
            \Modules\administrator\repository\Model::unbindFile($image['imageBig'], 'standard/content_management', $widgetId);
        }
        if (isset($image['imageSmall']) && $image['imageSmall']) {
            \Modules\administrator\repository\Model::unbindFile($image['imageSmall'], 'standard/content_management', $widgetId);
        }        
    }

}