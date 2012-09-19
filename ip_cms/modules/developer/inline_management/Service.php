<?php
    /**
     * @package ImpressPages
     * @copyright   Copyright (C) 2011 ImpressPages LTD.
     * @license see ip_license.html
     */

    namespace Modules\developer\inline_management;


class Service
{
    private $dao;

    public function __construct()
    {
        $this->dao = new Dao();
    }

    public function generateManagedLogo($cssClass = null)
    {
        global $site;

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

        if ($site->managementState()) {
            return \Ip\View::create('view/management/logo.php', $data)->render();
        } else {
            return \Ip\View::create('view/display/logo.php', $data)->render();
        }

    }

    /**
     * Use if you watn to generate image logo. No mather what has been chosen by the user.
     * @param $cssClass
     */
    public function generateImageLogo($cssClass = null)
    {
        $data = $this->getLogoData();
        $data['type']  = Entity\Logo::TYPE_IMAGE;
        $data['cssClass'] = $cssClass;
        return \Ip\View::create('view/display/logo.php', $data)->render();
    }

    /**
     * Use if you watn to generate text logo. No mather what has been chosen by the user.
     * @param $cssClass
     */
    public function generateTextLogo($cssClass)
    {
        $data = $this->getLogoData();
        $data['type']  = Entity\Logo::TYPE_TEXT;
        $data['cssClass'] = $cssClass;
        return \Ip\View::create('view/display/logo.php', $data)->render();
    }

    public function generateManagedString($key, $tag = 'span', $defaultValue = null, $cssClass = null)
    {
        global $site;
        $curValue = $this->dao->getLanguageValue(Dao::PREFIX_STRING, $key, $site->getCurrentLanguage()->getId());
        if ($curValue === false) {
            $curValue = $defaultValue;
        }

        $data = array (
            'defaultValue' => $defaultValue,
            'value' => $curValue,
            'key' => $key,
            'tag' => $tag,
            'cssClass' => $cssClass
        );

        if ($site->managementState()) {
            $view = \Ip\View::create('view/management/string.php', $data);
        } else {
            $view = \Ip\View::create('view/display/string.php', $data);
        }
        return $view->render();
    }

    public function generateManagedText($key, $tag = 'span', $defaultValue = null, $cssClass = null)
    {
        global $site;
        $curValue = $this->dao->getLanguageValue(Dao::PREFIX_TEXT, $key, $site->getCurrentLanguage()->getId());

        if ($curValue === false) {
            $curValue = $defaultValue;
        }

        $data = array (
            'defaultValue' => $defaultValue,
            'value' => $curValue,
            'key' => $key,
            'tag' => $tag,
            'cssClass' => $cssClass
        );

        if ($site->managementState()) {
            $view = \Ip\View::create('view/management/text.php', $data);
        } else {
            $view = \Ip\View::create('view/display/text.php', $data);
        }
        return $view->render();
    }

    public function generateManagedImage($key, $defaultValue = null, $options = array(), $cssClass = null)
    {
        global $site;

        if ($defaultValue === null) {
            $defaultValue = MODULE_DIR.'inline_management/public/empty.gif';
        }

        $imageStr = $this->dao->getValue(Dao::PREFIX_IMAGE, $key, $site->getCurrentLanguage()->getId(), $site->getCurrentZone()->getName(), $site->getCurrentElement()->getId());
        $image = new Entity\Image($imageStr, $defaultValue);

        $data = array (
            'value' => $image->getImage(),
            'defaultValue' => $defaultValue,
            'empty' => $image->getImage() == '',
            'key' => $key,
            'options' => $options,
            'cssClass' => $cssClass
        );

        if ($site->managementState()) {
            $view = \Ip\View::create('view/management/image.php', $data);
        } else {
            $view = \Ip\View::create('view/display/image.php', $data);
        }
        return $view->render();
    }


    private function getLogoData()
    {
        global $site;
        $logoStr = $this->dao->getGlobalValue(Dao::PREFIX_LOGO, '');
        $logo = new Entity\Logo($logoStr);

        $data = array (
            'type' => $logo->getType(),
            'link' => $site->generateUrl(),
            'text' => $logo->getText(),
            'image' => $logo->getImage() ? $logo->getImage() : '',
            'font' => $logo->getFont(),
            'color' => $logo->getColor()
        );
        return $data;
    }


}