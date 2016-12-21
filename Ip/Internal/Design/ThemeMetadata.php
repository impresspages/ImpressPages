<?php
/**
 * Created by JetBrains PhpStorm.
 * User: algimantas
 * Date: 8/5/13
 * Time: 5:05 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Ip\Internal\Design;


class ThemeMetadata
{
    protected $metadata;

    public function __construct($metadata = [])
    {
        $this->metadata = $metadata;

        if (array_key_exists('name', $metadata)) {
            $this->setName($metadata['name']);
        }
    }

    public function setName($name)
    {
        if (!preg_match('/^([A-Za-z_][A-Za-z0-9_\-]*)$/', $name)) {
            throw new \Exception('Forbidden characters in theme name: ' . $name);
        }

        $this->metadata['name'] = $name;
    }

    public function setTitle($title)
    {
        $this->metadata['title'] = $title;
    }

    public function setDoctype($doctype)
    {
        $this->metadata['doctype'] = $doctype;
    }

    public function setVersion($version)
    {
        $this->metadata['version'] = $version;
    }

    public function setOptions($options)
    {
        $this->metadata['options'] = $options;
    }

    public function setThumbnail($thumbnail)
    {
        $this->metadata['thumbnail'] = $thumbnail;
    }

    public function setAuthorTitle($authorTitle)
    {
        $this->metadata['authorTitle'] = $authorTitle;
    }

    public function setWidgetOptions($widgetOptions)
    {
        $this->metadata['widgetOptions'] = $widgetOptions;
    }

    public function setPath($path)
    {
        $this->metadata['path'] = $path;
    }

    public function setUrl($url)
    {
        $this->metadata['url'] = $url;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }
}
