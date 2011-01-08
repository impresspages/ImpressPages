<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 JSC Apro media.
 * @license   GNU/GPL, see license.html
 */
namespace Modules\standard\seo;

if (!defined('BACKEND')) exit;
require_once(__DIR__.'/seo_area.php');


class Manager{
  var $standardModule;

  function __construct() {

    $area = new SeoArea();

    $this->standardModule = new \Modules\developer\std_mod\StandardModule($area);
  }


  function manage() {
    return $this->standardModule->manage();
  }




}
