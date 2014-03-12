<?php
namespace Plugin\ImportExport;


class Service
{

    private $menusForImporting = Array(),
        $languagesForImporting = Array(),
        $importLog = Array();

    public function startImport($uploadedFile)
    {


        $this->addLogRecord('Starting importing the site. '.$uploadedFile->getOriginalFileName(), 'info');

        $extractedDirName = $this->extractZip($uploadedFile);
        $this->importSiteTree($extractedDirName);

        $parentId = 0;
        $recursive = true;

        foreach ($this->languagesForImporting as $language) { // TODO X fix languages

        }

        $languageCode = $language->getCode(); // TODO X fix languages

        try {

            foreach ($this->menusForImporting as $menuItem) {

                $menuName = $menuItem['nameForImporting'];
                $this->addLogRecord('MENU NAME: ' . $menuName, 'info');
                $recursive = true;
                $this->addLogRecord('Processing language: ' . $language->getCode(), 'info');
                $menu = \Ip\Internal\Pages\Service::getMenu($languageCode, $menuName);
                $parentSubPageId = $menu['id'];

                $pageData = array('languageCode' =>  $language->getCode());

                $directory = ipFile(
                    'file/secure/tmp/' . $extractedDirName .'/archive/'. $language->getUrl() . '_' . $menuItem['nameInFile']
                );

                if (is_dir($directory)) {

                    $this->addLogRecord("Processing:" . $directory);

                    $this->addPages($directory, $parentSubPageId, $recursive, $menuName, $language);

                }
            }


        } catch (\Exception $e) {
            $this->addLogRecord("Skipping:" . $e);
        }

        $this->addLogRecord('Finished importing', 'success');
        return true;
    }

    private function importSiteTree($extractedDirName)
    {

        $this->menusForImporting = Array();
        $this->languagesForImporting = Array();

        $string = file_get_contents(ipFile('file/secure/tmp/' . $extractedDirName . '/archive/info.json'));
        $siteData = json_decode($string, true);

        $version = $siteData['version'];

        $this->addLogRecord('Importing version '.$version, 'info');

        $this->importLanguages($siteData['languages']);

        $this->importMenus($siteData['menuLists']);

        return true;
    }

    private function importMenus($menuList){

        foreach ($menuList as $menu) {

            $prefix = 'imported_';
            $suffix = ''; // TODO Add a prefix if page with specific name already exists
//            while (ipContent()->getPage($prefix . $curZoneName . $suffix)) {
//                $suffix = $suffix + 1;
//            }

            $menuName = $prefix . $menu['name'] . $suffix;
            $menuTitle = $menu['title'];
            $menuDescription = $menu['description'];
            $menuUrl = $menu['url'];
            $associatedModule = 'Content';
            $defaultLayout = 'main.php';

            $this->menusForImporting[] = Array(
                'nameInFile' => $menu['name'],
                'nameForImporting' => $menuName,
                'title' => $menuTitle,
                'description' => $menuDescription,
                'url' => $menuUrl,
                'associatedModule' => $associatedModule,
                'layout' => $defaultLayout
            );

            try {

                $menuExists = \Ip\Internal\Pages\Service::getMenu('en', $menuName);
                if (!isset($menuExists['isDeleted']) || ($menuExists['isDeleted'] == '1')){
                    \Ip\Internal\Pages\Service::createMenu('en', $menuName, $menuTitle);
                }else{
                    $this->addLogRecord('Menu '.$menuName.' already exists. Importing anyway.', 'error');
                }

            } catch (\Exception $e) {
                throw new \Exception($e);
            }

        }


        return true;

    }

    private function importLanguages($languageList)
    {

        foreach ($languageList as $language){
            if (!Model::languageExists($language['url'])){

                $languageId = ipContent()->addLanguage($language['d_long'], $language['d_short'], $language['code'], $language['url'], true);

//                \Ip\Module\Pages\Service::addLanguage($language['code'], $language['url'], $language['d_long'], $language['d_short'], false);

            }else{
                $languageId = Model::getLanguageIdByUrl($language['url']);
            }
            //TODO



            $this->languagesForImporting[] = ipContent()->getLanguage($languageId);;
        }

        return true;
    }

    private function extractZip($file)
    {
        $extractSubDir = false;

        $fileName = $file->getOriginalFileName();

        try {
            $zipLib = ipFile('Plugin/ImportExport/lib/pclzip.lib.php');
            require_once($zipLib);

            $archive = new \PclZip(ipFile('file/secure/tmp/' . $fileName));

            $zipNameNoExt = basename($fileName, '.zip');
            $extractSubDir = $zipNameNoExt;
            $count = 0;
            while (is_file(ipFile('file/secure/tmp/' . $extractSubDir)) || is_dir(
                    ipFile('file/secure/tmp/' . $extractSubDir)
                )) {
                $count++;
                $extractSubDir = $zipNameNoExt . '_' . $count;
            }

            if ($archive->extract(
                    PCLZIP_OPT_PATH,
                    ipFile('file/secure/tmp'),
                    PCLZIP_OPT_ADD_PATH,
                    $extractSubDir
                ) == 0
            ) {
                die("Error : " . $archive->errorInfo(true));
            }
        } catch (\Exception $e) {
            $this->addLogRecord($e);
        }
        return $extractSubDir;
    }

    private function importWidgets($fileName, $pageId, $menuName, $language)
    {

        $pageRevision = \Ip\Internal\Revision::getLastRevision($pageId);
        $revisionId = $pageRevision['revisionId'];

        $languageId = $language->getId();
        $languageDir = $language->getUrl();

        $this->addLogRecord('Importing widgets from '.$fileName, 'info');

        $buttonTitle = basename($fileName, ".json");
        $url = $buttonTitle;


        $string = file_get_contents($fileName);

        $position = 0;

        $pageData = json_decode($string, true);



        if (isset($pageData['widgets'])) {

            $widgetList = $pageData['widgets'];

            foreach ($widgetList as $widgetKey => $widgetValue) {

                $blockId = 'main'; // TODO X Allow to import all blocks, not only main

                if (isset($widgetValue['type'])) {
                    $widgetName = $widgetValue['type'];

                    //TODO Testing
                    $processWidget = false;

                    if (isset($widgetValue['layout'])){
                        $layout =  $widgetValue['layout'];
                    }else{
                        $layout =  'default';
                    }

                    if (isset($widgetValue['data'])){

                        $widgetData = $widgetValue['data'];

                    }

                    switch ($widgetName) {
                        case 'Separator':
                            $content = null;
                            $processWidget = true;
                            break;
                        case 'Table':
                            $content['text'] = $widgetData['text'];
                            $processWidget = true;
                            break;
                        case 'Text':
                            $content['text'] = $widgetData['text'];
                            $processWidget = true;
                            break;
                        case 'TextImage': //  IpTextImage as IpText
                            $widgetName = 'IpText';
                            $content['text'] = $widgetData['text'];
                            $processWidget = true;
                            break;
                        case 'Title':
                            $content['title'] = $widgetData['title'];
                            $processWidget = true;
                            break;
                        case 'Html':
                            $content['html'] = $widgetData['html'];
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

                        $widgetId = \Ip\Internal\Content\Service::createWidget($widgetName, $widgetData);
                        \Ip\Internal\Content\Service::addWidgetInstance($widgetId, $revisionId, 0, $blockId, $position);

//                        $instanceId = \Ip\Internal\Content\Service::addWidget(
//                            $widgetName,
//                            $zoneName,
//                            $pageId,
//                            $blockId,
//                            $revisionId,
//                            $position
//                        );
//



                        // \Ip\Internal\Revision::getLastRevision($pageId)
                        // createWidget
                        // addWidgetInstance($widgetId, $revisionId, $languageId, $block, $position, $visible = true)
//                        \Ip\Module\Content\Service::addWidgetContent($instanceId, $content, $layout);
                        $this->addLogRecord('Widget ' . $widgetName . " added. File name: ".$fileName.", Menu name: ".$menuName. ", Language: ".$languageDir, 'danger');
                    } else {
                        $this->addLogRecord('ERROR: Widget ' . $widgetName . " not supported. File name: ".$fileName.", Zone name: ".$menuName. ", Language: ".$languageDir, 'danger');
                    }
                }

            }


        }
    }

    private function addPages($directory, $parentId, $recursive, $menuName, $language)
    {

        $array_items = array();


        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_dir($directory . "/" . $file)) {

                        if ($recursive) {
                            $pageFileNamePath = $directory . "/" . $file . ".json";
                            if (is_file($pageFileNamePath)) {
                                $string = file_get_contents($pageFileNamePath);
                                $pageData = json_decode($string, true);

                                $pageSettings = $pageData['settings'];
                                $buttonTitle = $pageSettings['button_title'];
                                $pageTitle = $pageSettings['page_title'];
                                $url = $pageSettings['url'];

//                                $pageId = \Ip\Module\Content\Service::addPage(
//                                    $zoneName,
//                                    $parentId,
//                                    $buttonTitle,
//                                    $pageTitle,
//                                    $url
//                                );
                                $pageData = array('languageCode' =>  $language->getCode(),
                                                'urlPath' => esc($url),
                                                'metaTitle' => esc($pageTitle),
                                );

                                $pageId = ipContent()->addPage($parentId, $buttonTitle, $pageData);


                                $this->addPages($directory . "/" . $file, $pageId, $recursive, $menuName, $language);
                            }else{
                                $this->addLogRecord('ERROR: File ' . $pageFileNamePath . " does not exist. Menu name: ".$menuName);
                            }
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


//                            $pageId = \Ip\Module\Content\Service::addPage(
//                                $zoneName,
//                                $parentId,
//                                $buttonTitle,
//                                $pageTitle,
//                                $url
//                            );

                            $pageData = array('languageCode' =>  $language->getCode(),
                                                'urlPath' =>  esc($url),
                                                'metaTitle' => esc($pageTitle),
                            );

                            $pageId = ipContent()->addPage($parentId, $buttonTitle, $pageData);
                            $this->importWidgets($fileFullPath, $pageId, $menuName, $language);


                        }
                    }
                }
            }
            closedir($handle);
        }
        return $array_items;
    }

    private function addLogRecord($msg, $status = 'warning')
    {
        $this->importLog[] = Array('message' => $msg, 'status' => $status);

    }

    public function getImportLog()
    {
        return $this->importLog;
    }
}