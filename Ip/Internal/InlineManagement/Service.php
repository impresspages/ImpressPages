<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\InlineManagement;


class Service
{
    private $dao;

    public function __construct()
    {
        $this->dao = new Dao();
    }

    public function generateManagedLogo($cssClass = null)
    {

        $data = $this->getLogoData();

        if (empty($data['type'])) {
            $data['type'] = 'text';
        }

        if ($data['type'] == 'text') {
            $text = str_replace(' ', '', $data['text']);
            $data['empty'] = $text == '';
        } else {
            $data['empty'] = empty($data['image']);
        }

        $data['cssClass'] = $cssClass;

        if (ipIsManagementState()) {
            return ipView('view/management/logo.php', $data)->render();
        } else {
            return ipView('view/display/logo.php', $data)->render();
        }

    }

    /**
     * Use if you want to generate image logo. No mather what has been chosen by the user.
     * @param $cssClass
     * @return string
     */
    public function generateImageLogo($cssClass = null)
    {
        $data = $this->getLogoData();
        $data['type'] = Entity\Logo::TYPE_IMAGE;
        $data['cssClass'] = $cssClass;
        return ipView('view/display/logo.php', $data)->render();
    }

    /**
     * Use if you watn to generate text logo. No mather what has been chosen by the user.
     * @param $cssClass
     * @return string
     */
    public function generateTextLogo($cssClass)
    {
        $data = $this->getLogoData();
        $data['type'] = Entity\Logo::TYPE_TEXT;
        $data['cssClass'] = $cssClass;
        return ipView('view/display/logo.php', $data)->render();
    }


    public function generateManagedText($key, $tag = 'span', $defaultValue = null, $cssClass = null, $attributes = null)
    {

        $curValue = $this->dao->getLanguageValue(Dao::PREFIX_TEXT, $key, ipContent()->getCurrentLanguage()->getId());

        if ($curValue === false) {
            $curValue = $defaultValue;
        }

        $attributesStr = '';
        if (is_array($attributes)) {
            $attributesStr = join(
                ' ',
                array_map(
                    function ($sKey) use ($attributes) {
                        if (is_bool($attributes[$sKey])) {
                            return $attributes[$sKey] ? $sKey : '';
                        }
                        return $sKey . '="' . $attributes[$sKey] . '"';
                    },
                    array_keys($attributes)
                )
            );
        }

        $data = array(
            'defaultValue' => $defaultValue,
            'value' => $curValue,
            'key' => $key,
            'tag' => $tag,
            'cssClass' => $cssClass,
            'attributes' => $attributes,
            'attributesStr' => $attributesStr
        );

        if (ipIsManagementState()) {
            $view = ipView('view/management/text.php', $data);
        } else {
            $view = ipView('view/display/text.php', $data);
        }
        return $view->render();
    }

    public function generateManagedImage($key, $defaultValue = null, $options = [], $cssClass = null)
    {
        $defaultPlaceholder = ipFileUrl('Ip/Internal/InlineManagement/assets/empty.gif');

        if (isset($options['languageId'])) {
            $languageId = $options['languageId'];
        } else {
            $languageId = ipContent()->getCurrentLanguage()->getId();
        }

        if (isset($options['pageId'])) {
            $pageId = $options['pageId'];
        } else {
            if (ipContent()->getCurrentPage()) {
                $pageId = ipContent()->getCurrentPage()->getId();
            } else {
                $pageId = null;
            }

        }

        // if default value is not defined, we'll add it
        if (empty($defaultValue)) {
            $defaultValue = $defaultPlaceholder;
        }

        $imageStr = $this->dao->getValue(Dao::PREFIX_IMAGE, $key, $languageId, $pageId);

        $image = new Entity\Image($imageStr, $defaultValue);

        $data = array(
            'value' => $image->getImage(),
            'defaultValue' => $defaultValue,
            'empty' => ($image->getImage() == '' || $image->getImage() == $defaultPlaceholder),
            'key' => $key,
            'options' => $options,
            'cssClass' => $cssClass
        );

        if (ipIsManagementState()) {
            $view = ipView('view/management/image.php', $data);
        } else {
            $view = ipView('view/display/image.php', $data);
        }
        return $view->render();
    }


    private function getLogoData()
    {
        $logoStr = $this->dao->getGlobalValue(Dao::PREFIX_LOGO, '');
        $logo = new Entity\Logo($logoStr);

        $data = array(
            'type' => $logo->getType(),
            'link' => ipContent()->getCurrentLanguage()->getLink(),
            'text' => $logo->getText(),
            'image' => $logo->getImage() ? $logo->getImage() : '',
            'font' => $logo->getFont(),
            'color' => $logo->getColor()
        );
        return $data;
    }


}
