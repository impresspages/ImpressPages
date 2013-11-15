<?php
/**
 * @package   ImpressPages
 */

namespace Ip;


class Block
{
    private $exampleContent = '';
    private $name;
    private $isStatic = false;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function render()
    {
        global $site;
        $data = array (
            'blockName' => $this->name,
        );

        $event = new \Ip\Event($site, 'site.generateBlock', $data);

        $processed = \Ip\ServiceLocator::getDispatcher()->notifyUntil($event);

        if ($processed && $event->issetValue('content')) {
            $content = $event->getValue('content');
            if (is_object($content) && method_exists($content, 'render')) {
                $content = $content->render();
            }
            return (string)$content;
        } else {
            $predefinedContent = $site->getBlockContent($this->name);
            if ($predefinedContent !== null) {
                return $predefinedContent;
            }


            if ($this->isStatic) {
                $revisionId = null;
            } else {
                $revision = $site->getRevision();
                if ($revision) {
                    $revisionId = $revision['revisionId'];
                } else {
                    return '';
                }
            }

            if ($this->name == 'main') {
                $currentElement =  $site->getCurrentPage();
                if (!($currentElement instanceof \Ip\Module\Content\Element)) {
                    return $currentElement->generateContent();
                }
            }

            return \Ip\Module\Content\Model::generateBlock($this->name, $revisionId, $site->managementState(), $this->exampleContent);
        }
    }

    public function asStatic()
    {
        $this->isStatic = true;
        return $this;
    }

    /**
     * Sets example content to be used when block content is empty.
     *
     * @param string $content
     * @return $this
     */
    public function exampleContent($content)
    {
        $this->exampleContent = $content;
        return $this;
    }

    /**
     * Loads example content from file.
     *
     * @param $filename
     * @return $this
     */
    public function exampleContentFrom($filename)
    {
        $this->exampleContent = \Ip\View::create(\Ip\Config::themeFile($filename));
        return $this;
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
            $log = \Ip\ServiceLocator::getLog();
            $log->log('system', 'exception in __toString method', $e->getMessage().' '.$e->getFile().' '.$e->getLine());
            throw $e;
        }

        return $content;
    }

}