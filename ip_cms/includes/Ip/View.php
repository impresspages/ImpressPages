<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Ip;


if (!defined('CMS')) exit;



/**
 *
 * View class
 *
 */
class View{

    
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
        global $site;
        $this->file = $file;
        $this->data = $data;
        if ($languageId == null) {
            $this->languageId = $site->getCurrentLanguage()->getId();
        } else {
            $this->languageId = $languageId;
        }
        eval('$this->doctype = self::'.DEFAULT_DOCTYPE.';');
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
        require_once(BASE_DIR.MODULE_DIR.'standard/content_management/model.php');
        $answer = \Modules\standard\content_management\Model::generateWidgetPreviewFromStaticData($widgetName, $data, $layout);
        return $answer;
    }
    
    /**
     * Escape and echo text
     * @param string $text
     */
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
        return $this->esc($this->par($parameterKey), $variables);
    }

    public function par($parameterKey, $variables = null){
        global $parametersMod;
        $parts = explode('/', $parameterKey);
        if (count($parts) != 4) {
            if (DEVELOPMENT_ENVIRONMENT) {
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
     * 
     * Set view data
     * @param array $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }


    public function render () {
        global $site;
        global $log;
        global $dispatcher;
        global $parametersMod;
        global $session;

        foreach ($this->data as $foreachDataKey => $foreachDataValue) {
            eval(' $'.$foreachDataKey.' = $foreachDataValue;');
        }


        $found = false;

        ob_start();

        require ($this->file);      //file existance has been checked in constructor

        $output = ob_get_contents();
        ob_end_clean();

        return $output;

    }
    
    public function __toString()
    {
        return $this->render();
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
        global $site;
        if ($doctype === null) {
            $doctype = $this->getDoctype();
        }
        switch ($doctype) {
            case self::DOCTYPE_XHTML1_STRICT:
            case self::DOCTYPE_XHTML1_TRANSITIONAL:
            case self::DOCTYPE_XHTML1_FRAMESET:
                $lang = $site->getCurrentLanguage()->getCode();
                return ' xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$lang.'" lang="'.$lang.'"';
                break;
            case self::DOCTYPE_HTML4_STRICT:
            case self::DOCTYPE_HTML4_TRANSITIONAL:
            case self::DOCTYPE_HTML4_FRAMESET:
            default:
                return '';
                break;
            case self::DOCTYPE_HTML5:
                $lang = $site->getCurrentLanguage()->getCode();
                return ' lang="'.$lang.'"';
                break;
        }        
       
    }

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
        if (strpos($file, BASE_DIR) !== 0) {
            $file = dirname($sourceFile).'/'.$file;
        }



        $moduleView = ''; //relative link to view according to modules root.
        if (strpos($file, BASE_DIR.MODULE_DIR) === 0) {
            $moduleView = substr($file, strlen(BASE_DIR.MODULE_DIR));
        }

        if ($moduleView == '' && strpos($file, BASE_DIR.PLUGIN_DIR) === 0) {
            $moduleView = substr($file, strlen(BASE_DIR.PLUGIN_DIR));
        }

        if ($moduleView == '' && strpos($file, BASE_DIR.THEME_DIR.'modules/') === 0) {
            $moduleView = substr($file, strlen(BASE_DIR.THEME_DIR.'modules/'));
        }
        if ($moduleView != '') {
            if (file_exists(BASE_DIR.THEME_DIR.THEME.'/modules/'.$moduleView)) {
                return BASE_DIR.THEME_DIR.THEME.'/modules/'.$moduleView;
            }

            if (file_exists(BASE_DIR.PLUGIN_DIR.$moduleView)) {
                return(BASE_DIR.PLUGIN_DIR.$moduleView);
            }

            if (file_exists(BASE_DIR.MODULE_DIR.$moduleView)) {
                return(BASE_DIR.MODULE_DIR.$moduleView);
            }

        } else {
            if (file_exists($file)) {
                return $file;
            } else {
                return false;
            }
        }

        return false;
    }


    public function generateBlock($blockName, $static = false)
    {
        global $site;
        return $site->generateBlock($blockName, $static);
    }

    public function generateManagedLogo($cssClass = null)
    {
        $inlineManagementService = new \Modules\developer\inline_management\Service();
        return $inlineManagementService->generateManagedLogo($cssClass);
    }

    public function generateManagedString($key, $tag = 'span', $defaultValue = null, $cssClass = null)
    {
        $inlineManagementService = new \Modules\developer\inline_management\Service();
        return $inlineManagementService->generateManagedString($key, $tag, $defaultValue, $cssClass);
    }

    public function generateManagedText($key, $tag = 'div', $defaultValue = null, $cssClass = null)
    {
        $inlineManagementService = new \Modules\developer\inline_management\Service();
        return $inlineManagementService->generateManagedText($key, $tag, $defaultValue, $cssClass);
    }

    public function generateManagedImage($key, $defaultValue = null, $options = array(), $cssClass = null)
    {
        $inlineManagementService = new \Modules\developer\inline_management\Service();
        return $inlineManagementService->generateManagedImage($key, $defaultValue, $options, $cssClass);
    }


    /**
     * @param int $price in cents
     * @param string $currency three letter currency code
     */
    public function formatPrice($price, $currency)
    {
        global $dispatcher;
        global $site;

        $data = array (
            'price' => $price,
            'currency' => $currency
        );
        $event = new \Ip\Event($this, 'global.formatCurrency', $data);
        $dispatcher->notifyUntil($event);
        if ($event->issetValue('formattedPrice')) {
            $formattedPrice = $event->getValue('formattedPrice');
        } else {
            if (function_exists('numfmt_create') && function_exists('numfmt_format_currency')) {
                $language = $site->getLanguageById($this->getLanguageId());
                $locale = str_replace('-', '_', $language->getCode());
                $fmt = numfmt_create( $locale, \NumberFormatter::CURRENCY );

                $formattedPrice = numfmt_format_currency($fmt, $price / 100, $currency);
            } else {
                $formattedPrice = ($data['price']/100).' '.$data['currency'];
            }
        }
        return $formattedPrice;
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