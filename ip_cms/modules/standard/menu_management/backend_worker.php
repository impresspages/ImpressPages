<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\menu_management;

if (!defined('BACKEND')) exit;



class BackendWorker {


  function __construct() {

  }


  function work() {

    $result[] = array(
				"attr" => array("id" => "1"),
				"data" => "title",
				"state" => "closed"
		);

    $result[] = array(
				"attr" => array("id" => "2"),
				"data" => "title2",
				"state" => "closed"
		);
		
		echo json_encode($result);

		//echo 'oho';

  }

}