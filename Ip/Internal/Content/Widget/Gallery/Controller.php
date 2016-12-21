<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content\Widget\Gallery;


class Controller extends \Ip\WidgetController
{

    public function getTitle()
    {
        return __('Gallery', 'Ip-admin', false);
    }


    public function update($widgetId, $postData, $currentData)
    {

        if (isset($postData['method'])) {
            switch ($postData['method']) {
                case 'move':
                    if (!isset($postData['originalPosition'])) {
                        throw new \Ip\Exception("Missing required parameter");
                    }
                    $originalPosition = $postData['originalPosition'];
                    if (!isset($postData['newPosition'])) {
                        throw new \Ip\Exception("Missing required parameter");
                    }
                    $newPosition = $postData['newPosition'];

                    if (!isset($currentData['images'][$originalPosition])) {
                        throw new \Ip\Exception("Moved image doesn't exist");
                    }

                    $movedImage = $currentData['images'][$originalPosition];
                    unset($currentData['images'][$originalPosition]);
                    array_splice($currentData['images'], $newPosition, 0, array($movedImage));
                    return $currentData;
                case 'add':
                    if (!isset($postData['images']) || !is_array($postData['images'])) {
                        throw new \Ip\Exception("Missing required parameter");
                    }

                    $newImages = [];

                    foreach ($postData['images'] as $image) {
                        if (!isset($image['fileName']) || !isset($image['status'])) { //check if all required data present
                            continue;
                        }

                        //just to be sure
                        if (!file_exists(ipFile('file/repository/' . $image['fileName']))) {
                            continue;
                        }

                        //bind new image to the widgetx
                        \Ip\Internal\Repository\Model::bindFile($image['fileName'], 'Content', $widgetId);


                        //find image title
                        if (!empty($image['title'])) {
                            $title = $image['title'];
                        } else {
                            $title = basename($image['fileName']);
                        }

                        $newImage = array(
                            'imageOriginal' => $image['fileName'],
                            'title' => $title,
                        );

                        $newImages[] = $newImage;
                    }
                    if (empty($currentData['images']) || !is_array($currentData['images'])) {
                        $currentData['images'] = [];
                    }

                    if(ipGetOption('Content.imageGalleryPosition') == 'bottom') {
                        $currentData['images'] = array_merge($currentData['images'], $newImages);
                    } else {
                        $currentData['images'] = array_merge($newImages, $currentData['images']);
                    }


                    return $currentData;
                case 'crop':
                    break;
                case 'update' :

                    $tmpData = $currentData['images'][$postData['imageIndex']];
                    if ($tmpData['imageOriginal'] != $postData['fileName']) {
                        $this->_deleteOneImage($tmpData, $widgetId);
                        //bind new image to the widget
                        \Ip\Internal\Repository\Model::bindFile($postData['fileName'], 'Content', $widgetId);
                        $tmpData['imageOriginal'] = $postData['fileName'];
                    }

                    //check if crop coordinates are set
                    if (isset($postData['cropX1']) && isset($postData['cropY1']) && isset($postData['cropX2']) && isset($postData['cropY2'])) {
                        $tmpData['cropX1'] = $postData['cropX1'];
                        $tmpData['cropY1'] = $postData['cropY1'];
                        $tmpData['cropX2'] = $postData['cropX2'];
                        $tmpData['cropY2'] = $postData['cropY2'];
                    }

                    $currentData['images'][$postData['imageIndex']] = $tmpData;
                    return $currentData;
                    break;

                case 'setLink':
                    if (!isset($postData['index'])) {
                        throw new \Ip\Exception("Missing required parameter");
                    }
                    $index = $postData['index'];
                    if (isset($postData['type'])) {
                        $currentData['images'][$index]['type'] = $postData['type'];
                    }
                    if (isset($postData['url'])) {
                        $currentData['images'][$index]['url'] = $postData['url'];
                    }
                    if (isset($postData['blank'])) {
                        $currentData['images'][$index]['blank'] = (int)$postData['blank'];
                    }
                    if (isset($postData['nofollow'])) {
                        $currentData['images'][$index]['nofollow'] = (int) $postData['nofollow'];
                    }
                    return $currentData;

                    break;
                case 'delete':
                    if (!isset($postData['position'])) {
                        throw new \Ip\Exception("Missing required parameter");
                    }
                    $deletePosition = (int)$postData['position'];


                    $this->_deleteOneImage($currentData['images'][$deletePosition], $widgetId);

                    unset($currentData['images'][$deletePosition]);
                    $currentData['images'] = array_values($currentData['images']); // 'reindex' array
                    return $currentData;
                case 'saveSettings':
                    if (!isset($postData['index'])) {
                        throw new \Ip\Exception("Missing required parameter");
                    }
                    $index = $postData['index'];

                    if (isset($postData['title'])) {
                        $currentData['images'][$index]['title'] = $postData['title'];
                    }
                    if (isset($postData['description'])) {
                        $currentData['images'][$index]['description'] = $postData['description'];
                    }
                    return $currentData;

                    break;
                default:
                    throw new \Ip\Exception('Unknown command');

            }
        }


        return $currentData;
    }


    public function adminHtmlSnippet()
    {
        $variables = array(
            'linkForm' => $this->linkForm(),
            'settingsForm' => $this->settingsForm()
        );
        return ipView('snippet/gallery.php', $variables)->render();

    }


    public function generateHtml($revisionId, $widgetId, $data, $skin)
    {

        if (isset($data['images']) && is_array($data['images'])) {
            //loop all current images
            foreach ($data['images'] as &$curImage) {
                if (empty($curImage['imageOriginal'])) {
                    continue;
                }
                $desiredName = isset($curImage['title']) ? $curImage['title'] : null;

                //create big image reflection
                $bigWidth = ipGetOption('Config.lightboxWidth', 800);
                $bigHeight = ipGetOption('Config.lightboxHeight', 600);

                $transformBig = array(
                    'type' => 'fit',
                    'width' => $bigWidth,
                    'height' => $bigHeight
                );
                $curImage['imageBig'] = ipFileUrl(
                    ipReflection($curImage['imageOriginal'], $transformBig, $desiredName)
                );


                $curImage['imageSmall'] = $this->cropSmallImage($curImage);


                if (empty($curImage['type'])) {
                    $curImage['type'] = 'lightbox';
                }
                if (empty($curImage['url'])) {
                    $curImage['url'] = '';
                } else {
                    if (!preg_match('/^((http|https):\/\/)/i', $curImage['url'])) {
                        $curImage['url'] = 'http://' . $curImage['url'];
                    }
                }
                if (empty($curImage['blank'])) {
                    $curImage['blank'] = '';
                }
                if (empty($curImage['nofollow'])) {
                    $curImage['nofollow'] = '';
                }
                if (empty($curImage['title'])) {
                    $curImage['title'] = '';
                }


            }
        }
        return parent::generateHtml($revisionId, $widgetId, $data, $skin);
    }


    protected function cropSmallImage($curImage)
    {
        if (!isset($curImage['title'])) {
            $curImage['title'] = '';
        }
        $smallImageUrl = null;
        if (isset($curImage['cropX1']) && isset($curImage['cropY1']) && isset($curImage['cropX2']) && isset($curImage['cropY2'])) {
            $transformSmall = array(
                'type' => 'crop',
                'x1' => $curImage['cropX1'],
                'y1' => $curImage['cropY1'],
                'x2' => $curImage['cropX2'],
                'y2' => $curImage['cropY2'],
                'width' => ipGetOption('Content.widgetGalleryWidth'),
                'height' => ipGetOption('Content.widgetGalleryHeight'),
                'quality' => ipGetOption('Content.widgetGalleryQuality')
            );
        } else {
            $transformSmall = array(
                'type' => 'center',
                'width' => ipGetOption('Content.widgetGalleryWidth'),
                'height' => ipGetOption('Content.widgetGalleryHeight'),
                'quality' => ipGetOption('Content.widgetGalleryQuality')
            );
        }
        $smallImageUrl = '';
        if (!empty($curImage['imageOriginal'])) {
            $smallImageUrl = ipFileUrl(ipReflection($curImage['imageOriginal'], $transformSmall, $curImage['title']));
        }
        return $smallImageUrl;
    }

    /**
     * Process data which is passed to widget's JavaScript file for processing
     *
     * @param int $revisionId Widget revision ID
     * @param int $widgetId Widget ID
     * @param int $widgetId Widget instance ID
     * @param array $data Widget data array
     * @param string $skin Widget skin name
     * @return array Data array
     */
    public function dataForJs($revisionId, $widgetId, $data, $skin)
    {
        if (isset($data['images']) && is_array($data['images'])) {
            //loop all current images
            foreach ($data['images'] as &$curImage) {
                if (!is_array($curImage)) {
                    $curImage = [];
                }
                $curImage['imageSmall'] = $this->cropSmallImage($curImage);
            }
        }
        return $data;
    }


    public function delete($widgetId, $data)
    {
        if (!isset($data['images']) || !is_array($data['images'])) {
            return;
        }

        foreach ($data['images'] as $image) {
            self::_deleteOneImage($image, $widgetId);
        };
    }

    private function _deleteOneImage($image, $widgetId)
    {
        if (!is_array($image)) {
            return;
        }
        if (isset($image['imageOriginal']) && $image['imageOriginal']) {
            \Ip\Internal\Repository\Model::unbindFile($image['imageOriginal'], 'Content', $widgetId);
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
     * @return array
     */
    public function duplicate($oldId, $newId, $data)
    {
        if (!isset($data['images']) || !is_array($data['images'])) {
            return null;
        }

        foreach ($data['images'] as $image) {
            if (!is_array($image)) {
                return null;
            }
            if (isset($image['imageOriginal']) && $image['imageOriginal']) {
                \Ip\Internal\Repository\Model::bindFile($image['imageOriginal'], 'Content', $newId);
            }
        }
        return $data;
    }


    protected function linkForm()
    {
        $form = new \Ip\Form();
        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);

        $field = new \Ip\Form\Field\Select(
            array(
                'name' => 'type',
                'label' => __('Mouse click action', 'Ip-admin', false),
            ));

        $values = array(
            array('lightbox', __('Lightbox', 'Ip-admin', false)),
            array('link', __('URL', 'Ip-admin', false)),
            array('none', __('None', 'Ip-admin', false)),
        );
        $field->setValues($values);
        $form->addfield($field);


        $field = new \Ip\Form\Field\Url(
            array(
                'name' => 'url',
                'label' => __('Url', 'Ip-admin', false),
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'blank',
                'label' => __('Open in new window', 'Ip-admin', false),
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'nofollow',
                'label' => __('Set rel="nofollow" attribute', 'Ip-admin', false),
            ));
        $form->addField($field);

        return $form; // Output a string with generated HTML form
    }


    protected function settingsForm()
    {
        $form = new \Ip\Form();

        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'title',
                'label' => __('Title', 'Ip-admin', false),
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Textarea(
            array(
                'name' => 'description',
                'label' => __('Description', 'Ip-admin', false),
            ));
        $form->addField($field);


        return $form; // Output a string with generated HTML form
    }

    /**
     * Array 0f menu items to be added to the widget's options menu. (gear box on the left top corner of the widget)
     * @param $revisionId
     * @param $widgetId
     * @param $data
     * @param $skin
     * @return array
     */
    public function optionsMenu($revisionId, $widgetId, $data, $skin)
    {
        $answer = [];
        $answer[] = array(
            'title' => __('Add image', 'Ip-admin', false),
            'attributes' => array(
                'class' => 'ipsAdd'
            )
        );
        $answer[] = array(
            'title' => __('Manage images', 'Ip-admin', false),
            'attributes' => array(
                'class' => 'ipsManage'
            )
        );
        return $answer;
    }

}
