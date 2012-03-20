<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2012 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\theme;

if (!defined('CMS')) exit;


class Theme{
    const INSTALL_DIR = 'install/';

    private $name;
    private $previewImage;
    
    public function __construct($name) {
        
        if (!preg_match('/^([A-Za-z_][A-Za-z0-9_]*)$/', $name)) {
            throw new \Exception('Forbidden characters in theme name: '.$name);
        }
        
        $this->name = $name;
        if (file_exists(BASE_DIR.THEME_DIR.$name.'/'.self::INSTALL_DIR.'preview.png')) {
            $this->previewImage = THEME_DIR.$name.'/'.self::INSTALL_DIR.'preview.png';
        }
    }
    
    public function getPreviewImage() {
        return $this->previewImage;
    }
    
    public function getName() {
        return $this->name;
    }
    
}