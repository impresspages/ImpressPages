<?php
/**
 * @package   ImpressPages
 *
 *
 */
namespace Ip\Internal\Languages;



class AdminController extends \Ip\Controller{

    public function index() {
        $languageArea = new LanguageArea();
        $stdMod = new \Ip\Lib\StdMod\StandardModule($languageArea, 'Languages.index');
        return $stdMod->manage();
    }

}
