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


class IpImage extends \Modules\standard\content_management\Widget{


    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('standard', 'content_management', 'widget_image', 'image');
    }

    
    public function update($widgetId, $postData, $currentData) {
        global $parametersMod;
        $answer = '';



        $newData = $currentData;
        $newData['imageWindowWidth'] = $postData['imageWindowWidth'];

        if (isset($postData['newImage']) && file_exists(BASE_DIR.$postData['newImage']) && is_file(BASE_DIR.$postData['newImage'])) {

            if (TMP_FILE_DIR.basename($postData['newImage']) != $postData['newImage']) {
                throw new \Exception("Security notice. Try to access an image (".$postData['newImage'].") from a non temporary folder.");
            }

            //remove old image
            if (isset($currentData['imageOriginal']) && $currentData['imageOriginal']) {
                \Modules\administrator\repository\Model::unbindFile($currentData['imageOriginal'], 'standard/content_management', $widgetId);
            }
            
            //new original image
            $newData['imageOriginal'] = \Modules\administrator\repository\Model::addFile($postData['newImage'], 'standard/content_management', $widgetId);
            
            //remove old big image
            if (isset($currentData['imageBig']) && $currentData['imageBig']) {
                \Modules\administrator\repository\Model::unbindFile($currentData['imageBig'], 'standard/content_management', $widgetId);
            }
            
            
            //new big image
            $tmpBigImage = $this->cropBigImage($postData['newImage']);
            $newData['imageBig'] = \Modules\administrator\repository\Model::addFile(TMP_IMAGE_DIR.$tmpBigImageName, 'standard/content_management', $widgetId);
            //delete temporary file
            unlink(BASE_DIR.TMP_IMAGE_DIR.$tmpBigImageName);
        }

        if (isset($postData['cropX1']) && isset($postData['cropY1']) && isset($postData['cropX2']) && isset($postData['cropY2']) && isset($postData['scale']) && isset($postData['maxWidth'])) {
            //remove old file
            if(isset($currentData['imageSmall'])) {
                \Modules\administrator\repository\Model::unbindFile($currentData['imageSmall'], 'standard/content_management', $widgetId);
            }
            

            //new small image
            $newData['cropX1'] = $postData['cropX1'];
            $newData['cropY1'] = $postData['cropY1'];
            $newData['cropX2'] = $postData['cropX2'];
            $newData['cropY2'] = $postData['cropY2'];
            $newData['scale'] = $postData['scale'];
            $newData['maxWidth'] = $postData['maxWidth'];
            
            $tmpSmallImageName = $this->cropImage($newData['cropX1'], $newData['cropY1'], $newData['cropX2'], $newData['cropY2'], $newData['scale'], $postData['maxWidth']);
            
            $newData['imageSmall'] = \Modules\administrator\repository\Model::addFile(TMP_IMAGE_DIR.$tmpSmallImageName, 'standard/content_management', $widgetId);
            
            //delete temporary file
            unlink(BASE_DIR.TMP_IMAGE_DIR.$tmpSmallImageName);
        }



        if (isset($postData['title'])) {
            $newData['title'] = $postData['title'];
        }

        return $newData;
    }

    public function delete($widgetId, $data) {
        self::_deleteImage($data, $widgetId);
    }
    
    private function _deleteImage($data, $widgetId) {
        if (!is_array($data)) {
            return;
        }
        if (isset($data['imageOriginal']) && $data['imageOriginal']) {
            \Modules\administrator\repository\Model::unbindFile($data['imageOriginal'], 'standard/content_management', $widgetId);
        }
        if (isset($data['imageBig']) && $data['imageBig']) {
            \Modules\administrator\repository\Model::unbindFile($data['imageBig'], 'standard/content_management', $widgetId);
        }
        if (isset($data['imageSmall']) && $data['imageSmall']) {
            \Modules\administrator\repository\Model::unbindFile($data['imageSmall'], 'standard/content_management', $widgetId);
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
        if (!is_array($data)) {
            return;
        }
        if (isset($data['imageOriginal']) && $data['imageOriginal']) {
            \Modules\administrator\repository\Model::bindFile($data['imageOriginal'], 'standard/content_management', $newId);
        }
        if (isset($data['imageBig']) && $data['imageBig']) {
            \Modules\administrator\repository\Model::bindFile($data['imageBig'], 'standard/content_management', $newId);
        }
        if (isset($data['imageSmall']) && $data['imageSmall']) {
            \Modules\administrator\repository\Model::bindFile($data['imageSmall'], 'standard/content_management', $newId);
        }
    }
    
    /**
     * If theme has changed, we need to crop thumbnails again.
     * @see Modules\standard\content_management.Widget::recreate()
     */
    public function recreate($widgetId, $data) {
        $newData = $data;
        
        //crop big lightbox image from original. Remove old one.
        if ($data['imageOriginal']) {
            //remove old big image
            if (isset($data['imageBig']) && $data['imageBig']) {
                \Modules\administrator\repository\Model::unbindFile($data['imageBig'], 'standard/content_management', $widgetId);
            }
            
            //new big image
            $tmpBigImage = $this->cropBigImage($data['imageOriginal']);
            $newData['imageBig'] = \Modules\administrator\repository\Model::addFile(TMP_IMAGE_DIR.$tmpBigImageName, 'standard/content_management', $widgetId);
            //delete temporary file
            unlink(BASE_DIR.TMP_IMAGE_DIR.$tmpBigImageName);
        }
        
        //crop small image from original. Remove the old one.
        if (isset($data['cropX1']) && isset($data['cropY1']) && isset($data['cropX2']) && isset($data['cropY2']) && isset($data['scale']) && isset($data['maxWidth'])) {
            //remove old file
            if(isset($currentData['imageSmall'])) {
                \Modules\administrator\repository\Model::unbindFile($currentData['imageSmall'], 'standard/content_management', $widgetId);
            }
            
            $tmpSmallImageName = $this->cropImage($data['cropX1'], $data['cropY1'], $data['cropX2'], $data['cropY2'], $data['scale'], $data['maxWidth']);
            $newData['imageSmall'] = \Modules\administrator\repository\Model::addFile(TMP_IMAGE_DIR.$tmpSmallImageName, 'standard/content_management', $widgetId);
            
            //delete temporary file
            unlink(BASE_DIR.TMP_IMAGE_DIR.$tmpSmallImageName);
        }
        return $newData;
    }
   

    private function cropBigImage($imageOriginal) {
        global $parametersMod;
        $bigImageName = \Library\Php\Image\Functions::resize(
            $imageOriginal,
            $parametersMod->getValue('standard', 'content_management', 'widget_image', 'big_width'),
            $parametersMod->getValue('standard', 'content_management', 'widget_image', 'big_height'),
            TMP_IMAGE_DIR,
            \Library\Php\Image\Functions::CROP_TYPE_FIT,
            false,
            $parametersMod->getValue('standard', 'content_management', 'widget_image', 'big_quality')
        );
        return $bigImageName;
    }
    
    private function cropImage($imageOriginal, $cropX1, $cropY1, $cropX2, $cropY2, $scale, $maxWidth) {
        global $parametersMod;
        $ratio = ($cropX2 - $cropX1) / ($cropY2 - $cropY1);
        $requiredWidth = round($maxWidth * $scale);
        $requiredHeight = round($requiredWidth / $ratio);
        $imageName = \Library\Php\Image\Functions::crop (
            $imageOriginal,
            TMP_IMAGE_DIR,
            $cropX1,
            $cropY1,
            $cropX2,
            $cropY2,
            $parametersMod->getValue('standard', 'content_management', 'widget_image', 'quality'),
            $requiredWidth,
            $requiredHeight
        );
        return $imageName;
    }

}