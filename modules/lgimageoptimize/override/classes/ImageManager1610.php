<?php
/**
 * Copyright 2022 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
class ImageManager extends ImageManagerCore
{
    /**
     * Resize, cut and optimize image
     *
     * @param string $src_file Image object from $_FILE
     * @param string $dst_file Destination filename
     * @param int $dst_width Desired width (optional)
     * @param int $dst_height Desired height (optional)
     * @param string $file_type
     * @param bool $force_type
     * @param int $error
     * @param int $tgt_width
     * @param int $tgt_height
     * @param int $quality
     * @param int $src_width
     * @param int $src_height
     *
     * @return bool Operation result
     */
    public static function resize(
        $src_file,
        $dst_file,
        $dst_width = null,
        $dst_height = null,
        $file_type = 'jpg',
        $force_type = false,
        &$error = 0,
        &$tgt_width = null,
        &$tgt_height = null,
        $quality = 5,
        &$src_width = null,
        &$src_height = null
    )
    {
        if (Module::isInstalled('lgimageoptimize') && Module::isEnabled('lgimageoptimize') && Configuration::get('LGIMAGEOPTIMIZE_ADVANCED')) {
            return self::resizeLG(
                $src_file,
                $dst_file,
                $dst_width,
                $dst_height,
                $file_type,
                $force_type,
                $error,
                $tgt_width,
                $tgt_height,
                $quality,
                $src_width,
                $src_height
            );
        } else {
            return parent::resize(
                $src_file,
                $dst_file,
                $dst_width,
                $dst_height,
                $file_type,
                $force_type,
                $error,
                $tgt_width,
                $tgt_height,
                $quality,
                $src_width,
                $src_height
            );
        }
    }

    public static function resizeLG(
        $src_file,
        $dst_file,
        $dst_width = null,
        $dst_height = null,
        $file_type = 'jpg',
        $force_type = false,
        &$error = 0
    )
    {
        $module = Module::getInstanceByName('lgimageoptimize');
        return $module->resizeLG1610(
            $src_file,
            $dst_file,
            $dst_width,
            $dst_height,
            $file_type,
            $force_type,
            $error
        );
    }


    /**
     * Create an image with GD extension from a given type
     *
     * @param string $type
     * @param string $filename
     *
     * @return resource
     */
    public static function create($type, $filename)
    {
        switch ($type) {
            case IMAGETYPE_WEBP:
                return imagecreatefromwebp($filename);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($filename);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($filename);
            case IMAGETYPE_JPEG:
            default:
                return imagecreatefromjpeg($filename);
        }
    }

    /**
     * Generate a cached thumbnail for object lists (eg. carrier, order statuses...etc)
     *
     * @param string $image Real image filename
     * @param string $cache_image Cached filename
     * @param int $size Desired size
     * @param string $image_type Image type
     * @param bool $disable_cache When turned on a timestamp will be added to the image URI to disable the HTTP cache
     * @param bool $regenerate When turned on and the file already exist, the file will be regenerated
     *
     * @return string
     */
    public static function thumbnail(
        $image,
        $cache_image,
        $size,
        $image_type = 'jpg',
        $disable_cache = true,
        $regenerate = false
    )
    {
        if (!file_exists($image)) {
            return '';
        }

        if (file_exists(_PS_TMP_IMG_DIR_ . $cache_image) && $regenerate) {
            @unlink(_PS_TMP_IMG_DIR_ . $cache_image);
        }

        if ($regenerate || !file_exists(_PS_TMP_IMG_DIR_ . $cache_image)) {
            $infos = getimagesize($image);

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($image)) {
                return false;
            }

            $x = $infos[0];
            $y = $infos[1];
            $max_x = $size * 3;

            // Size is already ok
            if ($y < $size && $x <= $max_x) {
                copy($image, _PS_TMP_IMG_DIR_ . $cache_image);
            } else { // We need to resize */
                $ratio_x = $x / ($y / $size);
                if ($ratio_x > $max_x) {
                    $ratio_x = $max_x;
                    $size = $y / ($x / $max_x);
                }

                parent::resize($image, _PS_TMP_IMG_DIR_ . $cache_image, $ratio_x, $size, $image_type);
            }
        }

        // Relative link will always work, whatever the base uri set in the admin
        if (Context::getContext()->controller->controller_type == 'admin') {
            return
                '<img src="../img/tmp/' .
                $cache_image .
                ($disable_cache ? '?time=' . time() : '') .
                '" alt="" class="imgm img-thumbnail" />';
        } else {
            return
                '<img src="' .
                _PS_TMP_IMG_ .
                $cache_image .
                ($disable_cache ? '?time=' . time() : '') .
                '" alt="" class="imgm img-thumbnail" />';
        }
    }
}
