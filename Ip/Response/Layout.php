<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Response;

/**
 *
 * Event dispatcher class
 *
 */
class Layout extends \Ip\Response {

    const DOCTYPE_XHTML1_STRICT = 1;
    const DOCTYPE_XHTML1_TRANSITIONAL = 2;
    const DOCTYPE_XHTML1_FRAMESET = 3;
    const DOCTYPE_HTML4_STRICT = 4;
    const DOCTYPE_HTML4_TRANSITIONAL = 5;
    const DOCTYPE_HTML4_FRAMESET = 6;
    const DOCTYPE_HTML5 = 7;


    protected $layout = null;

    /** array js variables */
    private $javascriptVariables = array();

    /** array required javascript files */
    private $requiredJavascript = array();

    /** array required css files */
    private $requiredCss = array();

    private $layoutVariables = array();

    private $title;
    private $keywords;
    private $description;
    private $favicon;
    private $charset;


    public function __construct($content = NULL, $headers = NULL, $statusCode = NULL)
    {
        $this->setFavicon(ipFileUrl('favicon.ico'));
        $this->setCharset(ipConfig()->getRaw('CHARSET'));
        parent::__construct($content = NULL, $headers = NULL, $statusCode = NULL);
    }


    public function send()
    {
        ipContent()->setBlockContent('main', $this->content);


        if ($this->getLayout() === null) {
            $this->chooseLayout();
        }
        $layout = $this->getLayout();

        if ($layout[0] == '/' || $layout[1] == ':') { // Check if absolute path: '/' for unix, 'C:' for windows
            $viewFile = $layout;
        } else {
            $viewFile = ipThemeFile($layout);
        }

        $this->setContent(ipView($viewFile, $this->getLayoutVariables())->render());

        parent::send();
    }

    protected function chooseLayout()
    {
        if (\Ip\ServiceLocator::request()->getControllerType() == \Ip\Request::CONTROLLER_TYPE_ADMIN) {
            $this->layout = ipFile('Ip/Internal/Admin/view/layout.php');
            $this->addJavascript(ipFileUrl('Ip/Internal/Ip/assets/bootstrap/bootstrap.js'));
        } elseif (\Ip\Internal\Admin\Model::isSafeMode()) {
            $this->layout = '/Admin/view/safeModeLayout.php';
        } else {
            $this->layout = 'main.php';
        }
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function addCss($file, $attributes = array(), $stage = 1, $cacheFix = true) {
        $this->requiredCss[(int)$stage][$file] = array (
            'value' => $file,
            'attributes' => $attributes,
            'cacheFix' => $cacheFix
        );
    }

    public function removeCss($file) {
        foreach($this->requiredCss as $levelKey => &$level) {
            if (isset($this->requiredCss[$levelKey][$file])) {
                unset($this->requiredCss[$levelKey][$file]);
            }
        }
    }

    public function getCss() {
        ksort($this->requiredCss);
        $cssFiles = array();
        foreach($this->requiredCss as $level) {
            $cssFiles = array_merge($cssFiles, $level);
        }
        return $cssFiles;
    }

    public function addJavascriptContent($key, $javascript, $stage = 1) {
        $this->requiredJavascript[(int)$stage][$key] = array (
            'type' => 'content',
            'value' => $javascript
        );
    }


    public function addJavascript($file, $attributes = array(), $stage = 1, $cacheFix = true) {
        $this->requiredJavascript[(int)$stage][$file] = array (
            'type' => 'file',
            'value' => $file,
            'attributes' => $attributes,
            'cacheFix' => $cacheFix
        );
    }

    public function removeJavascript($file) {
        foreach($this->requiredJavascript as $levelKey => &$level) {
            if (isset($this->requiredJavascript[$levelKey][$file]) && $this->requiredJavascript[$levelKey][$file]['type'] == 'file') {
                unset($this->requiredJavascript[$levelKey][$file]);
            }
        }
    }



    public function removeJavascriptContent($key) {
        foreach($this->requiredJavascript as $levelKey => &$level) {
            if (isset($this->requiredJavascript[$levelKey][$key]) && $this->requiredJavascript[$levelKey][$key]['type'] == 'content') {
                unset($this->requiredJavascript[$levelKey][$key]);
            }
        }
    }

    public function getJavascript() {
        ksort($this->requiredJavascript);
        return $this->requiredJavascript;
    }

    public function addJavascriptVariable($name, $value) {
        $this->javascriptVariables[$name] = $value;
    }

    public function removeJavascriptVariable($name) {
        if (isset($this->javascriptVariables[$name])) {
            unset($this->javascriptVariables[$name]);
        }
    }

    public function getJavascriptVariables() {
        return $this->javascriptVariables;
    }

    public function generateHead() {
        $cacheVersion = \Ip\ServiceLocator::storage()->get('Ip', 'cacheVersion', 1);
        $cssFiles = $this->getCss();

        $inDesignPreview = false;

        $data = ipRequest()->getRequest();

        if (!empty($data['ipDesign']['pCfg']) && (defined('IP_ALLOW_PUBLIC_THEME_CONFIG') || isset($_REQUEST['ipDesignPreview']))) {
            $config = \Ip\Internal\Design\ConfigModel::instance();
            $inDesignPreview = $config->isInPreviewState();
        }

        if ($inDesignPreview) {
            foreach($cssFiles as &$file) {
                $path = pathinfo($file['value']);
                if ($path['dirname'] . '/' == ipThemeFile('') && file_exists(ipThemeFile($path['filename'] . '.less'))) {
                    $designService = \Ip\Internal\Design\Service::instance();
                    $file = $designService->getRealTimeUrl(ipConfig()->theme(), $path['filename']);
                } else {
                    if ($file['cacheFix']) {
                        $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
                    }
                }
            }
        } else {
            foreach($cssFiles as &$file) {
                if ($file['cacheFix']) {
                    $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
                }
            }
        }


        $data = array (
            'title' => $this->getTitle(),
            'keywords' => $this->getKeywords(),
            'description' => $this->getDescription(),
            'favicon' => $this->getFavicon(),
            'charset' => $this->getCharset(),
            'css' => $cssFiles
        );

        return ipView(ipFile('Ip/Internal/Config/view/head.php'), $data)->render();
    }

    public function generateJavascript() {
        $cacheVersion = \Ip\ServiceLocator::storage()->get('Ip', 'cacheVersion', 1);
        $javascriptFiles = $this->getJavascript();
        foreach($javascriptFiles as &$level) {
            foreach($level as &$file) {
                if ($file['type'] == 'file' && $file['cacheFix']) {
                    $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
                }
            }
        }
        $revision = \Ip\ServiceLocator::content()->getCurrentRevision();
        $data = array (
            'ip' => array(
                'baseUrl' => ipConfig()->baseUrl(),
                'languageId' => ipContent()->getCurrentLanguage()->getId(),
                'languageUrl' => \Ip\Internal\Deprecated\Url::generate(),
                'theme' => ipConfig()->getRaw('THEME'),
                'zoneName' => ipContent()->getCurrentZone() ? ipContent()->getCurrentZone()->getName() : null,
                'pageId' => ipContent()->getCurrentPage() ? ipContent()->getCurrentPage()->getId() : null,
                'revisionId' => $revision['revisionId'],
                'securityToken' => \Ip\ServiceLocator::application()->getSecurityToken(),
                'developmentEnvironment' => ipConfig()->isDevelopmentEnvironment(),
                'debugMode' => ipconfig()->isDebugMode()
            ),
            'javascriptVariables' => $this->getJavascriptVariables(),
            'javascript' => $javascriptFiles,
        );
        return ipView(ipFile('Ip/Internal/Config/view/javascript.php'), $data)->render();
    }


    public function setLayoutVariable($name, $value)
    {
        $this->layoutVariables[$name] = $value;
    }

    public function getLayoutVariables()
    {
        return $this->layoutVariables;
    }

    public function getTitle(){
        return $this->title;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function getKeywords(){
        return $this->keywords;
    }

    public function setKeywords($keywords){
        $this->keywords = $keywords;
    }

    public function getDescription(){
        return $this->description;
    }

    public function setDescription($description){
        $this->description = $description;
    }

    public function getFavicon(){
        return $this->favicon;
    }

    public function setFavicon($favicon){
        $this->favicon = $favicon;
    }

    public function getCharset(){
        return $this->charset;
    }

    public function setCharset($charset){
        $this->charset = $charset;
    }

}