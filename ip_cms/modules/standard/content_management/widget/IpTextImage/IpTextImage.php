<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management\widget;

if (!defined('CMS')) exit;

require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget.php');

class IpTextImage extends \Modules\standard\content_management\Widget{

    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('standard', 'content_management', 'widget_text_image', 'text_image');
    }


    public function update($widgetId, $postData, $currentData) {
        global $parametersMod;
        $answer = '';



        $newData = $currentData;


        $newData['text'] = $postData['text'];
        $newData['title'] = $postData['title'];

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
            $tmpBigImageName = \Library\Php\Image\Functions::resize(
            $postData['newImage'],
            $parametersMod->getValue('standard', 'content_management', 'widget_text_image', 'big_width'),
            $parametersMod->getValue('standard', 'content_management', 'widget_text_image', 'big_height'),
            TMP_IMAGE_DIR,
            \Library\Php\Image\Functions::CROP_TYPE_FIT,
            false,
            $parametersMod->getValue('standard', 'content_management', 'widget_text_image', 'big_quality')
            );
            $newData['imageBig'] = \Modules\administrator\repository\Model::addFile(TMP_IMAGE_DIR.$tmpBigImageName, 'standard/content_management', $widgetId);
            unlink(BASE_DIR.TMP_IMAGE_DIR.$tmpBigImageName);
            
        }

        if (isset($newData['imageOriginal']) &&  isset($postData['cropX1']) && isset($postData['cropY1']) && isset($postData['cropX2']) && isset($postData['cropY2'])) {
            //remove old file
            if(isset($currentData['imageSmall'])) {
                \Modules\administrator\repository\Model::unbindFile($currentData['imageSmall'], 'standard/content_management', $widgetId);
            }
            
            //new small image
            $ratio = ($postData['cropX2'] - $postData['cropX1']) / ($postData['cropY2'] - $postData['cropY1']);
            $requiredWidth = round($parametersMod->getValue('standard', 'content_management', 'widget_text_image', 'width'));
            $requiredHeight = round($requiredWidth / $ratio);
            $tmpSmallImageName = \Library\Php\Image\Functions::crop (
            $newData['imageOriginal'],
            TMP_IMAGE_DIR,
            $postData['cropX1'],
            $postData['cropY1'],
            $postData['cropX2'],
            $postData['cropY2'],
            $parametersMod->getValue('standard', 'content_management', 'widget_text_image', 'quality'),
            $requiredWidth,
            $requiredHeight
            );
            $newData['imageSmall'] = \Modules\administrator\repository\Model::addFile(TMP_IMAGE_DIR.$tmpSmallImageName, 'standard/content_management', $widgetId);
            unlink(BASE_DIR.TMP_IMAGE_DIR.$tmpSmallImageName);
            
            $newData['cropX1'] = $postData['cropX1'];
            $newData['cropY1'] = $postData['cropY1'];
            $newData['cropX2'] = $postData['cropX2'];
            $newData['cropY2'] = $postData['cropY2'];

        }

        return $newData;
    }
    
    
    public function managementHtml($instanceId, $data, $layout) {
        global $parametersMod;
        $data['translations']['title'] = $parametersMod->getValue('standard', 'content_management', 'widget_text_image', 'title');
        return parent::managementHtml($instanceId, $data, $layout);
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

}