<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;

/**
 *
 * MVC View class
 *
 */
class View
{
    /**
     * @ignore
     */
    const OVERRIDE_DIR = 'override';

    private $file;
    private $data;
    private $doctype;

    static protected $overrides = array();


    /**
     * Construct view object using specified file and data.
     * @internal
     * @param string $file Path to view file. Relative to file where this constructor is executed from.
     * @param array $data Array of data to pass to view
     */
    public function __construct($file, $data = array())
    {
        self::checkData($data);
        $this->file = $file;
        $this->data = $data;
        $doctypeConstant = ipConfig()->get('defaultDoctype');
        $this->doctype = constant('\Ip\Response\Layout::' . $doctypeConstant);
    }

    /**
     * Set view variables
     * @param $variables
     * @return $this
     */
    public function setVariables($variables)
    {
        $this->data = $variables;
        return $this;
    }

    /**
     * Get view variables
     * @return array
     */
    public function getVariables()
    {
        return $this->data;
    }


    /**
     * Set a single view variable
     * @param string $name
     * @param $value
     * @return $this
     */
    public function setVariable($name, $value)
    {
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * Unset a view variable
     * @param $name
     * @return $this
     */
    public function unsetVariable($name)
    {
        unset($this->data[$name]);
        return $this;
    }

    /**
     * Get view variable value
     * @param $name
     * @return null
     */
    public function getVariable($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return null;
    }


    /**
     * Render a view and return HTML, XML, or any other string.
     * @return string
     */
    public function render()
    {


        extract($this->data);

        ob_start();

        $file = $this->file;
        if (!empty(self::$overrides[$file])) {
            $file = self::$overrides[$file];
        }
        require($file); // file existence has been checked in ipView function

        $output = ob_get_contents();
        ob_end_clean();

        return $output;

    }


    /**
     * PHP can't handle exceptions in __toString method. Try to avoid it every time possible. Use render() method instead.
     * @ignore
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
            ipLog()->error(
                'View.toStringException: Exception in View::__toString() method.',
                array('exception' => $e, 'view' => $this->file)
            );

            if (ipConfig()->isDevelopmentEnvironment()) {
                return "<pre class=\"error\">\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n</pre>";
            } else {
                return '';
            }
        }

        return $content;
    }

    /**
     * Set DOCTYPE declaration.
     * @param $doctype
     * @return $this
     */
    public function setDoctype($doctype)
    {
        $this->doctype = $doctype;
        return $this;
    }

    /**
     * Return DOCTYPE declaration.
     * @return mixed
     */
    public function getDoctype()
    {
        return $this->doctype;
    }

    /**
     * Get theme option. Options can be viewed or set using UI via Theme options dialog box.
     * @param $name
     * @param null $default
     * @return string
     */
    public function getThemeOption($name, $default = null)
    {
        $themeService = \Ip\Internal\Design\Service::instance();
        return $themeService->getThemeOption($name, $default);
    }

    private static function checkData($data)
    {
        foreach ($data as $key => $value) {
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $key) || $key == '') {
                $source = '';
                if (isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                    $source = "(Error source: " . ($backtrace[0]['file']) . " line: " . ($backtrace[0]['line']) . " ) ";
                }
                throw new \Ip\Exception\View("Incorrect view variable name '" . esc($key) . "' " . esc($source));
            }
        }
    }

    /**
     * Register view file override
     * @param string $source absolute path to the view file to be overriden
     * @param string $destination absolute path to the new view file
     */
    public static function registerOverride($source, $destination)
    {
        self::$overrides[$source] = $destination;
    }

    /**
     * Clear registered view file override
     * @param $source
     */
    public static function clearOverride($source)
    {
        unset(self::$overrides[$source]);
    }
}
