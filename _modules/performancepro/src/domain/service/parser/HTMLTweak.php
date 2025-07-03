<?php
/**
 * This file is part of the performancepro package.
 *
 * @author Mathias Reker
 * @copyright Mathias Reker
 * @license Commercial Software License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\Module\PerformancePro\domain\service\parser;

use Configuration;
use DOMDocument;
use DOMElement;
use Exception;
use FasterImage\FasterImage;
use PrestaShop\Module\PerformancePro\domain\service\image\ImageFactory\SVG;
use PrestaShop\Module\PerformancePro\domain\service\image\ImageFactory\WebP;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\domain\service\util\ContextService;
use PrestaShop\Module\PerformancePro\domain\service\util\LinkService;
use PrestaShop\Module\PerformancePro\domain\service\util\PathService;
use PrestaShop\Module\PerformancePro\exception\PerformanceProInvalidResourceException;
use PrestaShop\Module\PerformancePro\resources\config\Config;
use Tools;

final class HTMLTweak
{
    /**
     * @var string
     */
    private $html;

    /**
     * @var bool
     */
    private $loadScriptAsync = false;

    /**
     * @var bool
     */
    private $addLazyLoadImg = false;

    /**
     * @var bool
     */
    private $addLazyLoadIframe = false;

    /**
     * @var bool
     */
    private $addLazyLoadVideo = false;

    /**
     * @var bool
     */
    private $addLazyLoadAudio = false;

    /**
     * @var bool
     */
    private $addImageSizes = false;

    /**
     * @var bool
     */
    private $minifyJson = false;

    /**
     * @var bool
     */
    private $decodeImgAsync = false;

    /**
     * @var bool
     */
    private $minifySvg = false;

    /**
     * @var bool
     */
    private $optimizeNoopener = false;

    /**
     * @var string
     */
    private $imgUrl;

    /**
     * @var string
     */
    private $imgUri;

    /**
     * @var array
     */
    private $extension = [];

    public function getHtml(): string
    {
        return $this->html;
    }

    public function setHtml(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function build(): self
    {
        if (empty($this->html)) {
            return $this;
        }

        $domDocument = new DOMDocument('1.0', 'UTF-8');

        $previous = libxml_use_internal_errors(true);
        $domDocument->loadHTML('<?xml encoding="utf-8"?>' . $this->html);

        $this->parseByImgTag($domDocument);
        $this->parseByIframeTag($domDocument);
        $this->parseByVideoTag($domDocument);
        $this->parseByAudioTag($domDocument);
        $this->parseByTargetTag($domDocument);
        $this->parseByScriptTag($domDocument);
        $this->parseByATag($domDocument);

        $domDocument->recover = true;
        $this->html = '<!DOCTYPE html>' . $domDocument->saveHTML($domDocument->documentElement);

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $this;
    }

    private function parseByImgTag(DOMDocument $domDocument): void
    {
        $doOptimizeWebp = !empty($this->extension);

        $domNodeList = $domDocument->getElementsByTagName('img');

        if ($doOptimizeWebp) {
            $webP = new WebP();
        }

        foreach ($domNodeList as $singleDomNodeList) {
            if ($this->addImageSizes) {
                $this->addImgSizes($singleDomNodeList);
            }

            if ($doOptimizeWebp) {
                $this->convertImgToWebp($singleDomNodeList, $webP);
            }

            if ($this->decodeImgAsync) {
                $this->decodeImgAsync($singleDomNodeList);
            }

            if ($this->addLazyLoadImg) {
                $this->lazyLoad($singleDomNodeList);
            }

            if ($this->minifySvg) {
                $this->minifySvg($singleDomNodeList);
            }
        }
    }

    private function addImgSizes(DOMElement $domElement): void
    {
        $src = $domElement->getAttribute('src');

        $heightAtt = $domElement->getAttribute('height');

        $widthAtt = $domElement->getAttribute('width');

        if (!empty($widthAtt) && !empty($heightAtt)) {
            return;
        }

        try {
            $image = (new FasterImage())->batch([$src]);
        } catch (Exception $exception) {
            LogService::error($exception->getMessage(), $exception->getTrace());

            return;
        }

        [$width, $height] = current($image)['size'];

        if (empty($width)) {
            return;
        }

        if (empty($height)) {
            return;
        }

        if (empty($widthAtt) && empty($heightAtt)) {
            $domElement->setAttribute('width', (string)$width);

            $domElement->setAttribute('height', (string)$height);

            return;
        }

        if (empty($heightAtt)) {
            $ratio = $height / $width;

            $domElement->setAttribute('height', (string)((float)$widthAtt * $ratio));
        } else {
            $ratio = $width / $height;

            $domElement->setAttribute('width', (string)((float)$heightAtt * $ratio));
        }
    }

    private function convertImgToWebp(DOMElement $domElement, WebP $webP): void
    {
        $options = [
            'fail' => 'throw',
            'reconvert' => false,
            'serve-original' => false,
            'show-report' => false,
            'suppress-warnings' => true,
            'serve-image' => [
                'headers' => [
                    'cache-control' => true,
                    'content-length' => true,
                    'content-type' => true,
                    'expires' => false,
                    'last-modified' => true,
                    'vary-accept' => false,
                ],
                'cache-control-header' => 'public, max-age=31536000',
            ],
            'redirect-to-self-instead-of-serving' => false,
            'png' => [
                'encoding' => 'auto',
                'quality' => (int)Configuration::get('PP_CONVERT_PNG_TO_WEBP_QUALITY'),
                'sharp-yuv' => true,
            ],
            'jpeg' => [
                'encoding' => 'auto',
                'quality' => (int)Configuration::get('PP_CONVERT_JPEG_TO_WEBP_QUALITY'),
                'auto-limit' => true,
                'sharp-yuv' => true,
            ],
        ];

        $attributes = [
            'src',
            'data-src',
            'data-image-medium-src',
            'data-image-large-src',
            'data-full-size-image-url',
            'data-thumb',
            'srcset',
        ];

        foreach ($attributes as $attribute) {
            $src = $domElement->getAttribute($attribute);

            if ($src === "") {
                continue;
            }

            if ($attribute === 'srcset') {
                $re = '/(https?:\/\/.*\.(?:png|jpe?g))/mU';

                preg_match_all($re, $src, $matches);

                foreach ($matches[0] as $match) {
                    $srcNoParams = $this->removeParams($match);

                    $ext = $this->getPathExtension($srcNoParams);

                    if (!empty($srcNoParams) && in_array($ext, $this->extension, true)) {
                        $absoluteLink = LinkService::createAbsoluteLink($srcNoParams);

                        $relativeUrl = LinkService::createRelativeLink($absoluteLink);

                        $imgCacheFile = urldecode(Config::getImgCachePath() . $this->convertExtensionToWebp($relativeUrl));

                        if (!file_exists($imgCacheFile)) {
                            PathService::createPath(dirname($imgCacheFile));

                            try {
                                $webP->create($absoluteLink, $imgCacheFile, $options);
                            } catch (PerformanceProInvalidResourceException $performanceProInvalidResourceException) {
                                LogService::error(
                                    $performanceProInvalidResourceException->getMessage(),
                                    $performanceProInvalidResourceException->getTrace()
                                );
                            }
                        }

                        $newSrc = LinkService::createNormalizedLink(
                            $this->imgUrl . $this->convertExtensionToWebp($relativeUrl)
                        );

                        $src = str_replace($match, $newSrc, $src);
                    }
                }

                $domElement->setAttribute($attribute, $src);

                continue;
            }

            $srcNoParams = $this->removeParams($src);

            $ext = $this->getPathExtension($srcNoParams);

            if (!empty($srcNoParams) && in_array($ext, $this->extension, true)) {
                $absoluteLink = LinkService::createAbsoluteLink($srcNoParams);

                $relativeUrl = LinkService::createRelativeLink($absoluteLink);

                $imgCacheFile = urldecode(Config::getImgCachePath() . $this->convertExtensionToWebp($relativeUrl));

                if (!file_exists($imgCacheFile)) {
                    PathService::createPath(dirname($imgCacheFile));

                    try {
                        $webP->create($absoluteLink, $imgCacheFile, $options);
                    } catch (PerformanceProInvalidResourceException $performanceProInvalidResourceException) {
                        LogService::error(
                            $performanceProInvalidResourceException->getMessage(),
                            $performanceProInvalidResourceException->getTrace()
                        );
                    }
                }

                $newSrc = LinkService::createNormalizedLink(
                    $this->imgUrl . $this->convertExtensionToWebp($relativeUrl)
                );

                $domElement->setAttribute($attribute, $newSrc);

                $domElement->setAttribute('onerror', sprintf("this.onerror=null; this.%s='%s'", $attribute, $src));
            }
        }
    }

    private function removeParams(string $src): string
    {
        return (string)strtok($src, '?');
    }

    private function getPathExtension(string $path): string
    {
        return mb_strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    private function convertExtensionToWebp(string $link): string
    {
        $str = (int)mb_strrpos($link, '.');

        return mb_substr($link, 0, $str) . '.webp';
    }

    private function decodeImgAsync(DOMElement $domElement): void
    {
        $domElement->setAttribute('decoding', 'async');
    }

    private function lazyLoad(DOMElement $domElement): void
    {
        $hasLoadingAttribute = $domElement->getAttribute('loading');

        if (!$hasLoadingAttribute) {
            $domElement->setAttribute('loading', 'lazy');
        }
    }

    private function minifySvg(DOMElement $domElement): void
    {
        $src = $domElement->getAttribute('src');

        $src = $this->removeParams($src);

        if ('svg' !== $this->getPathExtension($src)) {
            return;
        }

        if (!empty($src)) {
            $svgImgUrl = $this->imgUri . parse_url(LinkService::createAbsoluteLink($src), PHP_URL_PATH);

            if (!file_exists($svgImgUrl)) {
                (new SVG())->create(LinkService::createAbsoluteLink($src), $svgImgUrl);
            }

            $relativeUrl = LinkService::createRelativeLink($src);

            $newSrc = $this->imgUrl . $relativeUrl;

            $domElement->setAttribute('src', $newSrc);
        }
    }

    private function parseByIframeTag(DOMDocument $domDocument): void
    {
        $domNodeList = $domDocument->getElementsByTagName('iframe');

        foreach ($domNodeList as $singleDomNodeList) {
            if ($this->addLazyLoadIframe) {
                $this->lazyLoad($singleDomNodeList);
            }
        }
    }

    private function parseByVideoTag(DOMDocument $domDocument): void
    {
        $domNodeList = $domDocument->getElementsByTagName('video');

        foreach ($domNodeList as $singleDomNodeList) {
            if ($this->addLazyLoadVideo) {
                $this->lazyLoadVideoAndAudio($singleDomNodeList);
            }
        }
    }

    private function parseByAudioTag(DOMDocument $domDocument): void
    {
        $domNodeList = $domDocument->getElementsByTagName('audio');

        foreach ($domNodeList as $singleDomNodeList) {
            if ($this->addLazyLoadAudio) {
                $this->lazyLoadVideoAndAudio($singleDomNodeList);
            }
        }
    }

    private function lazyLoadVideoAndAudio(DOMElement $domElement): void
    {
        $domElement->setAttribute('preload', 'none');
    }

    private function parseByTargetTag(DOMDocument $domDocument): void
    {
        if ($this->optimizeNoopener) {
            $domNodeList = $domDocument->getElementsByTagName('target');

            foreach ($domNodeList as $singleDomNodeList) {
                $singleDomNodeList->setAttribute('rel', 'noopener');
            }
        }
    }

    private function parseByScriptTag(DOMDocument $domDocument): void
    {
        $jsThemeCache = Configuration::get('PS_JS_THEME_CACHE');

        $domNodeList = $domDocument->getElementsByTagName('script');

        if (!($jsThemeCache && $this->loadScriptAsync)) {
            return;
        }

        if (!$this->minifyJson) {
            return;
        }

        foreach ($domNodeList as $singleDomNodeList) {
            if ($this->loadScriptAsync) {
                $src = $singleDomNodeList->getAttribute('src');

                if ($src === $this->getJavascriptLink()) {
                    $singleDomNodeList->setAttribute('defer', '');
                }
            }

            if ($this->minifyJson) {
                $type = $singleDomNodeList->getAttribute('type');

                if ('application/ld+json' === $type) {
                    $singleDomNodeList->nodeValue = json_encode(json_decode($singleDomNodeList->nodeValue));
                }
            }
        }
    }

    private function getJavascriptLink()
    {
        return ContextService::getController()->getJavascript()['bottom']['external']['bottom-js-ccc']['uri'];
    }

    private function parseByATag(DOMDocument $domDocument): void
    {
        if ($this->optimizeNoopener) {
            $domNodeList = $domDocument->getElementsByTagName('a');

            foreach ($domNodeList as $singleDomNodeList) {
                $href = $singleDomNodeList->getAttribute('href');

                if ($this->isExternalLink($href)) {
                    $singleDomNodeList->setAttribute('rel', 'noopener');
                }
            }
        }
    }

    private function isExternalLink(string $url): bool
    {
        $components = parse_url($url);

        return !empty($components['host']) && strcasecmp($components['host'], Tools::getHttpHost());
    }

    public function doMinifySvg(bool $opt = false): self
    {
        $this->minifySvg = $opt;

        return $this;
    }

    public function doDecodeImgAsync(bool $opt): self
    {
        $this->decodeImgAsync = $opt;

        return $this;
    }

    public function doLazyLoadImg(bool $opt): self
    {
        $this->addLazyLoadImg = $opt;

        return $this;
    }

    public function doLazyLoadIframe(bool $opt): self
    {
        $this->addLazyLoadIframe = $opt;

        return $this;
    }

    public function doLazyLoadVideo(bool $opt): self
    {
        $this->addLazyLoadVideo = $opt;

        return $this;
    }

    public function doLazyLoadAudio($opt): self
    {
        $this->addLazyLoadAudio = $opt;

        return $this;
    }
    public function doOptimizeNoopener(bool $opt): self
    {
        $this->optimizeNoopener = $opt;

        return $this;
    }

    public function doLoadScriptAsync(bool $opt): self
    {
        $this->loadScriptAsync = $opt;

        return $this;
    }

    public function doAddImageSizes(bool $opt): self
    {
        $this->addImageSizes = $opt;

        return $this;
    }

    public function doMinifyJson(bool $opt): self
    {
        $this->minifyJson = $opt;

        return $this;
    }

    public function setImgLink(string $imgUrl): self
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    public function doConvertJpgToWebp(bool $doConvertJpgToWebp): self
    {
        if ($doConvertJpgToWebp) {
            $this->extension[] = 'jpg';
        }

        return $this;
    }

    public function doConvertPngToWebp(bool $doConvertPngToWebp): self
    {
        if ($doConvertPngToWebp) {
            $this->extension[] = 'png';
        }

        return $this;
    }

    public function setImgPath(string $imgUri): self
    {
        $this->imgUri = $imgUri;

        return $this;
    }
}
