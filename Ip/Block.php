<?php
/**
 * Page content block
 *
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

    /**
     * Render page block content
     * @param null $revisionId
     * @return null|string
     */
    public function render($revisionId = null)
    {
        $data = array (
            'blockName' => $this->name,
        );

        $content = ipJob('ipGenerateBlock', $data);

        if ($content) {
            if (is_object($content) && method_exists($content, 'render')) {
                $content = $content->render();
            }
            return (string)$content;
        } else {
            $predefinedContent = \Ip\ServiceLocator::content()->getBlockContent($this->name);
            if ($predefinedContent !== null) {
                return $predefinedContent;
            }


            if ($this->isStatic) {
                $revisionId = null;
            } else {
                if ($revisionId === null) {
                    $revision = \Ip\ServiceLocator::content()->getCurrentRevision();
                    if ($revision) {
                        $revisionId = $revision['revisionId'];
                    }
                }
                if (!$revisionId) {
                    return '';
                }
            }

            return \Ip\Internal\Content\Model::generateBlock($this->name, $revisionId, ipIsManagementState(), $this->exampleContent);
        }
    }

    /**
     * Make a block content the same on all pages.
     *
     * @return $this
     */
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
     * Example content should be placed in theme's directory
     *
     * @param string $filename
     * @return $this
     */
    public function exampleContentFrom($filename)
    {
        $this->exampleContent = ipView(ipThemeFile($filename));
        return $this;
    }

    /**
     * PHP can't handle exceptions in __toString method. Try to avoid it every time possible. Use render() method instead.
     * @return string
     * @ignore
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
            ipLog()->error('Block.toStringException: Exception in block `{block}` __toString() method.', array('block' => $this->name, 'exception' => $e));
            return $e->getTraceAsString();
        }
        return $content;
    }

}