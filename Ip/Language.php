<?php
/**
 *
 *
 */

namespace Ip;


/**
 * Website language support class
 *
 */
class Language
{
    /** string - eg. EN */
    public $shortDescription;
    /** string - eg. English */
    public $longDescription;
    /** string */
    public $url;
    /** string - RFC 4646 code. Eg. en, en-us */
    public $code;
    /** bool */
    public $visible;
    /** int */
    public $id;
    /** string - text direction */
    public $textDirection;


    /**
     * @param $id
     * @param $code
     * @param $url
     * @param $longDescription
     * @param $shortDescription
     * @param $visible
     * @param $textDirection
     */
    public function __construct($id, $code, $url, $longDescription, $shortDescription, $visible, $textDirection)
    {
        $this->id = (int)$id;
        $this->code = $code;
        $this->url = $url;
        $this->longDescription = $longDescription;
        $this->shortDescription = $shortDescription;
        $this->visible = $visible;
        $this->textDirection = $textDirection;
    }

    public static function getByCode($languageCode)
    {
        $row = ipDb()->selectRow('language', '*', array('code' => $languageCode));
        if (!$row) {
            return null;
        }

        return new self($row['id'], $row['code'], $row['url'], $row['title'], $row['abbreviation'], $row['isVisible'], $row['textDirection']);
    }

    /**
     * Get language title
     * @return string Eg. English
     *
     */
    public function getTitle()
    {
        return $this->longDescription;
    }

    /**
     * Get language abbreviation
     * @return string Eg. en
     *
     */
    public function getAbbreviation()
    {
        return $this->shortDescription;
    }

    /**
     * Get language id
     * @return int
     *
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get language code
     * @return string Eg. en, en-us
     *
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get language URL partial
     * @return string
     *
     */
    public function getUrlPath()
    {
        return $this->url == '' ? '' : $this->url . '/';
    }

    /**
     * Check if the language is visible on a web site
     *
     * @return bool Returns true, if visible
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * Get text direction.
     *
     * @return string Returns either "ltr" or "rtl"
     */
    public function getTextDirection()
    {
        return $this->textDirection;
    }


    /**
     * Check if the language is current page language
     * @returns boolean Returns true if this language is the language of currently displayed page
     */
    public function isCurrent()
    {
        if ($this->getId() == ipContent()->getCurrentLanguage()->getId()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generate language URL
     *
     * @return string Returns link to homepage if multilingual option is turned off
     */
    public function getLink()
    {
        $link = ipConfig()->baseUrl();

        if (ipConfig()->get('rewritesDisabled')) {
            $link .= 'index.php/';
        }

        $link .= \Ip\ServiceLocator::content()->getLanguage($this->getId())->getUrlPath();

        return $link;
    }
}
