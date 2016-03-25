<?php
/******************************************************************************
 * Copyright (c) 2010 Jevon Wright and others.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 * https://github.com/soundasleep/html2text
 *
 * or
 *
 * LGPL license
 *
 * Contributors:
 *    Jevon Wright - initial API and implementation
 *
 *    ImpressPages team - objectify the code
 ****************************************************************************/

namespace Ip\Internal\Text;


class Html2TextException extends \Exception {
    var $more_info;

    public function __construct($message = "", $more_info = "") {
        parent::__construct($message);
        $this->more_info = $more_info;
    }
}

class Html2Text
{

    /**
     * Tries to convert the given HTML into a plain text format - best suited for
     * e-mail display, etc.
     *
     * <p>In particular, it tries to maintain the following features:
     * <ul>
     *   <li>Links are maintained, with the 'href' copied over
     *   <li>Information in the &lt;head&gt; is lost
     * </ul>
     *
     * @param string html the input HTML
     * @param boolean partial states if provided HTML is partial or not. Partial means without head / body sections. If null, function tries to autodetect.
     * @return string the HTML converted, as best as possible, to text
     * @throws Html2TextException if the HTML could not be loaded as a {@link \DOMDocument}
     */
    public static function convert($html, $partial = null)
    {
        if ($partial || $partial === null && mb_strpos($html, '<html ') === false) {
            $html = '<html lang="en"><head><meta charset="UTF-8" /></head><body>' . $html .'</body>';
        }
        if ($html == '') {
            return '';
        }
        $html = self::fix_newlines($html);

        $doc = new \DOMDocument('1.0', 'UTF-8');
        $prevValue = libxml_use_internal_errors(true);
        $loaded = $doc->loadHTML($html);
        libxml_use_internal_errors($prevValue);
        if (!$loaded) {
            throw new Html2TextException("Could not load HTML - badly formed?", $html);
        }

        $output = self::iterate_over_node($doc);

        // remove leading and trailing spaces on each line
        $output = preg_replace("/[ \t]*\n[ \t]*/im", "\n", $output);

        // remove leading and trailing whitespace
        $output = trim($output);

        return $output;
    }



    /**
     * Unify newlines; in particular, \r\n becomes \n, and
     * then \r becomes \n. This means that all newlines (Unix, Windows, Mac)
     * all become \ns.
     *
     * @param string text text with any number of \r, \r\n and \n combinations
     * @return string the fixed text
     */
    protected static function fix_newlines($text) {
        // replace \r\n to \n
        $text = str_replace("\r\n", "\n", $text);
        // remove \rs
        $text = str_replace("\r", "\n", $text);

        return $text;
    }

    protected static function next_child_name($node) {
        // get the next child
        $nextNode = $node->nextSibling;
        while ($nextNode != null) {
            if ($nextNode instanceof DOMElement) {
                break;
            }
            $nextNode = $nextNode->nextSibling;
        }
        $nextName = null;
        if ($nextNode instanceof DOMElement && $nextNode != null) {
            $nextName = mb_strtolower($nextNode->nodeName);
        }

        return $nextName;
    }
    protected static function prev_child_name($node) {
        // get the previous child
        $nextNode = $node->previousSibling;
        while ($nextNode != null) {
            if ($nextNode instanceof DOMElement) {
                break;
            }
            $nextNode = $nextNode->previousSibling;
        }
        $nextName = null;
        if ($nextNode instanceof DOMElement && $nextNode != null) {
            $nextName = mb_strtolower($nextNode->nodeName);
        }

        return $nextName;
    }

    protected static function iterate_over_node($node) {
        if ($node instanceof \DOMText) {
            return mb_ereg_replace("/[\\t\\n\\v\\f\\r ]+/im", " ", $node->wholeText);
        }
        if ($node instanceof \DOMDocumentType) {
            // ignore
            return "";
        }

        $nextName = self::next_child_name($node);
        $prevName = self::prev_child_name($node);

        $name = mb_strtolower($node->nodeName);

        // start whitespace
        switch ($name) {
            case "hr":
                return "------\n";

            case "style":
            case "head":
            case "title":
            case "meta":
            case "script":
                // ignore these tags
                return "";

            case "h1":
            case "h2":
            case "h3":
            case "h4":
            case "h5":
            case "h6":
                // add two newlines
                $output = "\n";
                break;

            case "p":
            case "div":
                // add one line
                $output = "\n";
                break;

            default:
                // print out contents of unknown tags
                $output = "";
                break;
        }

        // debug
        //$output .= "[$name,$nextName]";

        if (isset($node->childNodes)) {
            for ($i = 0; $i < $node->childNodes->length; $i++) {
                $n = $node->childNodes->item($i);

                $text = self::iterate_over_node($n);

                $output .= $text;
            }
        }

        // end whitespace
        switch ($name) {
            case "style":
            case "head":
            case "title":
            case "meta":
            case "script":
                // ignore these tags
                return "";

            case "h1":
            case "h2":
            case "h3":
            case "h4":
            case "h5":
            case "h6":
                $output .= "\n";
                break;

            case "p":
            case "br":
                // add one line
                if ($nextName != "div")
                    $output .= "\n";
                break;

            case "div":
                // add one line only if the next child isn't a div
                if ($nextName != "div" && $nextName != null)
                    $output .= "\n";
                break;

            case "a":
                // links are returned in [text](link) format
                $href = $node->getAttribute("href");
                if ($href == null) {
                    // it doesn't link anywhere
                    if ($node->getAttribute("name") != null) {
                        $output = "[$output]";
                    }
                } else {
                    if ($href == $output || $href == "mailto:$output" || $href == "http://$output" || $href == "https://$output") {
                        // link to the same address: just use link
                        $output;
                    } else {
                        // replace it
                        $output = "[$output]($href)";
                    }
                }

                // does the next node require additional whitespace?
                switch ($nextName) {
                    case "h1": case "h2": case "h3": case "h4": case "h5": case "h6":
                    $output .= "\n";
                    break;
                }

            default:
                // do nothing
        }

        return $output;
    }


}



