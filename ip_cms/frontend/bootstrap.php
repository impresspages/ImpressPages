<?php



    $site->init();
    $site->dispatchEvent('administrator', 'system', 'init', array());
    $dispatcher->notify(new \Ip\Event($site, 'site.afterInit', null));

    /*detect browser language*/
    if((!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == '') && $parametersMod->getValue('standard', 'languages', 'options', 'detect_browser_language') && $site->getCurrentUrl() == BASE_URL && !isset($_SESSION['modules']['standard']['languages']['language_selected_by_browser']) && $parametersMod->getValue('standard', 'languages', 'options', 'multilingual')){
        require_once(BASE_DIR.LIBRARY_DIR.'php/browser_detection/language.php');

        $browserLanguages = Library\Php\BrowserDetection\Language::getLanguages();
        $selectedLanguageId = null;
        foreach($browserLanguages as $browserLanguageKey => $browserLanguage){
            foreach($site->languages as $siteLanguageKey => $siteLanguage){
                if(strpos($browserLanguage, '-') !== false) {
                    $browserLanguage = substr($browserLanguage, 0, strpos($browserLanguage, '-'));
                }
                if(strpos($siteLanguage['code'], '-') !== false) {
                    $siteLanguage['code'] = substr($siteLanguage['code'], 0, strpos($siteLanguage['code'], '-'));
                }

                if($siteLanguage['code'] == $browserLanguage){
                    $selectedLanguageId = $siteLanguage['id'];
                    break;
                }
            }
            if ($selectedLanguageId != null) {
                break;
            }
        }

        if($selectedLanguageId != $site->currentLanguage['id'] && $selectedLanguageId !== null)
            header("location:".$site->generateUrl($selectedLanguageId));
    }
    $_SESSION['modules']['standard']['languages']['language_selected_by_browser'] = true;
    /*eof detect browser language*/

    /*check if the website is closed*/
    if($parametersMod->getValue('standard', 'configuration', 'main_parameters', 'closed_site') && !$site->managementState()
            && (!\Ip\Backend::loggedIn() || !isset($_REQUEST['g']) || !isset($_REQUEST['m']) || !isset($_REQUEST['a']))){
        echo $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'closed_site_message');
        \Db::disconnect();
        exit;
    }
    /*eof check if the website is closed*/

    if(!defined('BACKEND')){
        $session = \Ip\ServiceLocator::getSession();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
            $parametersMod->getValue('standard', 'configuration', 'advanced_options', 'xss_autocheck') &&
            (empty($_POST['securityToken']) || $_POST['securityToken'] !=  $session->getSecurityToken()) &&
            (empty($_POST['pa']) || empty($_POST['m']) || empty($_POST['g']))
        ) {
            $data = array(
                'status' => 'error',
                'errors' => array(
                    'securityToken' => $parametersMod->getValue('developer', 'form', 'error_messages', 'xss')
                )
            );
            header('Content-type: text/json; charset=utf-8'); //throws save file dialog on firefox if iframe is used
            echo json_encode($data);
            \Db::disconnect();
            $dispatcher->notify(new \Ip\Event($site, 'site.databaseDisconnect', null));
            exit;
        }


        $site->makeActions(); //all posts are handled by "site" and redirected to current module actions.php before any output.


        if (!$site->managementState() && !\Modules\standard\design\ConfigModel::instance()->isInPreviewState()) {
            $site->makeRedirect(); //if required;
        }
    }

    if (defined('WORKER')) {

        global $site;
        global $log;

        $site = \Ip\ServiceLocator::getSite();

        $log = new \Modules\administrator\log\Module();

        require_once (BASE_DIR.BACKEND_DIR.'cms.php');
        require_once (BASE_DIR.BACKEND_DIR.'db.php');

        global $cms;
        $cms = new \Backend\Cms();

        $cms->worker();

        \Db::disconnect();
        exit();
    }

    $output = $site->generateOutput();

    $dispatcher->notify(new \Ip\Event($site, 'site.outputGenerated', array('output' => &$output)));
    echo $output;
    $dispatcher->notify(new \Ip\Event($site, 'site.outputPrinted', array('output' => &$output)));


    /*
     Automatic execution of cron.
    The best solution is to setup cron service to launch file www.yoursite.com/ip_cron.php few times a day.
    By default fake cron is enabled
    */
    if(!\Ip\Module\Admin\Model::isSafeMode() && $parametersMod->getValue('standard', 'configuration', 'advanced_options', 'use_fake_cron') && function_exists('curl_init') && $log->lastLogsCount(60, 'system/cron') == 0){
        // create a new curl resource
         
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, BASE_URL.'ip_cron.php');
        curl_setopt($ch, CURLOPT_REFERER, BASE_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        $fakeCronAnswer = curl_exec($ch);
        $dispatcher->notify(new \Ip\Event($site, 'cron.afterFakeCron', $fakeCronAnswer));

    }


    \Db::disconnect();
    $dispatcher->notify(new \Ip\Event($site, 'site.databaseDisconnect', null));




