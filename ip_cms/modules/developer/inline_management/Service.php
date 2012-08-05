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

    public function generateManagedLogo($defaultText = null, $defaultImage = null, $cssClass = null)
    {
        global $site;
        $logo = $this->dao->getValueLogo();
        $curText = $logo->getText();
        $curImage = '';
        if ($curText === false) {
            $curText = $defaultText;
        }

        if ($curImage === false) {
            $curImage = $defaultImage;
        }

        $data = array (
            'link' => $site->generateUrl(),
            'curText' => $curText,
            'curImage' => $curImage,
            'cssClass' => $cssClass
        );

        if ($site->managementState()) {
            $view = \Ip\View::create('view/management/logo.php', $data);
        } else {
            $view = \Ip\View::create('view/display/logo.php', $data);
        }
        return $view->render();
    }

    public function generateManagedString($key, $defaultValue = null, $cssClass = null)
    {
        global $site;
        $curValue = $this->dao->getValueString($key);
        if ($curValue === false) {
            $curValue = $defaultValue;
        }

        $data = array (
            'value' => $curValue,
            'key' => $key,
            'cssClass' => $cssClass
        );

        if ($site->managementState()) {
            $view = \Ip\View::create('view/management/string.php', $data);
        } else {
            $view = \Ip\View::create('view/display/string.php', $data);
        }
        return $view->render();
    }

    public function generateManagedText($key, $defaultValue = null, $cssClass = null)
    {
        global $site;
        $curValue = $this->dao->getValueString($key);
        if ($curValue === false) {
            $curValue = $defaultValue;
        }

        $data = array (
            'value' => $curValue,
            'key' => $key,
            'cssClass' => $cssClass
        );

        if ($site->managementState()) {
            $view = \Ip\View::create('view/management/text.php', $data);
        } else {
            $view = \Ip\View::create('view/display/text.php', $data);
        }
        return $view->render();
    }

    public function generateManagedImage($key, $defaultValue = null, $cssClass = null)
    {
        global $site;
        $curValue = $this->dao->getValueString($key);
        if ($curValue === false) {
            $curValue = $defaultValue;
        }

        $data = array (
            'value' => $curValue,
            'key' => $key,
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