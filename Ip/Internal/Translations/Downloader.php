<?php


namespace Ip\Internal\Translations;


class Downloader
{
    public function downloadTranslation($namespace, $languageCode, $version)
    {
        $filename = "{$namespace}-{$languageCode}.json";

        $url = "http://download.impresspages.org/translations/{$filename}?v={$version}";

        $netHelper = new \Ip\Internal\NetHelper();

        try {
            $json = $netHelper->fetchUrl($url);
        } catch (\Ip\Exception $e) {
            return false;
        }

        if (!is_string($json) || !json_decode($json)) {
            return false;
        }

        file_put_contents(ipFile("file/translations/original/{$filename}"), $json);

        return true;
    }
} 
