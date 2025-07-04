<?php
/**
 * License limited to a single site, for use on another site please purchase a license for this module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Dingedi.com
 * @copyright Copyright 2020 © Dingedi All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  Dingedi PrestaShop Modules
 */

namespace Dingedi\PsTranslationsApi\Html;

class DgHTMLParser
{

    /** @var \DOMDocument $domDocument */
    private $domDocument;

    /** @var \DOMXPath $domxpath */
    private $domxpath;

    /** @var bool $addSurround */
    private $addSurround = false;

    /**
     * @var string
     */
    private $removedDoctype = '';

    /**
     * @param string $html
     */
    public function __construct($html)
    {
        $html = (string) $html;
        $this->setDomDocument($html);
    }

    /**
     * @return void
     * @param string $html
     */
    private function setDomDocument($html)
    {
        $html = (string) $html;
        preg_match("/<html [^>]*>.*<\/html>/mis", $html, $hasSurround);

        if (empty($hasSurround)) {
            $this->addSurround = true;
            $html = "<div>" . $html . "</div>";
        }

        libxml_clear_errors();
        libxml_use_internal_errors(true);

        $encoding = "UTF-8";
        if (function_exists('mb_detect_encoding')) {
            $encoding = mb_detect_encoding($html);
        }

        $domDocument = new \DOMDocument('1.0');
        $xmlEncoding = "<?xml encoding='" . $encoding . "'>";

        if (defined('LIBXML_HTML_NODEFDTD') || defined('LIBXML_HTML_NOIMPLIED')) {
            $domDocument->loadHTML($xmlEncoding . $html, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        } else {
            $domDocument->loadHTML($xmlEncoding . $html);
        }

        $this->domDocument = $domDocument;
    }

    /**
     * @return \DOMXPath
     */
    public function getDOMXPath()
    {
        if (!is_null($this->domxpath)) {
            return $this->domxpath;
        }

        $this->domxpath = new \DOMXPath($this->domDocument);

        return $this->domxpath;
    }

    /**
     * @return mixed[]
     */
    public function getTextNodes()
    {
        $xpath = $this->getDOMXPath();

        $nodes = array();
        foreach ($xpath->query('//text()') as $node) {
            if (trim((string)$node->nodeValue) !== "" && !in_array($node->parentNode->tagName, array('style', 'script'))) {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }

    /**
     * @return string
     */
    public function getHTMLOutput()
    {
        $xpath = $this->getDOMXPath();

        // Remove html comments
        foreach ($xpath->query('//comment()') as $comment) {
            $comment->parentNode->removeChild($comment);
        }

        foreach ($this->domDocument->childNodes as $item) {
            if ($item->nodeType == XML_PI_NODE) {
                $this->domDocument->removeChild($item);
            }
        }

        $this->domDocument->encoding = 'UTF-8';

        $html = html_entity_decode($this->domDocument->saveHTML(), ENT_QUOTES | ENT_COMPAT, 'UTF-8');

        $regex = '/^(?:<!DOCTYPE\s+[^>]+>)/m';
        preg_match($regex, $html, $matches);

        if (!empty($matches)) {
            $html = preg_replace($regex, '', $html);
            $this->removedDoctype = $matches[0];
        }

        if ($this->addSurround) {
            $html = preg_replace('/^\s*<div>/', '', $html);
            $html = preg_replace('/<\/div>$/', '', $html);
        }

        if (!defined('LIBXML_HTML_NODEFDTD') || !defined('LIBXML_HTML_NOIMPLIED')) {
            $toRemove = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">';

            if (strncmp($html, $toRemove, strlen($toRemove)) === 0) {
                $html = substr($html, strlen($toRemove));
            }

            if ($this->addSurround) {
                $html = preg_replace('/^<html><body><div>/', '', $html);
                $html = preg_replace('/<\/div><\/body><\/html>$/', '', $html);
            }
        }

        $html = $this->removedDoctype . $html;
        $html = $this->fixHtmlOutput($html);

        return trim($html);
    }

    /**
     * @return string
     */
    private function fixHtmlOutput($html)
    {
        $replaces = array(
            "%7B" => "{",
            "%7D" => "}",
            "<br>" => "<br/>",
        );

        return str_replace(array_keys($replaces), array_values($replaces), (string)$html);
    }
}
