<?php

namespace Tests\Ip\Internal\Translations;

use PhpUnit\Helper\TestEnvironment;

class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setup();
    }

    public function testSetClosureLanguage()
    {
        $installTranslationsDir = ipFile('install/Plugin/Install/translations/');
        \Ip\ServiceLocator::translator()->addTranslationFilePattern('json', $installTranslationsDir, 'Install-%s.json', 'Install');

        $this->assertEquals('Next', __('Next', 'Install'));

        $next = ipSetTranslationLanguage('lt', function() {
            return __('Next', 'Install');
            });

        $this->assertEquals('Toliau', $next);

        $this->assertEquals('Next', __('Next', 'Install'));
    }

    public function testSetTranslationLanguage()
    {
        $installTranslationsDir = ipFile('install/Plugin/Install/translations/');
        \Ip\ServiceLocator::translator()->addTranslationFilePattern('json', $installTranslationsDir, 'Install-%s.json', 'Install');

        $this->assertEquals('Next', __('Next', 'Install'));

        $oldLanguage = ipSetTranslationLanguage('lt');

        $this->assertEquals('en', $oldLanguage);

        $this->assertEquals('Toliau', __('Next', 'Install'));

        ipSetTranslationLanguage($oldLanguage);
        
        $this->assertEquals('Next', __('Next', 'Install'));
    }
}
