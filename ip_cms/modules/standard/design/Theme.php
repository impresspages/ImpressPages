<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\standard\design;


class Theme {


    protected $name;
    protected $title;
    protected $doctype;
    protected $version;
    protected $thumbnail;
    protected $options;
    
    public function __construct($name) {
        
        if (!preg_match('/^([A-Za-z_][A-Za-z0-9_]*)$/', $name)) {
            throw new \Exception('Forbidden characters in theme name: '.$name);
        }

        $this->name = $name;
    }


    public function setThumbnail($thumbnail) {
        $this->thumbnail = $thumbnail;
    }
    
    public function getThumbnail() {
        return "/ip_themes/{$this->name}/install/thumbnail.png";
        return $this->thumbnail;
    }
    
    public function getName() {
        return $this->name;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setDoctype($doctype) {
        $this->doctype = $doctype;
    }

    public function getDoctype() {
        return $this->doctype;
    }

    public function setVersion($version) {
        $this->version = $version;
    }

    public function getVersion() {
        return $this->version;
    }

    public function setOptions($options) {
        $this->options = $options;
    }

    public function getOptions() {
        if (!$this->options) {
            return array();
        }
        return $this->options;
    }
    
}