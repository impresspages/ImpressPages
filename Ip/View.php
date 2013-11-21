<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;

/**
 *
 * View class
 *
 */
class View implements \Ip\Response\ResponseInterface
{

    const DOCTYPE_XHTML1_STRICT = 1;
    const DOCTYPE_XHTML1_TRANSITIONAL = 2;
    const DOCTYPE_XHTML1_FRAMESET = 3;
    const DOCTYPE_HTML4_STRICT = 4;
    const DOCTYPE_HTML4_TRANSITIONAL = 5;
    const DOCTYPE_HTML4_FRAMESET = 6;
    const DOCTYPE_HTML5 = 7;
    
        
    private $file;
    private $data;
    private $doctype;
    private $languageId;

    /**
     * Construct view object using specified file and date
     * @param string $file path to view file. Relative to file where this constructor is executed from.
     * @param array $data array of data to pass to view
     * @param int $languageId language in which to render the view. Current language by default
     */
    public static function create($file, $data = array(), $languageId = null) {
        $foundFile = self::findView($file);
        self::checkData($data);
        return new \Ip\View($foundFile, $data, $languageId = null);
    }



    /**
     * Construct view object using specified file and date
     * @internal
     * @param string $file path to view file. Relative to file where this constructor is executed from.
     * @param array $data array of data to pass to view
     * @param int $languageId language in which to render the view. Current language by default
     */
    private function __construct($file, $data = array(), $languageId = null) {
        $content = \Ip\ServiceLocator::content();
        $this->file = $file;
        $this->data = $data;
        if ($languageId == null) {
            $this->languageId = $content->getCurrentLanguage()->getId();
        } else {
            $this->languageId = $languageId;
        }
        eval('$this->doctype = self::'. ipConfig()->getRaw('DEFAULT_DOCTYPE').';');
    }
    
    /**
     * 
     * Create new view object with the same doctype, but different view file and data
     * Use it to include another view file within the view
     * @param string $file path to view file relative to current view
     * @param array $data associative array of data to pass to the view
     */
    public function subview($file, $data = array()) {
        $foundFile = self::findView($file);
        self::checkData($data);
        $view = new \Ip\View($foundFile, $data);
        $view->setDoctype($this->getDoctype());
        return $view;
    }


    public function renderWidget($widgetName, $data = array(), $layout = null) {
        $answer = \Ip\Module\Content\Model::generateWidgetPreviewFromStaticData($widgetName, $data, $layout);
        return $answer;
    }
    
    /**
     * Escape and echo text
     * @param string $text
     */
    //TODOX remove. use ipEsc()
    public function esc($text, $variables = null){
        if (!empty($variables) && is_array($variables)) {
            foreach($variables as $variableKey => $variableValue) {
                $text = str_replace('[[' . $variableKey . ']]', $variableValue, $text);
            }
            
        }
        return htmlspecialchars($text, ENT_QUOTES);
    }
    
    /**
     * Escape and echo parameter
     * @param string $parameterKey
     */    
    public function escPar($parameterKey, $variables = null){
        //TODOX remove all instances
        return $this->esc($this->par($parameterKey), $variables);
    }

    public function par($parameterKey, $variables = null){
        return $parameterKey; //TODOX remove all instances
        global $parametersMod;
        $parts = explode('/', $parameterKey);
        if (count($parts) != 4) {
            if (ipConfig()->isDevelopmentEnvironment()) {
                throw new \Ip\CoreException("Can't find parameter: '" . $parameterKey . "'", \Ip\CoreException::VIEW);
            } else {
                return '';
            }
        }
        $value = $parametersMod->getValue($parts[0], $parts[1], $parts[2], $parts[3], $this->languageId);

        if (!empty($variables) && is_array($variables)) {
            foreach($variables as $variableKey => $variableValue) {
                $value = str_replace('[[' . $variableKey . ']]', $variableValue, $value);
            }
        }
        
        return $value;
    }



    /**
     * set view variables
     * @param $variables
     */
    public function setVariables($variables)
    {
        $this->data = $variables;
    }

    /**
     * get view variables
     * @return array
     */
    public function getVariables()
    {
        return $this->data;
    }

    /**
     * 
     * Set view data
     * @param array $data
     * @deprecated
     */
    public function setData($data) {
        $this->setVariables($data);
    }

    /**
     * @return array
     * @deprecated
     */
    public function getData() {
        return $this->getVariables();
    }

    public function setVariable($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function unsetVariable($name)
    {
        unset($this->data[$name]);
    }

    public function getVariable($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return null;
    }


    public function render () {


        extract($this->data);

        ob_start();

        require ($this->file);      //file existance has been checked in constructor

        $output = ob_get_contents();
        ob_end_clean();

        return $output;

    }

    public function send()
    {
        echo $this->render();
    }

    /**
     * PHP can't handle exceptions in __toString method. Try to avoid it every time possible. Use render() method instead.
     * @return string
     */
    public function __toString()
    {
        try {
        $content = $this->render();
        } catch (\Exception $e) {
            /*
            __toString method can't throw exceptions. In case of exception you will end with unclear error message.
            We can't avoid that here. So just logging clear error message in logs and rethrowing the same exception.
            */
            ipLog()->error('View.toStringException: Exception in View::__toString() method.', array('exception' => $e, 'view' => $this->file));

            if (ipConfig()->isDevelopmentEnvironment()) {
                return "<pre class=\"error\">\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n</pre>";
            } else {
                // TODOX return appropriate string
                throw $e;
            }
        }

        return $content;
    }

    public function setDoctype ($doctype) {
        $this->doctype = $doctype;
    }
    
    public function getDoctype () {
        return $this->doctype;
    }
    
    public function setLanguageId ($languageId) {
        $this->languageId = $languageId;
    }
    
    public function getLanguageId () {
        return $this->languageId;
    }    
    
    public function doctypeDeclaration($doctype = null) {
        if ($doctype === null) {
            $doctype = $this->getDoctype();
        }
        switch ($doctype) {
            case self::DOCTYPE_XHTML1_STRICT:
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
                break;   
            case self::DOCTYPE_XHTML1_TRANSITIONAL:
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
                break;
            case self::DOCTYPE_XHTML1_FRAMESET:
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
                break;
            case self::DOCTYPE_HTML4_STRICT:
                return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
                break;
            case self::DOCTYPE_HTML4_TRANSITIONAL:
                return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
                break;
            case self::DOCTYPE_HTML4_FRAMESET:
                return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
                break;
            case self::DOCTYPE_HTML5:
                return '<!DOCTYPE html>';
                break;
            default:
                throw new CoreException('Unknown doctype: '.$doctype, CoreException::VIEW);
        }
    }
    
    
    public function htmlAttributes($doctype = null) {
        $content = \Ip\ServiceLocator::content();
        if ($doctype === null) {
            $doctype = $this->getDoctype();
        }
        switch ($doctype) {
            case self::DOCTYPE_XHTML1_STRICT:
            case self::DOCTYPE_XHTML1_TRANSITIONAL:
            case self::DOCTYPE_XHTML1_FRAMESET:
                $lang = $content->getCurrentLanguage()->getCode();
                return ' xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$lang.'" lang="'.$lang.'"';
                break;
            case self::DOCTYPE_HTML4_STRICT:
            case self::DOCTYPE_HTML4_TRANSITIONAL:
            case self::DOCTYPE_HTML4_FRAMESET:
            default:
                return '';
                break;
            case self::DOCTYPE_HTML5:
                $lang = $content->getCurrentLanguage()->getCode();
                return ' lang="'.$lang.'"';
                break;
        }        
       
    }

    /**
     * @param $file
     * @return bool|string
     * @throws CoreException
     */
    private static function findView($file) {
        $backtrace = debug_backtrace();
        if(!isset($backtrace[1]['file']) || !isset($backtrace[1]['line'])) {
            throw new CoreException("Can't find caller", CoreException::VIEW);
        }

        $sourceFile = $backtrace[1]['file'];
        if (DIRECTORY_SEPARATOR != '/') {
            $sourceFile = str_replace(DIRECTORY_SEPARATOR, '/', $sourceFile);
        }


        $foundFile = self::findFile($file, $sourceFile);
        if ($foundFile === false) {
            throw new CoreException('Can\'t find view file \''.$file. '\' (Error source: '.$backtrace[1]['file'].' line: '.$backtrace[1]['line'].' )', CoreException::VIEW);
        }    
        return $foundFile;
    }
    
    private static function findFile($file, $sourceFile) {
        if (strpos($file, ipConfig()->baseFile('')) !== 0) {
            $file = dirname($sourceFile).'/'.$file;
        }


        $moduleView = ''; //relative link to view according to modules root.


        // TODOX Plugin dir

        if ($moduleView != '') {
            // TODOX override module views according to new structure
//            if (file_exists(ipConfig()->themeFile('modules/'.$moduleView))) {
//                return ipConfig()->themeFile('modules/'.$moduleView);
//            }

            // TODOX Plugin dir



        } else {
            if (file_exists($file)) {
                return $file;
            } else {
                return false;
            }
        }

        return false;
    }


    public function generateBlock($blockName)
    {
        return \Ip\ServiceLocator::content()->generateBlock($blockName);
    }

    public function generateSlot($name)
    {
        return ipSlot($name);
    }

    public function generateManagedString($key, $tag = 'span', $defaultValue = null, $cssClass = null)
    {
        $inlineManagementService = new \Ip\Module\InlineManagement\Service();
        return $inlineManagementService->generateManagedString($key, $tag, $defaultValue, $cssClass);
    }

    public function generateManagedText($key, $tag = 'div', $defaultValue = null, $cssClass = null)
    {
        $inlineManagementService = new \Ip\Module\InlineManagement\Service();
        return $inlineManagementService->generateManagedText($key, $tag, $defaultValue, $cssClass);
    }

    public function generateManagedImage($key, $defaultValue = null, $options = array(), $cssClass = null)
    {
        $inlineManagementService = new \Ip\Module\InlineManagement\Service();
        return $inlineManagementService->generateManagedImage($key, $defaultValue, $options, $cssClass);
    }


    /**
     * @param int $price in cents
     * @param string $currency three letter currency code
     * @return string
     */
    public function formatPrice($price, $currency)
    {
        $helper = \Library\Php\Ecommerce\Helper::instance();
        return $helper->formatPrice($price, $currency, $this->getLanguageId());
    }


    /**
     * @param string $menuKey any unique string that identifies this menu within this theme.
     * @param string | \Ip\Menu\Item[] $items zone name as string or array of menu items
     * @param string $viewFile absolute or relative
     */
    public function generateMenu($menuKey, $items, $viewFile = null)
    {
        //TODOX create sugar method
        if(is_string($items)) {
            $items = \Ip\Menu\Helper::getZoneItems($items);
        }
        $data = array(
            'menuKey' => $menuKey,
            'items' => $items,
            'depth' => 1
        );

        if ($viewFile === null) {
            $viewFile = ipConfig()->coreModuleFile('Config/view/menu.php');
        }
        $viewFile = self::findView($viewFile);
        $view = self::create($viewFile, $data);
        return $view->render();
    }

    public function getThemeOption($name, $default = null)
    {
        $themeService = \Ip\Module\Design\Service::instance();
        return $themeService->getThemeOption($name, $default);
    }

    private static function checkData ($data) {
        foreach ($data as $key => $value) {
            if (! preg_match('/^[a-zA-Z0-9_-]+$/', $key) || $key == '') {
                $source = '';
                if(isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                    $source = "(Error source: ".($backtrace[0]['file'])." line: ".($backtrace[0]['line'])." ) ";
                }
                throw new CoreException("Incorrect view variable name '".$key."' ".$source, CoreException::VIEW);
            }
        }
    }

    
    
}