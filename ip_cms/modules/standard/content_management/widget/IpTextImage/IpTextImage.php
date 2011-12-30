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




    public function prepareData($instanceId, $postData, $currentData) {
        global $parametersMod;
        $answer = '';


        $destinationDir = BASE_DIR.IMAGE_DIR;

        $newData = $currentData;


        $newData['text'] = $postData['text'];
        $newData['title'] = $postData['title'];

        if (isset($postData['newImage']) && file_exists(BASE_DIR.$postData['newImage']) && is_file(BASE_DIR.$postData['newImage'])) {

            if (TMP_FILE_DIR.basename($postData['newImage']) != $postData['newImage']) {
                throw new \Exception("Security notice. Try to access an image (".$postData['newImage'].") from a non temporary folder.");
            }

            //new original image
            $unocupiedName = \Library\Php\File\Functions::genUnocupiedName($postData['newImage'], $destinationDir);
            copy($postData['newImage'], $destinationDir.$unocupiedName);
            $newData['imageOriginal'] = IMAGE_DIR.$unocupiedName;



            $bigImageName = \Library\Php\Image\Functions::resize(
            $postData['newImage'],
            $parametersMod->getValue('standard', 'content_management', 'widget_text_photo', 'big_width'),
            $parametersMod->getValue('standard', 'content_management', 'widget_text_photo', 'big_height'),
            BASE_DIR.IMAGE_DIR,
            \Library\Php\Image\Functions::CROP_TYPE_FIT,
            false,
            $parametersMod->getValue('standard', 'content_management', 'widget_text_photo', 'big_quality')
            );
            $newData['imageBig'] = IMAGE_DIR.$bigImageName;
        }

        if (isset($postData['cropX1']) && isset($postData['cropY1']) && isset($postData['cropX2']) && isset($postData['cropY2'])) {
            //new small image
            $ratio = ($postData['cropX2'] - $postData['cropX1']) / ($postData['cropY2'] - $postData['cropY1']);
            $requiredWidth = round($parametersMod->getValue('standard', 'content_management', 'widget_text_photo', 'width'));
            $requiredHeight = round($requiredWidth / $ratio);
            $smallImageName = \Library\Php\Image\Functions::crop (
            $newData['imageOriginal'],
            $destinationDir,
            $postData['cropX1'],
            $postData['cropY1'],
            $postData['cropX2'],
            $postData['cropY2'],
            $parametersMod->getValue('standard', 'content_management', 'widget_text_photo', 'quality'),
            $requiredWidth,
            $requiredHeight
            );
            $newData['imageSmall'] = IMAGE_DIR.$smallImageName;
            $newData['cropX1'] = $postData['cropX1'];
            $newData['cropY1'] = $postData['cropY1'];
            $newData['cropX2'] = $postData['cropX2'];
            $newData['cropY2'] = $postData['cropY2'];

        }

        return $newData;
    }
    
    
    public function managementHtml($instanceId, $data, $layout) {
        global $parametersMod;
        $data['translations']['title'] = $parametersMod->getValue('standard', 'content_management', 'widget_text_photo', 'title');
        return parent::managementHtml($instanceId, $data, $layout);
    }    


}