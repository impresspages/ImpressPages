<?php

class ConfigurationParserTest extends PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $configurationParser = new Library\Model\ConfigurationParser(); 
        $this->configuration = $configurationParser->parse(FIXTURE_DIR.'Model/ConfigurationParser/');
        
    }
}
