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
     * Resize, cut and optimize image.
     *
     * @param string $sourceFile Image object from $_FILE
     * @param string $destinationFile Destination filename
     * @param int $destinationWidth Desired width (optional)
     * @param int $destinationHeight Desired height (optional)
     * @param string $fileType Desired file_type (may be override by PS_IMAGE_QUALITY)
     * @param bool $forceType Don't override $file_type
     * @param int $error Out error code
     * @param int $targetWidth Needed by AdminImportController to speed up the import process
     * @param int $targetHeight Needed by AdminImportController to speed up the import process
     * @param int $quality Needed by AdminImportController to speed up the import process
     * @param int $sourceWidth Needed by AdminImportController to speed up the import process
     * @param int $sourceHeight Needed by AdminImportController to speed up the import process
     *
     * @return bool Operation result
     */
    public static function resize(
        $sourceFile,
        $destinationFile,
        $destinationWidth = null,
        $destinationHeight = null,
        $fileType = 'jpg',
        $forceType = false,
        &$error = 0,
        &$targetWidth = null,
        &$targetHeight = null,
        $quality = 5,
        &$sourceWidth = null,
        &$sourceHeight = null
    )
    {
        if (Module::isInstalled('lgimageoptimize') && Module::isEnabled('lgimageoptimize') && Configuration::get('LGIMAGEOPTIMIZE_ADVANCED')) {
            return self::resizeLG(
                $sourceFile,
                $destinationFile,
                $destinationWidth,
                $destinationHeight,
                $fileType,
                $forceType,
                $error,
                $targetWidth,
                $targetHeight,
                $quality,
                $sourceWidth,
                $sourceHeight
            );
        } else {
            return parent::resize(
                $sourceFile,
                $destinationFile,
                $destinationWidth,
                $destinationHeight,
                $fileType,
                $forceType,
                $error,
                $targetWidth,
                $targetHeight,
                $quality,
                $sourceWidth,
                $sourceHeight
            );
        }
    }

    /**
     * Resize, cut and optimize image
     *
     * @param string $sourceFile Image object from $_FILE
     * @param string $destinationFile Destination filename
     * @param int $destinationWidth Desired width (optional)
     * @param int $destinationHeight Desired height (optional)
     * @param string $fileType Desired file_type (may be override by PS_IMAGE_QUALITY)
     * @param bool $forceType Don't override $file_type
     * @param int $error Out error code
     * @param int $targetWidth Needed by AdminImportController to speed up the import process
     * @param int $targetHeight Needed by AdminImportController to speed up the import process
     * @param int $quality Needed by AdminImportController to speed up the import process
     * @param int $sourceWidth Needed by AdminImportController to speed up the import process
     * @param int $sourceHeight Needed by AdminImportController to speed up the import process
     *
     * @return bool Operation result
     */
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
        return $module->resizeLG1720(
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
     * Create an image with GD extension from a given type.
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
     * Generate a cached thumbnail for object lists (eg. carrier, order statuses...etc).
     *
     * @param string $image Real image filename
     * @param string $cacheImage Cached filename
     * @param int $size Desired size
     * @param string $imageType Image type
     * @param bool $disableCache When turned on a timestamp will be added to the image URI to disable the HTTP cache
     * @param bool $regenerate When turned on and the file already exist, the file will be regenerated
     *
     * @return string
     */
    public static function thumbnail(
        $image,
        $cacheImage,
        $size,
        $imageType = 'jpg',
        $disableCache = true,
        $regenerate = false
    )
    {
        if (!file_exists($image)) {
            return '';
        }

        if (file_exists(_PS_TMP_IMG_DIR_ . $cacheImage) && $regenerate) {
            @unlink(_PS_TMP_IMG_DIR_ . $cacheImage);
        }

        if ($regenerate || !file_exists(_PS_TMP_IMG_DIR_ . $cacheImage)) {
            $infos = getimagesize($image);

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($image)) {
                return false;
            }

            $x = $infos[0];
            $y = $infos[1];
            $maxX = $size * 3;

            // Size is already ok
            if ($y < $size && $x <= $maxX) {
                copy($image, _PS_TMP_IMG_DIR_ . $cacheImage);
            } else {
                // We need to resize */
                $ratioX = $x / ($y / $size);
                if ($ratioX > $maxX) {
                    $ratioX = $maxX;
                    $size = $y / ($x / $maxX);
                }

                parent::resize($image, _PS_TMP_IMG_DIR_ . $cacheImage, $ratioX, $size, $imageType);
            }
        }

        return '<img src="' . self::getThumbnailPath($cacheImage, $disableCache) .
            '" alt="" class="imgm img-thumbnail" />';
    }
}
