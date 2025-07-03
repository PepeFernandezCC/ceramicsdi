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

class HTMLSplitter
{
    /**
     * @parem int $partsLength
     * @return false|mixed[]
     * @param string $html
     * @param int $partsLength
     */
    public static function split($html, $partsLength = 4900)
    {
        $methods = ['splitOne', 'splitTwo'];

        foreach ($methods as $method) {
            $splitted = self::$method($html, $partsLength);

            if (!is_array($splitted)) {
                $splitted = false;
                continue;
            }

            // check chunks limit
            foreach ($splitted as $v) {
                if (strlen((string) $v) > $partsLength) {
                    $splitted = false;
                    continue;
                }
            }
            // all chunks are in limit, check to re-assemble
            if (is_array($splitted) && trim($html) !== trim(implode('', $splitted))) {
                $splitted = false;
                continue;
            }

            break;
        }

        return $splitted;
    }

    /**
     * @return bool|mixed[]
     * @param string $text
     * @param int $limit
     */
    private static function splitOne($text, $limit)
    {
        $text = (string) $text;
        $limit = (int) $limit;
        $tags = array('</div>', '</p>', '</section>');
        $translationsPart = array();
        $tags_copy = $tags;

        foreach ($tags as $k => $v) {
            unset($tags_copy[$k]);
            $tags_copy[] = $v;

            if (\Tools::strpos($text, $v) !== false) {
                $truncated = self::truncateOne($v, $text, $limit);
                $translationsPart = $truncated;

                if (is_array($translationsPart)) {
                    break;
                }
            }
        }

        return $translationsPart;
    }

    /**
     * @source https://stackoverflow.com/questions/57108447/how-to-split-html-to-n-parts-with-preserving-the-markup-from-php
     * @return bool|mixed[]
     * @param string $del
     * @param string $string
     * @param int $limit
     */
    private static function truncateOne($del, $string, $limit)
    {
        $del = (string) $del;
        $string = (string) $string;
        $limit = (int) $limit;
        $parts = array();
        $i = 0;

        foreach (explode($del, $string) as $str) {
            if (trim($str) === "") continue;

            $str = $str . $del;

            if (\Tools::strlen($str) > $limit) {
                return false;
            }

            if (!empty($parts) && isset($parts[$i]) && \Tools::strlen($parts[$i] . $str) > $limit) {
                ++$i;
            }
            if (isset($parts[$i])) {
                $parts[$i] .= $str;
            } else {
                $parts[$i] = $str;
            }
        }

        return $parts;
    }


    /**
     * @param string $string
     * @param int $limit
     * @return mixed[]
     */
    private static function splitTwo($string, $limit)
    {
        $string = (string) $string;
        $limit = (int) $limit;
        $splitted = array();
        $count = 0;
        do {
            $truncated = self::truncateTwo($string, $limit);
            $splitted[] = $truncated;
            $string = \Tools::substr($string, \Tools::strlen($truncated));
            $count++;

            if ($count > 1000) {
                return [$string];
            }
        } while ($string);

        return $splitted;
    }

    /**
     * @source https://github.com/urodoz/truncateHTML/blob/master/src/TruncateService.php
     * @param string $text
     * @param int $length
     * @return string
     */
    private static function truncateTwo($text, $length = 5000)
    {
        $text = (string) $text;
        $length = (int) $length;
        // if the plain text is shorter than the maximum length, return the whole text
        if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
            return $text;
        }
        // splits all html-tags to scanable lines
        preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
        $total_length = 0;
        $open_tags = array();
        $truncate = '';

        foreach ($lines as $line_matchings) {
            // if there is any html-tag in this line, handle it and add it (uncounted) to the output
            if (!empty($line_matchings[1])) {
                // if it's an "empty element" with or without xhtml-conform closing slash
                if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                    // do nothing if tag is a closing tag
                } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                    // delete tag from $open_tags list
                    $pos = array_search($tag_matchings[1], $open_tags);
                    if ($pos !== false) {
                        unset($open_tags[$pos]);
                    }
                    // if tag is an opening tag
                } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                    // add tag to the beginning of $open_tags list
                    array_unshift($open_tags, strtolower($tag_matchings[1]));
                }
                // add html-tag to $truncate'd text
                $truncate .= $line_matchings[1];
            }
            // calculate the length of the plain text part of the line; handle entities as one character
            $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
            if ($total_length + $content_length > $length) {
                // the number of characters which are left
                $left = $length - $total_length;
                $entities_length = 0;
                // search for html entities
                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                    // calculate the real length of all entities in the legal range
                    foreach ($entities[0] as $entity) {
                        if ($entity[1] + 1 - $entities_length <= $left) {
                            $left--;
                            $entities_length += strlen($entity[0]);
                        } else {
                            // no more characters left
                            break;
                        }
                    }
                }
                $truncate .= substr($line_matchings[2], 0, $left + $entities_length);
                // maximum lenght is reached, so get off the loop
                break;
            } else {
                $truncate .= $line_matchings[2];
                $total_length += $content_length;
            }
            // if the maximum length is reached, get off the loop
            if ($content_length >= $length) {
                break;
            }
        }

        // if the words shouldn't be cut in the middle, search the last occurance of a space...
        $spacepos = strrpos($truncate, ' ');
        if (isset($spacepos)) {
            // ...and cut the text in this position
            $truncate = substr($truncate, 0, $spacepos);
        }

        return $truncate;
    }
}
