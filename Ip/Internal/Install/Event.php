<?php
/**
 * Created by PhpStorm.
 * User: maskas
 * Date: 16.12.4
 * Time: 18.47
 */

namespace Ip\Internal\Install;


class Event
{
    public static function ipInitFinished() {
        if (!ipConfig()->isEmpty()) {
            return null;
        }
        $translator = \Ip\ServiceLocator::translator();
        $installDir = ipFile('Ip/Internal/Install/translations/');
        $translator->addTranslationFilePattern('json', $installDir, 'Install-%s.json', 'Install');
    }
}