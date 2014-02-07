<?php


namespace Ip;


class CurrentPage
{
    private $requestedPage;
    private $revision;

    public function __construct($requestedPage)
    {
        $this->requestedPage = $requestedPage;
    }

    /**
     * @return \Ip\Language
     */
    public function getLanguage()
    {
        return $this->requestedPage['language'];
    }

    public function getControllerClass()
    {
        return $this->requestedPage['controllerClass'];
    }

    public function getControllerType()
    {
        return $this->requestedPage['controllerType'];
    }

    public function getControllerModule()
    {
        return $this->requestedPage['controllerModule'];
    }

    public function getControllerAction()
    {
        return $this->requestedPage['controllerAction'];
    }

    public function getPage()
    {
        return isset($this->requestedPage['page']) ? $this->requestedPage['page'] : new \Ip\Page404(1, '404');
    }

    public function getUrlPath()
    {
        return !empty($this->requestedPage['urlParts']) ? $this->requestedPage['urlParts'] : array();
    }

    public function getZone()
    {
        if (!isset($this->requestedPage['zone'])) {
            return new \Ip\Zone404('404');
        }

        return ipContent()->getZone($this->requestedPage['zone']);
    }

    public function getCurrentRevision()
    {
        if ($this->revision !== null) {
            return $this->revision;
        }
        $revision = false;
        $page = $this->getPage();
        if (ipIsManagementState()) {
            if (ipRequest()->getQuery('cms_revision')) {
                $revisionId = ipRequest()->getQuery('cms_revision');
                $revision = \Ip\Internal\Revision::getRevision($revisionId);
            }

            if ($page && !($page instanceof \Ip\Page404)) {
                if ($revision === false || $revision['zoneName'] != ipContent()->getCurrentZone()->getName(
                    ) || $revision['pageId'] != $page->getId()
                ) {
                    $revision = \Ip\Internal\Revision::getLastRevision(
                        ipContent()->getCurrentZone()->getName(),
                        $page->getId()
                    );
                    if ($revision['published']) {
                        $duplicatedId = \Ip\Internal\Revision::duplicateRevision($revision['revisionId']);
                        $revision = \Ip\Internal\Revision::getRevision($duplicatedId);
                    }
                }
            } else {
                $revision = false;
            }
        } elseif ($page) {
                $revision = \Ip\Internal\Revision::getPublishedRevision(
                    ipContent()->getCurrentZone()->getName(),
                    $page->getId()
                );
        }
        $this->revision = $revision;
        return $revision;
    }

    public function getType()
    {
        return $this->requestedPage['controllerType'];
    }

    public function _set($name, $value)
    {
        $this->requestedPage[$name] = $value;
    }

    public function get($name, $default = NULL)
    {
        return array_key_exists($name, $this->requestedPage) ? $this->requestedPage[$name] : $default;
    }
}
