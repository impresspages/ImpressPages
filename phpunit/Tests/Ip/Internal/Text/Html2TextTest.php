<?php

namespace Tests\Ip\Internal\Text;

use PhpUnit\Helper\TestEnvironment;

class Html2TextTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setupCode();
        ipContent()->_setCurrentLanguage(ipContent()->getLanguageByCode('en'));
    }

    public function testConvetFullHtml2Text()
    {
        $html = '<div class="test">Ventilüberstand</div>';
        $text = \Ip\Internal\Text\Html2Text::convert($html);
        $this->assertEquals('Ventilüberstand', $text);
    }

    public function testConvertPartialHtml2Text()
    {
        $html = '<html lang="en"><head><meta charset="UTF-8" /></head><body><div class="test">Ventilüberstand</div></body>';
        $text = \Ip\Internal\Text\Html2Text::convert($html);
        $this->assertEquals('Ventilüberstand', $text);
    }



}
