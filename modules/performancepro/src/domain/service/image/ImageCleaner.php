<?php
/**
 * This file is part of the performancepro package.
 *
 * @author Mathias Reker
 * @copyright Mathias Reker
 * @license Academic Free License (AFL 3.0)
 *
 * Additionally, this module is subject to a proprietary End User License Agreement (EULA).
 * For the full copyright, open source license, and EULA information, please view the LICENSE
 * that were distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\Module\PerformancePro\domain\service\image;

use PrestaShop\Module\PerformancePro\domain\service\log\LogService;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class ImageCleaner
{
    public function deleteBrokenImages(bool $analyse = false): int
    {
        $result = 0;

        $rows = \Image::getAllImages();

        foreach ($rows as $row) {
            $imageId = $row['id_image'];

            $imgFolder = _PS_PROD_IMG_DIR_ . \Image::getImgFolderStatic($imageId);

            $imgPath = $imgFolder . $imageId;

            $fileExtensions = ['.gif', '.jpg', '.jpeg', '.png', '.webp'];
            foreach ($fileExtensions as $fileExtension) {
                if (file_exists($imgPath . $fileExtension)) {
                    continue 2;
                }
            }

            if (!$analyse) {
                try {
                    if ((new \Image($imageId))->delete()) {
                        ++$result;
                    }
                } catch (\PrestaShopException $prestaShopException) {
                    LogService::error($prestaShopException->getMessage(), $prestaShopException->getTrace());
                }
            } else {
                ++$result;
            }
        }

        return $result;
    }

    public function deleteUnusedImages(): int
    {
        $result = 0;

        $realImages = \Tools::scandir(_PS_PROD_IMG_DIR_, 'jpg', '', true);

        $imagesData = \Image::getAllImages();

        $dbImages = array_column($imagesData, 'id_image');

        $dbImagesAsInt = array_map(static function ($value): int {
            return (int) $value;
        }, $dbImages);

        foreach ($realImages as $realImage) {
            $idImage = (int) strtr(basename($realImage), '.jpg', '');

            $imageExists = \in_array($idImage, $dbImagesAsInt, true);

            if ($imageExists) {
                continue;
            }

            if (!unlink(_PS_PROD_IMG_DIR_ . $realImage)) {
                continue;
            }

            ++$result;
        }

        $this->deleteEmptyImagesFolder(['jpg']);

        return $result;
    }

    public function deleteEmptyImagesFolder(array $extensions = ['jpg', 'png', 'webp']): int
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(_PS_PROD_IMG_DIR_, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
            \RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
        );

        $result = 0;

        foreach ($iterator as $path) {
            if ($path->isDir()) {
                $countImages = 0;

                foreach ($extensions as $extension) {
                    $directory = \Tools::scandir($path->getRealPath(), $extension, '', true);
                    $countImages += \is_array($directory) || $directory instanceof \Countable ? \count($directory) : 0;
                }

                if (0 === $countImages) {
                    ++$result;

                    \Tools::deleteDirectory($path->getRealPath(), true);
                }
            }
        }

        return $result;
    }

    /**
     * Delete all images in tmp dir.
     */
    public function deleteTmpImages(): int
    {
        $result = 0;

        foreach (scandir(_PS_TMP_IMG_DIR_, \SCANDIR_SORT_NONE) as $img) {
            if (preg_match('#(.*)\.jpg$#', $img)) {
                ++$result;

                unlink(_PS_TMP_IMG_DIR_ . $img);
            }
        }

        return $result;
    }
}
