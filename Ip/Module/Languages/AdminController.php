<?php
/**
 * @package   ImpressPages
 *
 *
 */
namespace Ip\Module\Languages;



class AdminController extends \Ip\Controller{

    public function indexAction() {
        $languageArea = new LanguageArea();
        $stdMod = new \Ip\Lib\StdMod\StandardModule($languageArea, 'Languages.index');
        return $stdMod->manage();
    }

}
