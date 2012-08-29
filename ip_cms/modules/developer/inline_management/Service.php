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

    public function generateManagedLogo($defaultLogo = null, $cssClass = null)
    {
        global $site;
        $logoStr = $this->dao->getGlobalValue(Dao::PREFIX_LOGO, '');
        $logo = new Entity\Logo($logoStr, $defaultLogo);

        $data = array (
            'type' => $logo->getType(),
            'link' => $site->generateUrl(),
            'text' => $logo->getText(),
            'image' => $logo->getImage() ? $logo->getImage() : '',
            'font' => $logo->getFont(),
            'color' => $logo->getColor(),
            'cssClass' => $cssClass,
        );




        if ($site->managementState()) {
            $data['type'] = Entity\Logo::TYPE_TEXT;
            $logoTextHtml = \Ip\View::create('view/display/logo.php', $data)->render();
            $data['type'] = Entity\Logo::TYPE_IMAGE;
            $logoImageHtml = \Ip\View::create('view/display/logo.php', $data)->render();

            $managementData = array(
                'type' => $logo->getType(),
                'logoTextHtml' => $logoTextHtml,
                'logoImageHtml' => $logoImageHtml,
                'cssClass' => $cssClass
            );
            return \Ip\View::create('view/management/logo.php', $managementData)->render();
        } else {
            $logoHtml = \Ip\View::create('view/display/logo.php', $data)->render();
            return $logoHtml;
        }

    }

    public function generateManagedString($key, $tag = 'span', $defaultValue = null, $cssClass = null)
    {
        global $site;
        $curValue = $this->dao->getLanguageValue(Dao::PREFIX_STRING, $key, $site->getCurrentLanguage()->getId());
        if ($curValue === false) {
            $curValue = $defaultValue;
        }

        $data = array (
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

        $imageStr = $this->dao->getGlobalValue(Dao::PREFIX_IMAGE, $key);
        $image = new Entity\Image($imageStr, $defaultValue);


        $data = array (
            'value' => $image->getImage(),
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


}