<?php
namespace Plugin\importExport;


use Ip\Form\Exception;

class AdminController extends \Ip\Controller
{

    private $zonesForImporting = Array(),
            $importLog = Array();

    public function index()
    {

        $form = new \Ip\Form();

        if (isset($_REQUEST['startImport'])) {

            switch ($_REQUEST['startImport']) {
                case 'import':
                    ipAddPluginAsset('ImportExport', 'importExport.js');
                    $this->startImport();
                    return new \Ip\Response\Json($this->importLog);
                default:
                    return $this->showWaitMessage();
            }
        }

        $field = new \Ip\Form\Field\Submit(
            array(
                'name' => 'submit', //html "name" attribute
                'label' => 'submit', //field label that will be displayed next to input field
                'defaultValue' => 'Import site widget content from file'
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\File(
            array(
                'name' => 'siteFile', //html "name" attribute
                'label' => 'ZIP file:', //field label that will be displayed next to input field
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'startImport',
                'defaultValue' => 'import'
            )
        );

        $form->addField($field);
        $formHtml = $form->render();

        return $formHtml;

    }

    private function showWaitMessage()
    {
        $data = Array();
        $renderedHtml = \Ip\View::create('View/view_wait.php', $data)->render();
        return $renderedHtml;
    }

    private function startImport()
    {
        $this->extractZip();
        $this->importZones();


        $zones = ipContent()->getZones();

        $parentId = 0;
        $recursive = true;


        try {

            foreach ($this->zonesForImporting as $zone) {

                $zoneName = $zone['nameForImporting'];

                $this->addLogRecord('ZONE NAME:' . $zoneName);

                $zoneObj = ipContent()->getZone($zoneName);

                $zoneId = $zoneObj->getId();

                $recursive = true;

                $languages = \Ip\Module\Pages\Db::getLanguages();


                foreach ($languages as $key => $language) {

                    $language_id = $language['id'];

                    $directory = ipConfig()->fileDirFile(
                        'ImportExport/archive/' . $language['url'] . '_' . $language_id . '/' . $zone['nameInFile']
                    );



                    if (file_exists($directory) || is_dir($directory)) {

                        $this->addLogRecord("<br>Processing:" . $directory);

                        $parentPageId = \Ip\Module\Pages\Db::rootContentElement($zoneId, $language_id);

                        $this->addZonePages($directory, $parentPageId, $recursive, $zoneName, $language);


                    } else {
                        $this->addLogRecord("Skipping:" . $directory);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->addLogRecord("Skipping:" . $e);
        }

        $this->addLogRecord('Finished importing');
        return true;
    }


    private function importZones()
    {

        $this->zonesForImporting = Array();

        $string = file_get_contents(ipConfig()->fileDirFile('ImportExport/archive/zones.json'));
        $zoneList = json_decode($string, true);

        foreach ($zoneList as $zone) {

            $curZoneName = $zone['name'];
            $prefix = 'imported_';
            $suffix = '';
            while (ipContent()->getZone($prefix . $curZoneName . $suffix)) {
                $suffix = $suffix + 1;
            }

            $zoneName = $prefix . $zone['name'] . $suffix;
            $zoneTitle = $zone['title'];
            $zoneDescription = $zone['description'];
            $zoneUrl = $zone['url'];
            $associatedModule = 'Content';
            $defaultLayout = 'main.php';

            $this->zonesForImporting[] = Array(
                'nameInFile' => $zone['name'],
                'nameForImporting' => $zoneName,
                'title' => $zoneTitle,
                'description' => $zoneDescription,
                'url' => $zoneUrl,
                'associatedModule' => $associatedModule,
                'layout' => $defaultLayout
            );


            try {
                \Ip\Module\Pages\Service::addZone(
                    $zoneTitle,
                    $zoneName,
                    $associatedModule,
                    $defaultLayout,
                    null,
                    $zoneDescription,
                    $zoneUrl
                );
            } catch (\Exception $e) {
                throw new \Exception($e);
            }

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
            $this->addLogRecord($e);
        }
    }

    private function importWidgets($fileName, $pageId, $zoneName, $language)
    {

        $language_id = $language['id'];
        $languageDir = $language['url'];


        $zone = ipContent()->getZone($zoneName);

        $parentPageId = \Ip\Module\Pages\Db::rootContentElement($zone->getId(), $language_id);


        if ($parentPageId === false) {
            trigger_error("Can't find root zone element.");

            return false;
        }

        $parentPage = $zone->getPage($parentPageId);


        //TODO get page data from JSON
        $buttonTitle = basename($fileName, ".json");
        $url = $buttonTitle;

        $revisionId = \Ip\Revision::createRevision($zoneName, $pageId, true);

        $string = file_get_contents($fileName);

        $position = 0;

        $pageData = json_decode($string, true);

        if (isset($pageData['widgets'])) {

            $widgetData = $pageData['widgets'];

            foreach ($widgetData as $widgetKey => $widgetValue) {

                $blockId = 'main';

                if (isset($widgetValue['type'])) {
                    $widgetName = $widgetValue['type'];

                    //TODO Testing
                    $processWidget = false;

                    $layout = 'default';

                    switch ($widgetName) {
                        case 'IpSeparator':
                            $content = null;
                            $processWidget = true;
                            break;
                        case 'IpTable':
                            $content['text'] = $widgetValue['text'];
                            $processWidget = true;
                            break;
                        case 'IpText':
                            $content['text'] = $widgetValue['text'];
                            $processWidget = true;
                            break;
                        case 'IpTextImage': // Import IpTextImage as IpText
                            $widgetName = 'IpText';
                            $content['text'] = $widgetValue['text'];
                            $processWidget = true;
                            break;
                        case 'IpTitle':
                            $content['title'] = $widgetValue['title'];
                            $processWidget = true;
                            break;
                        case 'IpHtml':
                            $content['html'] = $widgetValue['html'];
                            if (!isset($widgetValue['layout'])) {
                                $layout = 'escape'; // default layout for code examples
                            }

                            $processWidget = true;
                            break;
                        default:
                            $content = null;
                            break;
                    }

                    if ($processWidget) {
                        $position++;
                        $instanceId = \Ip\Module\Content\Service::addWidget(
                            $widgetName,
                            $zoneName,
                            $pageId,
                            $blockId,
                            $revisionId,
                            $position
                        );

                        \Ip\Module\Content\Service::addWidgetContent($instanceId, $content, $layout);

                    } else {
                        $this->addLogRecord('ERROR:' . $widgetName . " not supported");
                    }
                }

            }


        }
    }


    private function addZonePages($directory, $parentId, $recursive, $zoneName, $language)
    {
        $array_items = array();
        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_dir($directory . "/" . $file)) {

                        if ($recursive) {

                            $string = file_get_contents($directory . "/" . $file . ".json");
                            $pageData = json_decode($string, true);

                            $pageSettings = $pageData['settings'];
                            $buttonTitle = $pageSettings['button_title'];
                            $pageTitle = $pageSettings['page_title'];
                            $url = $pageSettings['url'];

                            $pageId = \Ip\Module\Content\Service::addPage(
                                $zoneName,
                                $parentId,
                                $buttonTitle,
                                $pageTitle,
                                $url
                            );
                            $this->addZonePages($directory . "/" . $file, $pageId, $recursive, $zoneName, $language);
                        }

                    } else {
                        $fileFullPath = $directory . "/" . $file;
                        if (!is_dir(preg_replace("/\\.[^.\\s]{3,4}$/", "", $fileFullPath))) {
                            $string = file_get_contents($fileFullPath);

                            $pageData = json_decode($string, true);
                            $pageSettings = $pageData['settings'];
                            $buttonTitle = $pageSettings['button_title'];
                            $pageTitle = $pageSettings['page_title'];
                            $url = $pageSettings['url'];


                            $pageId = \Ip\Module\Content\Service::addPage(
                                $zoneName,
                                $parentId,
                                $buttonTitle,
                                $pageTitle,
                                $url
                            );
                            $this->importWidgets($fileFullPath, $pageId, $zoneName, $language);
                        }
                    }
                }
            }
            closedir($handle);
        }
        return $array_items;
    }

    private function addLogRecord($msg)
    {
        $this->importLog[] = $msg;
    }



}