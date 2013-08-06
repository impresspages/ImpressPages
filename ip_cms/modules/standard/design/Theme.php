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
    
    public function __construct(ThemeMetadata $metadata) {
        $properties = $metadata->getMetadata();
        foreach ($properties as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getThumbnail() {
        return "/ip_themes/{$this->name}/install/thumbnail.png";
        return $this->thumbnail;
    }
    
    public function getName() {
        return $this->name;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDoctype() {
        return $this->doctype;
    }

    public function getVersion() {
        return $this->version;
    }

    public function getOptions() {
        if (!$this->options) {
            return array();
        }
        return $this->options;
    }
    
}