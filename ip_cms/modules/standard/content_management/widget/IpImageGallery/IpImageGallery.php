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
                    if (!\Library\Php\File\Functions::isFileInPublicDir($image['fileName'])) {
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
                    if (empty($currentData['images']) || $image['fileName']) {
                        break; //existing image not found. Impossible to recalculate coordinates if image does not exists.
                    }
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
    
    /**
    * If theme has changed, we need to crop thumbnails again.
    * @see Modules\standard\content_management.Widget::recreate()
    */
    public function recreate($widgetId, $data) {
        global $parametersMod;
        $newData = $data;
        
        if (!isset($data['images']) || !is_array($data['images'])) {
            return $newData;
        }
        
        foreach($newData['images'] as $imageKey => &$image) {
            
            if (!isset($image['cropX1']) || !isset($image['cropY1']) || !isset($image['cropX2']) || !isset($image['cropY2'])|| !isset($image['imageOriginal'])) {
                continue; //missing data. Better don't do anything
            }

            
            
            //remove old big image
            if (isset($image['imageBig']) && $image['imageBig']) {
                \Modules\administrator\repository\Model::unbindFile($image['imageBig'], 'standard/content_management', $widgetId);
            }
            
            //create simplified big image
            $tmpImageBig = self::_createBigImage($image['imageOriginal'], TMP_IMAGE_DIR);
            $imageBig = \Modules\administrator\repository\Model::addFile($tmpImageBig, 'standard/content_management', $widgetId);
            $image['imageBig'] = $imageBig;
            unlink(BASE_DIR.$tmpImageBig);
                        
            
            //remove curren t small image. New will be created.
            \Modules\administrator\repository\Model::unbindFile($image['imageSmall'], 'standard/content_management', $widgetId);
            
            $requiredProportions = $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'width') / $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'height');
            $this->_fixCoordinates($image, $requiredProportions);
            
            //create simplified small image (thumbnail)
            $tmpImageSmall = self::_createSmallImage(
            $image['imageOriginal'],
            $image['cropX1'],
            $image['cropY1'],
            $image['cropX2'],
            $image['cropY2'],
            TMP_IMAGE_DIR
            );
            $imageSmall = \Modules\administrator\repository\Model::addFile($tmpImageSmall, 'standard/content_management', $widgetId);
            unlink(BASE_DIR.$tmpImageSmall);
            $image['imageSmall'] = $imageSmall;
            
            
        };
        
    
        return $newData;
    }
    
    /**
     * 
     * If widget options has been changed, we need to fix cropping coordinates to new proportions.
     * 
     * It is done by putting current cropping area into circle and finding new rectangular that fits in the same
     * circle and has required proportions.
     * 
     * Then if new rectangular goes out of image edges, then it is scaled to fit.
     * 
     * @param array $image
     * @param float $requiredProportions
     */
    private function _fixCoordinates(&$image, $requiredProportions) {
        if (!isset($image['cropX1']) || !isset($image['cropY1']) || !isset($image['cropX2']) || !isset($image['cropY2'])|| !isset($image['imageOriginal'])) {
            return; //missing data. Better don't do anything
        }
        
        $x = $image['cropX2'] - $image['cropX1'];
        $y = $image['cropY2'] - $image['cropY1'];
        
        //d - diameter
        $d = sqrt($x*$x + $y*$y);
        
        //height of new rectangular
        $newY = $d / sqrt (1 + $requiredProportions * $requiredProportions);
        
        //width of new rectangular
        $newX = $newY * $requiredProportions;
                
        $xDifference = ($newX - ($image['cropX2'] - $image['cropX1'])) / 2;
        $newX1 = round($image['cropX1'] - $xDifference);
        $newX2 = round($image['cropX2'] + $xDifference);
        
        $yDifference = ($newY - ($image['cropY2'] - $image['cropY1'])) / 2;
        $newY1 = round($image['cropY1'] - $yDifference);
        $newY2 = round($image['cropY2'] + $yDifference);
        
        //resize if new rectangle goes out of image edges
        $resizeFactor = 0; //no resize
        if ($newX1 < 0) {
            $tmpResizeFactor = abs(($newX1 - 0) / ($newX2 - $newX1));
            if ($tmpResizeFactor > $resizeFactor) {
                $resizeFactor = $tmpResizeFactor;
            }
        }
        if ($newY1 < 0) {
            $tmpResizeFactor = abs(($newY1 - 0) / ($newY2 - $newY1));
            if ($tmpResizeFactor > $resizeFactor) {
                $resizeFactor = $tmpResizeFactor;
            }
        }
        
        $imageInfo = getimagesize($image['imageOriginal']);
        
        
        if ($newX2 > $imageInfo[0]) {
            $tmpResizeFactor = abs(($imageInfo[0] - $newX2) / ($newX2 - $newX1));
            if ($tmpResizeFactor > $resizeFactor) {
                $resizeFactor = $tmpResizeFactor;
            }
        }
        if ($newY2 > $imageInfo[1]) {
            $tmpResizeFactor = abs(($imageInfo[1] - $newY2) / ($newY2 - $newY1));
            if ($tmpResizeFactor > $resizeFactor) {
                $resizeFactor = $tmpResizeFactor;
            }
        }
        
        $finalX1 = $newX1 + ($newX2 - $newX1) * $resizeFactor;
        $finalX2 = $newX2 - ($newX2 - $newX1) * $resizeFactor;
        
        $finalY1 = $newY1 + ($newY2 - $newY1) * $resizeFactor;
        $finalY2 = $newY2 - ($newY2 - $newY1) * $resizeFactor;
        
        $image['cropX1'] = $finalX1;
        $image['cropX2'] = $finalX2;
        $image['cropY1'] = $finalY1;
        $image['cropY2'] = $finalY2;
        
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
        if (!isset($data['images']) || !is_array($data['images'])) {
            return;
        }
        
        foreach($data['images'] as $imageKey => $image) {
            if (!is_array($image)) {
                return;
            }
            if (isset($image['imageOriginal']) && $image['imageOriginal']) {
                \Modules\administrator\repository\Model::bindFile($image['imageOriginal'], 'standard/content_management', $newId);
            }
            if (isset($image['imageBig']) && $image['imageBig']) {
                \Modules\administrator\repository\Model::bindFile($image['imageBig'], 'standard/content_management', $newId);
            }
            if (isset($image['imageSmall']) && $image['imageSmall']) {
                \Modules\administrator\repository\Model::bindFile($image['imageSmall'], 'standard/content_management', $newId);
            }
        }

    }

}