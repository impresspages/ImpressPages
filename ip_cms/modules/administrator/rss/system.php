<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\administrator\rss;
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;




require_once (__DIR__.'/db.php');

class System{

    function clearCache($cachedBaseUrl){
        Db::clearCache();
    }

}