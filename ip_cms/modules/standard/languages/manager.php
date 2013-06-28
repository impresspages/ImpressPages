<?php
/**
 * @package   ImpressPages
 *
 *
 */
namespace Modules\standard\languages;

if (!defined('BACKEND')) exit;
require_once(__DIR__.'/language_area.php');


class Manager{
    var $standardModule;

    function __construct() {
        global $parametersMod;

        $languageArea = new LanguageArea();

        $this->standardModule = new \Modules\developer\std_mod\StandardModule($languageArea);
    }


    function manage() {
        return $this->standardModule->manage();
    }




}
