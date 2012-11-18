<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\administrator\repository;


class Controller extends \Ip\Controller{


    public function browserPopupHtml() {
        $answer = array();

        $answer['html'] = \Ip\View::create('view/popup.php')->render();

        $this->returnJson($answer);

    }



}
