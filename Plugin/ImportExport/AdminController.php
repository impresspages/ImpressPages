<?php
namespace Plugin\importExport;


class AdminController extends \Ip\Controller
{
    public function index()
    {

        $this->extractZip();
        $this->importZones();



        $zoneData['id'] = 111;
        $zoneData['name'] = 'TestName';
        $zoneData['template'] = 'testTemplate';
        $zoneData['title'] = 'Title';
        $zoneData['url'] = 'URL';
        $zoneData['keywords'] = '';
        $zoneData['description'] = 'Test description';

        ipAddPluginAsset('ImportExport', 'importExport.js');


        try {

            $zones = \Ip\Module\Pages\Db::getZones();

            foreach ($zones as $zone){


                $zoneName = $zone['name'];

                $zone = ipContent()->getZone($zoneName);



                $languages = \Ip\Module\Pages\Db::getLanguages();

                foreach ($languages as $key => $language) {
                    $language_id = $language['id'];
                    $parentPageId = \Ip\Module\Pages\Db::rootContentElement($zone->getId(), $language_id);


                    if ($parentPageId === false) {
                        trigger_error("Can't find root zone element.");

                        return false;
                    }

print_r($language);



                    $parentPage = $zone->getPage($parentPageId);

                    $pageId = \Ip\Module\Content\Service::addPage($zoneName, $parentPageId);
                    $revisionId = \Ip\Revision::createRevision($zoneName, $pageId, true);

                    $widgetName = 'IpText';
                    $content['text'] = '<p>Demushke</p>';

                    $instanceId = \Ip\Module\Content\Service::addWidget(
                        $widgetName,
                        $zoneName,
                        $pageId,
                        'main',
                        $revisionId,
                        null
                    );
                    \Ip\Module\Content\Service::addWidgetContent($instanceId, $content, $layout = 'default');

                    $this->importWidgets($language['d_short']);

                }
            }
        } catch (\Exception $e) {
            echo $e;
        }

        return 'test';
    }


    private function importZones()
    {

        $string = file_get_contents( ipConfig()->fileDirFile('ImportExport/archive/zones.json'));
        $json_a = json_decode($string, true);
        foreach ($json_a as $zone) {
            $zoneName = $zone['name'];
            $zoneTitle = $zone['title'];
            $zoneDescription = $zone['description'];
            $zoneUrl = $zone['url'];
            $associatedModule = 'Content';
            $defaultLayout = 'main.php';

            \Ip\Module\Pages\Service::addZone($zoneTitle, $zoneName, $associatedModule, $defaultLayout, null, $zoneDescription, $zoneUrl );

            $zone = ipContent()->getZone($zoneName);

        }
        return true;
    }

    private function extractZip()
    {

        try {
            $zipLib = ipConfig()->pluginFile('ImportExport/lib/pclzip.lib.php');
            require_once($zipLib);

            $archive = new \PclZip(ipConfig()->fileDirFile('ImportExport/archive.zip'));

            if ($archive->extract(PCLZIP_OPT_PATH, ipConfig()->fileDirFile('ImportExport')) == 0) {
                die("Error : " . $archive->errorInfo(true));
            }

        } catch (\Exception $e) {
            echo $e;
        }
    }

    private function importWidgets($languageDir){
        $path = realpath(ipConfig()->fileDirFile('ImportExport'));

        $Regex = new RegexIterator($Iterator, '/^.+\.json$/i', RecursiveRegexIterator::GET_MATCH);

        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
        foreach($objects as $name => $object){
            $string = file_get_contents( $name);
            $json_a = json_decode($string, true);
            print $name."<Br>";
        }
    }


}