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


class IpImageGallery extends \Modules\standard\content_management\Widget{

    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'gallery');
    }

    

    public function update($widgetId, $postData, $currentData) {
        $newData = $currentData;

        //check if images array is set
        if (!isset($postData['images']) || !is_array($postData['images'])) {
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

                    //bind new image to the widget
                    \Modules\administrator\repository\Model::bindFile($image['fileName'], 'standard/content_management', $widgetId);



                    //find image title
                    if ($image['title'] == '') {
                        $title = basename($image['fileName']);
                    } else {
                        $title = $image['title'];
                    }

                    $newImage = array(
                        'imageOriginal' => $image['fileName'],
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

                    //find image title
                    if ($image['title'] == '') {
                        $title = basename($image['fileName']);
                    } else {
                        $title = $image['title'];
                    }

                    $newImage = array(
                        'imageOriginal' => $existingImageData['imageOriginal'],
                        'title' => $title,
                        'cropX1' => $image['cropX1'],
                        'cropY1' => $image['cropY1'],
                        'cropX2' => $image['cropX2'],
                        'cropY2' => $image['cropY2'],
                    );
                    $newData['images'][] = $newImage;


                    break;


                case 'present': //picture not changed
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

                    $newImage = array_intersect_key($existingImageData, array('imageOriginal' => 1, 'title' => 1, 'cropX1' => 1, 'cropY1' => 1, 'cropX2' => 1, 'cropY2' => 1));
                    $newImage['title'] = $title;
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


        //delete images that does not exist in posted array
        //Usually it should not happen ever. But just in case we are checking it and deleting unused images.
        if (isset($currentData['images']) && is_array($currentData['images'])) {
            //loop all current images
            foreach ($currentData['images'] as $curImage) {
                if (!$this->_findExistingImage($curImage, $widgetId)) {
                    $this->_deleteOneImage($curImage, $widgetId);
                }
            }
        }


        return $newData;
    }






    private function _findExistingImage ($imageOriginalFile, $allImages) {

        if (!is_array($allImages)) {
            return false;
        }

        $answer = false;
        foreach ($allImages as $imageKey => $image) {
            if (isset($image['imageOriginal']) && $image['imageOriginal'] == $imageOriginalFile) {
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

    public function previewHtml($instanceId, $data, $layout)
    {
        global $parametersMod;
        $reflectionService = \Modules\administrator\repository\ReflectionService::instance();

        if (isset($data['images']) && is_array($data['images'])) {
            //loop all current images
            foreach ($data['images'] as $curImageKey => &$curImage) {
                if (empty($curImage['imageOriginal'])) {
                    continue;
                }
                $desiredName = isset($curImage['title']) ? $curImage['title'] : '';

                //create big image reflection
                $bigWidth = $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'big_width');
                $bigHeight = $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'big_height');
                $transformBig = new \Modules\administrator\repository\Transform\ImageFit($bigWidth, $bigHeight);

                try {
                    $curImage['imageBig'] = $reflectionService->getReflection($curImage['imageOriginal'], $desiredName, $transformBig);
                } catch (\Modules\administrator\repository\Exception $e) {
                    //do nothing
                }



                if (isset($curImage['cropX1']) && isset($curImage['cropY1']) && isset($curImage['cropX2']) && isset($curImage['cropY2']) ) {
                    $transformSmall = new \Modules\administrator\repository\Transform\ImageCrop(
                        $curImage['cropX1'],
                        $curImage['cropY1'],
                        $curImage['cropX2'],
                        $curImage['cropY2'],
                        $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'width'),
                        $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'height'),
                        $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'quality')
                    );

                } else {
                    $transformSmall = new \Modules\administrator\repository\Transform\ImageCropCenter(
                        $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'width'),
                        $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'height'),
                        $parametersMod->getValue('standard', 'content_management', 'widget_image_gallery', 'quality')
                    );

                }
                try {
                    $curImage['imageSmall'] = $reflectionService->getReflection($curImage['imageOriginal'], $curImage['title'], $transformSmall);
                } catch (\Modules\administrator\repository\Exception $e) {
                    //do nothing
                }

            }
        }
        return parent::previewHtml($instanceId, $data, $layout);
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
        }

    }

}