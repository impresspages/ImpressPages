<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2012 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\administrator\theme;



if (!defined('CMS')) exit;


class Theme{
    const INSTALL_DIR = 'install/';
    const PARAMETERS_FILE = 'parameters.php';
    
    private $name;
    private $title;
    private $doctype;
    private $version;
    private $previewImage;
    
    public function __construct($name) {
        
        if (!preg_match('/^([A-Za-z_][A-Za-z0-9_]*)$/', $name)) {
            throw new \Exception('Forbidden characters in theme name: '.$name);
        }
        
        $this->name = $name;
        if (file_exists(BASE_DIR.THEME_DIR.$name.'/'.self::INSTALL_DIR.'thumbnail.png')) {
            $this->previewImage = THEME_DIR.$name.'/'.self::INSTALL_DIR.'thumbnail.png';
        }
        
        $configuration = new ConfigurationFile(BASE_DIR.THEME_DIR.$name.'/'.self::INSTALL_DIR.'theme.ini');
        if ($configuration->getThemeTitle()) {
            $this->title = $configuration->getThemeTitle();
        } else {
            $this->title = $name;
        }
        
        $this->version = $configuration->getThemeVersion();
        
        if (defined('\Ip\View::'.$configuration->getThemeDoctype())) {
            $this->doctype = $configuration->getThemeDoctype();
        } else {
            $this->doctype = 'DOCTYPE_HTML5';
        }
        
    }
    
    public function getPreviewImage() {
        return $this->previewImage;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getDoctype() {
        return $this->doctype;
    }
    
    public function getVersion() {
        return $this->version;
    }
    
}