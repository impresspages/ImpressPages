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
class Layout extends \Ip\Response
{

    const DOCTYPE_XHTML1_STRICT = 1;
    const DOCTYPE_XHTML1_TRANSITIONAL = 2;
    const DOCTYPE_XHTML1_FRAMESET = 3;
    const DOCTYPE_HTML4_STRICT = 4;
    const DOCTYPE_HTML4_TRANSITIONAL = 5;
    const DOCTYPE_HTML4_FRAMESET = 6;
    const DOCTYPE_HTML5 = 7;


    /**
     * @var string
     */
    protected $layout = null;

    private $layoutVariables = array();

    private $title;
    private $keywords;
    private $description;
    private $favicon;
    private $charset;


    public function __construct($content = null, $headers = null, $statusCode = 200)
    {
        $this->setFavicon(ipFileUrl('favicon.ico'));
        $this->setCharset(ipConfig()->get('charset'));
        parent::__construct($content, $headers, $statusCode);
    }

    public function render()
    {
        return $this->execute()->render();
    }

    /**
     * Execute response and return html response
     *
     * @return \Ip\Response
     */
    public function execute()
    {
        ipContent()->setBlockContent('main', $this->content);

        $layout = $this->getLayout();

        if ($layout[0] == '/' || $layout[1] == ':') { // Check if absolute path: '/' for unix, 'C:' for windows
            $viewFile = $layout;
            if (!is_file($viewFile)) {
                $viewFile = ipThemeFile('main.php');
            }
        } elseif (strpos($layout, '/') !== false) { //impresspages path. Eg. Ip/Internal/xxx.php
            $viewFile = $layout;
            if (!is_file(ipFile($viewFile))) {
                $viewFile = ipThemeFile('main.php');
            }
        } else { //layout file. Like main.php
            $viewFile = ipThemeFile($layout);
            if (!is_file($viewFile)) {
                $viewFile = ipThemeFile('main.php');
            }
        }

        $content = ipView($viewFile, $this->getLayoutVariables())->render();

        $response = new \Ip\Response($content, $this->getHeaders(), $this->getStatusCode());

        return $response;
    }

    protected function chooseLayout()
    {
        if (ipRoute()->isAdmin()) {
            $this->layout = 'Ip/Internal/Admin/view/layout.php';
        } elseif (\Ip\Internal\Admin\Model::isSafeMode()) {
            $this->layout = 'Ip/Internal/Admin/view/safeModeLayout.php';
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
        if ($this->layout == '') {
            $this->chooseLayout();
        }

        return $this->layout;


    }

    public function setLayoutVariable($name, $value)
    {
        $this->layoutVariables[$name] = $value;
    }

    public function getLayoutVariables()
    {
        return $this->layoutVariables;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getFavicon()
    {
        return $this->favicon;
    }

    public function setFavicon($favicon)
    {
        $this->favicon = $favicon;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

}
