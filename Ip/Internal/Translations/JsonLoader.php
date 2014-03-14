<?php

namespace Ip\Internal\Translations;

use Zend\I18n\Translator\Loader\FileLoaderInterface;
use Zend\I18n\Exception;
use Zend\I18n\Translator\Plural\Rule as PluralRule;
use Zend\I18n\Translator\TextDomain;

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

        if (array_key_exists('', $textDomain)) {
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
