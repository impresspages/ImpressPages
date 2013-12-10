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
class View
{


    const OVERRIDE_DIR = 'override';
        
    private $file;
    private $data;
    private $doctype;

    /**
     * Construct view object using specified file and date
     * @param string $file path to view file. Relative to file where this constructor is executed from.
     * @param array $data array of data to pass to view
     */
    public static function create($file, $data = array()) {
        $foundFile = self::findFile($file);
        self::checkData($data);

        return new \Ip\View($foundFile, $data);
    }



    /**
     * Construct view object using specified file and date
     * @internal
     * @param string $file path to view file. Relative to file where this constructor is executed from.
     * @param array $data array of data to pass to view
     */
    private function __construct($file, $data = array()) {
        $this->file = $file;
        $this->data = $data;
        $doctypeConstant = ipConfig()->getRaw('DEFAULT_DOCTYPE');
        $this->doctype = constant('\Ip\Response\Layout::' . $doctypeConstant);
    }
    
    /**
     * 
     * Create new view object with the same doctype, but different view file and data
     * Use it to include another view file within the view
     * @param string $file path to view file relative to current view
     * @param array $data associative array of data to pass to the view
     */
    public function subview($file, $data = array()) {
        $foundFile = self::findFile($file);
        self::checkData($data);
        $view = new \Ip\View($foundFile, $data);
        $view->setDoctype($this->getDoctype());
        return $view;
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
                return '';
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
    

    



    
    private static function findFile($file) {
        //make $file absolute
        if ($file[0] == '/' || $file[1] == ':') { // Check if absolute path: '/' for unix, 'C:' for windows
            $absoluteFile = $file;
        } else {
            $backtrace = debug_backtrace();
            if(!isset($backtrace[1]['file']) || !isset($backtrace[1]['line'])) {
                throw new CoreException("Can't find caller", CoreException::VIEW);
            }
            $absoluteFile = dirname($backtrace[1]['file']) . DIRECTORY_SEPARATOR . $file;
        }

        if (DIRECTORY_SEPARATOR == '\\') {
            // Replace windows paths
            $absoluteFile = str_replace('\\', '/', $absoluteFile);
        }

        if (strpos($absoluteFile, ipFile('')) === 0) {
            //core dir
            $basePath = ipFile('');
        } elseif (strpos($absoluteFile, ipFile('Plugin/')) === 0) {
            //plugin dir
            $basePath = ipFile('Plugin/');
        } elseif (strpos($absoluteFile, ipThemeFile('')) === 0) {
            //theme dir
            $basePath = ipThemeFile('');
        } else {
            $backtrace = debug_backtrace();
            if(isset($backtrace[1]['file']) && isset($backtrace[1]['line'])) {
                $source = '(Error source: '.$backtrace[1]['file'].' line: '.$backtrace[1]['line'].' )';
            } else {
                $source = '';
            }
            throw new \Ip\CoreException('Can\'t find view file \''.$file. '\' ' . $source, CoreException::VIEW);
        }
        $relativeFile = substr($absoluteFile, strlen($basePath));

        $fileInThemeDir = ipThemeFile(self::OVERRIDE_DIR . DIRECTORY_SEPARATOR . $relativeFile);
        if (is_file($fileInThemeDir)) {
            //found file in theme.
            return $fileInThemeDir;
        }

        if (file_exists($basePath . $relativeFile)) {
            //found file in original location
            return $basePath . DIRECTORY_SEPARATOR . $relativeFile;
        } else {
            $backtrace = debug_backtrace();
            if(isset($backtrace[1]['file']) && isset($backtrace[1]['line'])) {
                $source = '(Error source: '.$backtrace[1]['file'].' line: '.$backtrace[1]['line'].' )';
            } else {
                $source = '';
            }
            throw new \Ip\CoreException('Can\'t find view file \''.$file. '\' ' . $source, CoreException::VIEW);
        }
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