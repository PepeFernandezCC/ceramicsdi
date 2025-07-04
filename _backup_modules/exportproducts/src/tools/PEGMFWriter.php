<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licensed under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the license agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    MyPrestaModules
 * @copyright 2013-2020 MyPrestaModules
 * @license LICENSE.txt
 */

class PEGMFWriter
{
    private $export_file;

    public function __construct($export_file_path)
    {
        $this->export_file = fopen($export_file_path, 'w');
        $this->declareGMFFile();
    }

    public function openTag($tag_name, $line_break = PHP_EOL, $tab_level = 0, $is_gmf_item = false)
    {
        $tag_name = $this->sanitizeTagName($tag_name);

        $tabs = $this->getTabsByLevel($tab_level);

        if ($is_gmf_item) {
            $tag = $tabs . '<g:' . $tag_name . '>' . $line_break;
        } else {
            $tag = $tabs . '<' . $tag_name . '>' . $line_break;
        }

        fwrite($this->export_file, $tag);
    }

    public function closeTag($tag_name, $line_break = PHP_EOL, $tab_level = 0, $is_gmf_item = false)
    {
        $tag_name = $this->sanitizeTagName($tag_name);

        $tabs = $this->getTabsByLevel($tab_level);

        if ($is_gmf_item) {
            $tag = $tabs . '</g:' . $tag_name . '>' . $line_break;
        } else {
            $tag = $tabs . '</' . $tag_name . '>' . $line_break;
        }

        fwrite($this->export_file, $tag);
    }

    public function writeTagValue($tag_value)
    {
        fwrite($this->export_file, '<![CDATA[' . $tag_value . ']]>');
    }

    public function closeFile()
    {
        fwrite($this->export_file, '</channel>' . PHP_EOL);
        fwrite($this->export_file, '</rss>' . PHP_EOL);
        fclose($this->export_file);
    }

    private function sanitizeTagName($tag_name)
    {
        return str_replace([':', ' ', "'", '"'], '', $tag_name);
    }

    private function declareGMFFile()
    {
        fwrite($this->export_file, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($this->export_file, '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . PHP_EOL);
        fwrite($this->export_file, '<channel>' . PHP_EOL);
        fwrite($this->export_file, '<title>Google Merchant Product Data Feed</title>' . PHP_EOL);
        fwrite($this->export_file, '<description>Google Merchant Product Data Feed</description>' . PHP_EOL);
        fwrite($this->export_file, '<link>'._PS_BASE_URL_SSL_.__PS_BASE_URI__.'</link>' . PHP_EOL);
    }

    private function getTabsByLevel($tab_level)
    {
        $tabs = "";

        if ($tab_level == 0) {
            return $tabs;
        }

        for ($i = 0; $i < $tab_level; $i++) {
            $tabs .= "\t";
        }

        return $tabs;
    }
}