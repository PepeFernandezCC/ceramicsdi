<?php
/**
 * Creative Slider - Responsive Slideshow Module
 * https://creativeslider.webshopworks.com
 *
 * @author    WebshopWorks <info@webshopworks.com>
 * @copyright 2015-2025 WebshopWorks
 * @license   One Domain Licence
 *
 * Not allowed to resell or redistribute this software
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class LsImportUtil
{
    // Counts the number of sliders imported.
    public $sliderCount = 0;

    // Database ID of the lastly imported slider.
    public $lastImportId;

    // The managed ZipArchieve instance.
    private $zip;

    // Target folders
    private $targetDir;
    private $targetURL;
    private $tmpDir;

    // Imported images
    private $imported = [];

    // Accepts $_FILES
    public function __construct($archive, $name = null)
    {
        // Attempt to workaround memory limit & execution time issues
        @ini_set('max_execution_time', 0);

        if (empty($name)) {
            $name = $archive;
        }

        // TODO: check file extension to support old import method
        $type = ls_check_filetype(basename($name), [
            'zip' => 'application/zip',
            'json' => 'application/json',
        ]);

        // Check for ZIP
        if (!empty($type['ext']) && 'zip' === $type['ext']) {
            if (class_exists('ZipArchive')) {
                // Extract ZIP
                $this->zip = new ZipArchive();
                if ($this->zip->open($archive)) {
                    if ($this->unpack()) {
                        // Uploaded folders
                        foreach (glob($this->tmpDir . '/*', GLOB_ONLYDIR) as $dir) {
                            $this->imported = [];

                            if (!isset(${'_POST'}['skip_images'])) {
                                $this->uploadMedia($dir);
                            }

                            if (file_exists($dir . '/settings.json')) {
                                $this->lastImportId = $this->addSlider($dir . '/settings.json');
                            }
                        }

                        // Clean up
                        $this->tmpDir && Tools::deleteDirectory($this->tmpDir);
                    }

                    // Close ZIP
                    $this->zip->close();
                }
            } else {
                ls_redirect('?controller=AdminLayerSlider&error=1&message=exportZipError');
            }
        } elseif (!empty($type['ext']) && 'json' === $type['ext']) {
            // Check for JSON
            $data = call_user_func('file_get_contents', $archive);
            // Get decoded file data
            if ($decoded = base64_decode($data, true)) {
                $parsed = json_decode($decoded, true);
            } else {
                // Since v5.1.1
                $parsed = [json_decode($data, true)];
            }

            // Iterate over imported sliders
            if (is_array($parsed)) {
                // Import sliders
                foreach ($parsed as $item) {
                    // Increment the slider counter
                    ++$this->sliderCount;

                    // Fix for export issue in v4.6.4
                    if (is_string($item)) {
                        $item = json_decode($item, true);
                    }

                    $this->lastImportId = LsSliders::add($item['properties']['title'], $item);
                }
            }
        }
    }

    public function unpack()
    {
        // Get uploads folder
        $uploads = ls_upload_dir();

        // Check if /uploads dir is writable
        if (is_writable($uploads['basedir'])) {
            // Get target folders
            $this->targetDir = $targetDir = $uploads['basedir'] . 'layerslider';
            $this->targetURL = $uploads['baseurl'] . 'layerslider';
            $this->tmpDir = $tmpDir = $uploads['basedir'] . 'layerslider/tmp';

            // Create necessary folders under /uploads
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755);
            }

            // Unpack archive
            if ($this->zip->extractTo($tmpDir)) {
                return true;
            }
        }

        return false;
    }

    public function uploadMedia($dir = null)
    {
        // Check provided data
        if (empty($dir) || !is_string($dir) || !file_exists($dir . '/uploads')) {
            return false;
        }

        // Create folder if it isn't exists already
        $targetDir = $this->targetDir . '/' . basename($dir);
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755);
        }

        // Iterate through directory
        foreach (glob($dir . '/uploads/*') as $filePath) {
            $fileName = ls_sanitize_file_name(basename($filePath));
            $targetFile = $targetDir . '/' . $fileName;
            // Validate media
            $filetype = ls_check_filetype($fileName, null);
            if (!empty($filetype['ext']) && 'php' !== $filetype['ext']) {
                // Move item to place
                rename($filePath, $targetFile);

                $this->imported[$fileName] = [
                    'id' => 0,
                    'url' => $this->targetURL . '/' . basename($dir) . '/' . $fileName,
                ];
            }
        }

        return true;
    }

    public function addSlider($file)
    {
        // Increment the slider counter
        ++$this->sliderCount;

        // Get slider data and title
        $data = json_decode(call_user_func('file_get_contents', $file), true);
        $title = $data['properties']['title'];
        $slug = !empty($data['properties']['slug']) ? $data['properties']['slug'] : '';

        // Import Google Fonts used in slider
        if (isset($data['googlefonts'])) {
            $this->addGoogleFonts($data);
            unset($data['googlefonts']);
        }

        // Slider Preview
        if (!empty($data['meta']) && !empty($data['meta']['preview'])) {
            $data['meta']['previewId'] = $this->attachIDForImage($data['meta']['preview']);
            $data['meta']['preview'] = $this->attachURLForImage($data['meta']['preview']);
        }

        // Slider settings
        if (!empty($data['properties']['backgroundimage'])) {
            $data['properties']['backgroundimageId'] = $this->attachIDForImage($data['properties']['backgroundimage']);
            $data['properties']['backgroundimage'] = $this->attachURLForImage($data['properties']['backgroundimage']);
        }

        if (!empty($data['properties']['yourlogo'])) {
            $data['properties']['yourlogoId'] = $this->attachIDForImage($data['properties']['yourlogo']);
            $data['properties']['yourlogo'] = $this->attachURLForImage($data['properties']['yourlogo']);
        }

        // Slides
        if (!empty($data['layers']) && is_array($data['layers'])) {
            foreach ($data['layers'] as &$slide) {
                if (!empty($slide['properties']['background'])) {
                    $slide['properties']['backgroundId'] = $this->attachIDForImage($slide['properties']['background']);
                    $slide['properties']['background'] = $this->attachURLForImage($slide['properties']['background']);
                    $slide['properties']['backgroundThumb'] = $slide['properties']['background'];
                }

                if (!empty($slide['properties']['thumbnail'])) {
                    $slide['properties']['thumbnailId'] = $this->attachIDForImage($slide['properties']['thumbnail']);
                    $slide['properties']['thumbnail'] = $this->attachURLForImage($slide['properties']['thumbnail']);
                    $slide['properties']['thumbnailThumb'] = $slide['properties']['thumbnail'];
                }

                // Layers
                if (!empty($slide['sublayers']) && is_array($slide['sublayers'])) {
                    foreach ($slide['sublayers'] as &$layer) {
                        if (!empty($layer['image'])) {
                            $layer['imageId'] = $this->attachIDForImage($layer['image']);
                            $layer['image'] = $this->attachURLForImage($layer['image']);
                            $layer['imageThumb'] = $layer['image'];
                        }

                        if (!empty($layer['poster'])) {
                            $layer['posterId'] = $this->attachIDForImage($layer['poster']);
                            $layer['poster'] = $this->attachURLForImage($layer['poster']);
                            $layer['posterThumb'] = $layer['poster'];
                        }
                    }
                }
            }
        }

        // Add slider
        return LsSliders::add($title, $data, $slug);
    }

    public function addGoogleFonts($data)
    {
        // Get current Google Fonts
        $googleFonts = ls_get_option('ls-google-fonts', []);
        $fontNames = [];

        // Gather used font names
        foreach ($googleFonts as $item) {
            $font = explode(':', $item['param']);
            $fontNames[$font[0]] = $item;
        }

        // Merge google fonts
        foreach ($data['googlefonts'] as $font) {
            // If no font-weight is specified, default to regular 400
            // since Google Fonts do exactly this as well.
            if (false === strpos(trim($font['param']), ':')) {
                $font['param'] .= ':regular';
            }

            list($family, $weights) = explode(':', $font['param']);

            // New font, just add
            if (!isset($fontNames[$family])) {
                $fontNames[$family] = $font;

            // Existing font, merge variants
            } else {
                $w = [];

                foreach (explode(',', $weights) as $weight) {
                    $w[$weight] = true;
                }

                // If no font-weight is specified, default to regular 400
                // since Google Fonts do exactly this as well.
                if (false === strpos(trim($fontNames[$family]['param']), ':')) {
                    $fontNames[$family]['param'] .= ':regular';
                }

                list($family, $weights) = explode(':', $fontNames[$family]['param']);
                foreach (explode(',', $weights) as $weight) {
                    $w[$weight] = true;
                }

                $fontNames[$family] = $font;
                $fontNames[$family]['param'] = $family . ':' . implode(',', array_keys($w));
            }
        }

        // Update Google Fonts
        $googleFonts = [];
        foreach ($fontNames as $font) {
            $googleFonts[] = $font;
        }

        ls_update_option('ls-google-fonts', $googleFonts);
    }

    public function attachURLForImage($file = '')
    {
        if (isset($this->imported[basename($file)])) {
            return $this->imported[basename($file)]['url'];
        }

        return $file;
    }

    public function attachIDForImage($file = '')
    {
        if (isset($this->imported[basename($file)])) {
            return $this->imported[basename($file)]['id'];
        }

        return '';
    }
}
