<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm\Field;


class Submit extends Field{
    
    public function render($doctype) {
        return '<input type="submit" value="Submit"/>';
    }
    
    public function getLayout() {
        return self::LAYOUT_DEFAULT;
    }
    
}