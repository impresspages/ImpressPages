<?php
/**
 * @package   ImpressPages
 */
//TODOX remove if not used
namespace Plugin\Install;


class DummyOptionsService extends \Ip\Options
{
    public function getOption($key, $defaultValue = null)
    {
        return '';
    }

    public function getOptionLang($key, $languageId, $defaultValue = null)
    {
        return $this->getOption($key, $defaultValue);
    }


    public function setOption($key, $value)
    {
    }

    public function setOptionLang($key, $languageId, $value)
    {
    }

    public function removeOption($key)
    {
    }

    public function removeOptionLang($key, $languageId)
    {
    }

    public function import($configFile)
    {
    }

    function getAllOptions()
    {
    }

}