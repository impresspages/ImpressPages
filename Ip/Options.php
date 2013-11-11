<?php
/**
 * @package ImpressPages

 *
 */

namespace Ip;



class Options
{
    public function getOption($key, $defaultValue = null)
    {
        $parts = explode('.', $key, 2);
        if (!isset($parts[1])) {
            throw new \Ip\CoreException("Option key must have plugin name separated by dot.");
        }
        return \Ip\ServiceLocator::getStorage()->get($parts[0], 'option.' . $parts[1], $defaultValue);
    }

    public function setOption($key, $value)
    {
        $parts = explode('.', $key, 2);
        if (!isset($parts[1])) {
            throw new \Ip\CoreException("Option key must have plugin name separated by dot.");
        }
        \Ip\ServiceLocator::getStorage()->set($parts[0], 'option.' . $parts[1], $value);
    }
}