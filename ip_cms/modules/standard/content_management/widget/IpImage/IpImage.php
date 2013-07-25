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
            //remove old image
            if (isset($currentData['imageOriginal']) && $currentData['imageOriginal']) {
                \Modules\administrator\repository\Model::unbindFile($currentData['imageOriginal'], 'standard/content_management', $widgetId);
            }
            
            //new original image
            \Modules\administrator\repository\Model::bindFile($postData['newImage'], 'standard/content_management', $widgetId);
            $newData['imageOriginal'] = $postData['newImage'];
            
        }

        if (isset($postData['cropX1']) && isset($postData['cropY1']) && isset($postData['cropX2']) && isset($postData['cropY2']) && isset($postData['scale']) && isset($postData['maxWidth'])) {
            //new small image
            $newData['cropX1'] = $postData['cropX1'];
            $newData['cropY1'] = $postData['cropY1'];
            $newData['cropX2'] = $postData['cropX2'];
            $newData['cropY2'] = $postData['cropY2'];
            $newData['scale'] = $postData['scale'];
            $newData['maxWidth'] = $postData['maxWidth'];
            
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
    }



    public function previewHtml($instanceId, $data, $layout) {

        if (isset($data['imageOriginal'])) {
            $reflectionService = \Modules\administrator\repository\ReflectionService::instance();
            $desiredName = isset($data['title']) ? $data['title'] : '';

            $transformBig = new \Modules\administrator\repository\Transform\None();
            $data['imageBig'] = $reflectionService->getReflection($data['imageOriginal'], $desiredName, $transformBig);

            if (isset($data['cropX1']) && isset($data['cropY1']) && isset($data['cropX2']) && isset($data['cropY2']) && isset($data['maxWidth']) && isset($data['scale'])) {
                $ratio = ($data['cropX2'] - $data['cropX1']) / ($data['cropY2'] - $data['cropY1']);
                $requiredWidth = round($data['maxWidth'] * $data['scale']);
                $requiredHeight = round($requiredWidth / $ratio);


                $transformSmall = new \Modules\administrator\repository\Transform\ImageCrop(
                    $data['cropX1'],
                    $data['cropY1'],
                    $data['cropX2'],
                    $data['cropY2'],
                    $requiredWidth,
                    $requiredHeight
                );
                $data['imageSmall'] = $reflectionService->getReflection($data['imageOriginal'], $data['title'], $transformSmall);
            }
        }
        return parent::previewHtml($instanceId, $data, $layout);
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