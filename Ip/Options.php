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
        return \Ip\ServiceLocator::storage()->get('Config', $parts[0] . '.' . $parts[1], $defaultValue);
    }

    public function setOption($key, $value)
    {
        $parts = explode('.', $key, 2);
        if (!isset($parts[1])) {
            throw new \Ip\CoreException("Option key must have plugin name separated by dot.");
        }
        \Ip\ServiceLocator::storage()->set('Config', $parts[0] . '.' . $parts[1], $value);
    }

    /**
     * @param string $configFile
     * @throws CoreException
     */
    public function import($configFile)
    {
        $content = file_get_contents($configFile);
        $values = json_decode($content, true);
        if (!is_array($values)) {
            throw new \Ip\CoreException("Can't parse configuration file: " . $configFile);
        }
        foreach ($values as $key => $value) {
            ipSetOption($key, $value);
        }
    }

    function getAllOptions()
    {
        $optionValues = \Ip\ServiceLocator::storage()->getAll('Config');
        $options = array();
        foreach ($optionValues as $option) {
            $options[$option['key']] = $option['value'];
        }
        return $options;
    }
}