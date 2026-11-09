<?php

namespace Ip\Internal\Translations;

use Laminas\I18n\Translator\Loader\FileLoaderInterface;
use Laminas\I18n\Exception;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;
use Laminas\I18n\Translator\TextDomain;

/**
 * PHP array loader.
 */
class JsonLoader implements FileLoaderInterface
{
    /**
     * load(): defined by FileLoaderInterface.
     *
     * @see    FileLoaderInterface::load()
     * @param  string $locale
     * @param  string $filename
     * @return TextDomain|null
     * @throws Exception\InvalidArgumentException
     */
    public function load($locale, $filename)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Could not open file %s for reading',
                $filename
            ));
        }

        $messages = json_decode(file_get_contents($filename), true);

        if (!is_array($messages)) {
            if (ipConfig()->isDevelopmentEnvironment()) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Expected an array, but received %s',
                    gettype($messages)
                ));
            } else {
                return null;
            }
        }

        $textDomain = new TextDomain($messages);

        if ($textDomain->offsetExists('')) {
            if (isset($textDomain['']['plural_forms'])) {
                $textDomain->setPluralRule(
                    PluralRule::fromString($textDomain['']['plural_forms'])
                );
            }

            unset($textDomain['']);
        }

        return $textDomain;
    }
}
