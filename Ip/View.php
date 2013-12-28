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
     * Create new view object with the same doctype, but different view file and data
     * Use it to include another view file within the view
     * @param $file
     * @param null $variables
     * @return View
     */
    public function subview($file, $variables = null) {
        $foundFile = self::findFile($file);
        if ($variables === null) {
            $variables = $this->getVariables();
        }
        self::checkData($variables);
        $view = new \Ip\View($foundFile, $variables);
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
    

    
    protected static function getCallerFile($backtraceLevel = 1)
    {

    }

    protected static function getIpRelativePath($absoluteFilename)
    {
        $overrides = ipConfig()->getRaw('FILE_OVERRIDES');
        if ($overrides) {
            foreach ($overrides as $relativePath => $fullPath) {
                if (strpos($absoluteFilename, $fullPath) === 0) {
                    return substr_replace($absoluteFilename, $relativePath, 0, strlen($fullPath));
                }
            }
        }

        $baseDir = ipConfig()->getRaw('BASE_DIR');
        $baseDir = str_replace('\\', '/', $baseDir); // Compatibility with Windows
        if (strpos($absoluteFilename, $baseDir) !== 0) {
            throw new \Ip\CoreException('Cannot find relative path for file ' . $absoluteFilename);
        }

        return substr($absoluteFilename, strlen($baseDir));
    }

    /**
     * @param $file relative, absolute, Ip/ or Plugin/
     * @return mixed|string
     * @throws CoreException
     */
    private static function findFile($file) {
        //make $file absolute
        if ($file[0] == '/' || $file[1] == ':') { // Check if absolute path: '/' for unix, 'C:' for windows
            $absoluteFile = $file;
        } else {
            $backtrace = debug_backtrace();
            if(!isset($backtrace[1]['file']) || !isset($backtrace[1]['line'])) {
                throw new CoreException("Can't find caller", CoreException::VIEW);
            }
            $absoluteFile = dirname($backtrace[1]['file']) . '/' . $file;
        }

        if (DIRECTORY_SEPARATOR == '\\') {
            // Replace windows paths
            $absoluteFile = str_replace('\\', '/', $absoluteFile);
        }

        $relativeFile = static::getIpRelativePath($absoluteFile);

        if (strpos($relativeFile, 'Plugin/') == 0) {
            $overrideFile = substr($relativeFile, 7);
        } else {
            $overrideFile = $relativeFile;
        }

        if (0) {
            $backtrace = debug_backtrace();
            if(isset($backtrace[1]['file']) && isset($backtrace[1]['line'])) {
                $source = '(Error source: '.$backtrace[1]['file'].' line: '.$backtrace[1]['line'].' )';
            } else {
                $source = '';
            }
            throw new \Ip\CoreException('Can\'t find view file \''.$file. '\' ' . $source, CoreException::VIEW);
        }

        $fileInThemeDir = ipThemeFile(self::OVERRIDE_DIR . '/' . $overrideFile);
        if (is_file($fileInThemeDir)) {
            //found file in theme.
            return $fileInThemeDir;
        }

        if (file_exists($absoluteFile)) {
            return $absoluteFile;
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
        $themeService = \Ip\Internal\Design\Service::instance();
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