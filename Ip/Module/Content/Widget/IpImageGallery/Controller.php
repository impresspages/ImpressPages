<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content\Widget\IpImageGallery;




class Controller extends \Ip\WidgetController{

    public function getTitle() {
        return __('Photo gallery', 'ipAdmin', false);
    }

    

    public function update($widgetId, $postData, $currentData) {

        if (isset($postData['method'])) {
            switch($postData['method']) {
                case 'move':
                    if (!isset($postData['originalPosition'])) {
                        throw new \Ip\CoreException("Missing required parameter");
                    }
                    $originalPosition = $postData['originalPosition'];
                    if (!isset($postData['newPosition'])) {
                        throw new \Ip\CoreException("Missing required parameter");
                    }
                    $newPosition = $postData['newPosition'];

                    if (!isset($currentData['images'][$originalPosition])) {
                        throw new \Ip\CoreException("Moved image doesn't exist");
                    }

                    $movedImage = $currentData['images'][$originalPosition];
                    unset($currentData['images'][$originalPosition]);
                    array_splice($currentData['images'], $newPosition, 0, array($movedImage));
                    return $currentData;
                case 'add':
                    if (!isset($postData['images']) || !is_array($postData['images'])) {
                        throw new \Ip\CoreException("Missing required parameter");
                    }


                    foreach($postData['images'] as $image){
                        if (!isset($image['fileName']) || !isset($image['status'])){ //check if all required data present
                            continue;
                        }

                        //just to be sure
                        if (!file_exists(ipFile('file/repository/' . $image['fileName']))) {
                            continue;
                        }

                        //bind new image to the widget
                        \Ip\Module\Repository\Model::bindFile($image['fileName'], 'Content', $widgetId);


                        //find image title
                        if ($image['title'] == '') {
                            $title = basename($image['fileName']);
                        } else {
                            $title = $image['title'];
                        }

                        $newImage = array(
                            'imageOriginal' => $image['fileName'],
                            'title' => $title,
                        );

                        $currentData['images'][] = $newImage;
                    }

                    return $currentData;
                default:
                    throw new \Ip\CoreException('Unknown command');

            }

        }


        $newData = $currentData;
        $newData['images'] = array(); //we will create new images array.



        //check if images array is set
        if (!isset($postData['images']) || !is_array($postData['images'])) {
            return $newData;
        }

        foreach($postData['images'] as $image){
            if (!isset($image['fileName']) || !isset($image['status'])){ //check if all required data present
                continue;
            }

            switch($image['status']){
                case 'new':
                    //just to be sure
                    if (!file_exists(ipFile('file/repository/' . $image['fileName']))) {
                        break;
                    }

                    //bind new image to the widget
                    \Ip\Module\Repository\Model::bindFile($image['fileName'], 'Content', $widgetId);



                    //find image title
                    if ($image['title'] == '') {
                        $title = basename($image['fileName']);
                    } else {
                        $title = $image['title'];
                    }

                    $newImage = array(
                        'imageOriginal' => $image['fileName'],
                        'title' => $title,
                    );

                    //check if crop coordinates are set
                    if (isset($image['cropX1']) && isset($image['cropY1']) && isset($image['cropX2']) && isset($image['cropY2'])) {
                        $newImage['cropX1'] = $image['cropX1'];
                        $newImage['cropY1'] = $image['cropY1'];
                        $newImage['cropX2'] = $image['cropX2'];
                        $newImage['cropY2'] = $image['cropY2'];
                    }



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


    public function adminSnippets()
    {
        $snippets = array();
        $snippets[] = \Ip\View::create('snippet/gallery.php', $data)->render();
        return $snippets;

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




    public function previewHtml($instanceId, $data, $layout)
    {
        $reflectionService = \Ip\Module\Repository\ReflectionService::instance();

        if (isset($data['images']) && is_array($data['images'])) {
            //loop all current images
            foreach ($data['images'] as $curImageKey => &$curImage) {
                if (empty($curImage['imageOriginal'])) {
                    continue;
                }
                $desiredName = isset($curImage['title']) ? $curImage['title'] : '';

                //create big image reflection
                $bigWidth = ipGetOption('Content.widgetImageGalleryBigWidth');
                $bigHeight = ipGetOption('Content.widgetImageGalleryBigHeight');
                $transformBig = new \Ip\Module\Repository\Transform\ImageFit($bigWidth, $bigHeight);

                try {
                    $curImage['imageBig'] = $reflectionService->getReflection($curImage['imageOriginal'], $desiredName, $transformBig);
                } catch (\Ip\Module\Repository\Exception $e) {
                    //do nothing
                }



                if (isset($curImage['cropX1']) && isset($curImage['cropY1']) && isset($curImage['cropX2']) && isset($curImage['cropY2']) ) {
                    $transformSmall = new \Ip\Module\Repository\Transform\ImageCrop(
                        $curImage['cropX1'],
                        $curImage['cropY1'],
                        $curImage['cropX2'],
                        $curImage['cropY2'],
                        ipGetOption('Content.widgetImageGalleryWidth'),
                        ipGetOption('Content.widgetImageGalleryHeight'),
                        ipGetOption('Content.widgetImageGalleryQuality')
                    );

                } else {
                    $transformSmall = new \Ip\Module\Repository\Transform\ImageCropCenter(
                        ipGetOption('Content.widgetImageGalleryWidth'),
                        ipGetOption('Content.widgetImageGalleryHeight'),
                        ipGetOption('Content.widgetImageGalleryQuality')
                    );

                }
                try {
                    $curImage['imageSmall'] = ipFileUrl('file/' . $reflectionService->getReflection($curImage['imageOriginal'], $curImage['title'], $transformSmall));
                } catch (\Ip\Module\Repository\Exception $e) {
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
            \Ip\Module\Repository\Model::unbindFile($image['imageOriginal'], 'Content', $widgetId);
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
                \Ip\Module\Repository\Model::bindFile($image['imageOriginal'], 'Content', $newId);
            }
        }

    }

}