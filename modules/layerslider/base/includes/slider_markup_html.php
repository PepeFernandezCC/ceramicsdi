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

/** @var int $sliderID */
/** @var array $slider */
/** @var array $slides */
/** @var array $lsContainer */
/** @var array $lsPlugins */
/** @var array $lsMarkup */

// Get slider style
$sliderStyleAttr = [];
$sliderStyleAttr[] = 'max-width:100%;';
$sliderStyleAttr[] = 'width:' . layerslider_check_unit($slides['properties']['props']['width']) . ';';

if ((!empty($slides['properties']['attrs']['type']) && 'fullsize' === $slides['properties']['attrs']['type']) && (empty($slides['properties']['attrs']['fullSizeMode']) || 'fitheight' !== $slides['properties']['attrs']['fullSizeMode'])) {
    $sliderStyleAttr[] = 'height:100vh;';
} else {
    $sliderStyleAttr[] = 'height:' . layerslider_check_unit($slides['properties']['props']['height']) . ';';
}

if (!empty($slides['properties']['props']['maxwidth'])) {
    $sliderStyleAttr[] = 'max-width:' . layerslider_check_unit($slides['properties']['props']['maxwidth']) . ';';
}

$sliderStyleAttr[] = 'margin:0 auto;';
if (isset($slides['properties']['props']['sliderStyle'])) {
    $sliderStyleAttr[] = $slides['properties']['props']['sliderStyle'];
}

// Before slider content hook
if (ls_has_action('layerslider_before_slider_content')) {
    ls_do_action('layerslider_before_slider_content');
}

$customClasses = '';
if (!empty($slides['properties']['props']['sliderclass'])) {
    $customClasses = ' ' . $slides['properties']['props']['sliderclass'];
}

$enhancedLazyLoad = false;
if (!empty($slides['properties']['props']['enhancedLazyLoad'])) {
    $enhancedLazyLoad = true;
}

// Start of slider container
$lsContainer[] = '<div id="' . $sliderID . '" class="ls-wp-container fitvidsignore' . $customClasses . '" style="' . implode('', $sliderStyleAttr) . '">';

// Add slides
if (!empty($slider['slides']) && is_array($slider['slides'])) {
    $now = time();
    foreach ($slider['slides'] as $slidekey => $slide) {
        // Skip this slide?
        if (!empty($slide['props']['skip'])
            || !empty($slide['props']['schedule_start']) && strtotime($slide['props']['schedule_start']) > $now
            || !empty($slide['props']['schedule_end']) && strtotime($slide['props']['schedule_end']) < $now
        ) {
            continue;
        }

        // Get slide attributes
        $slideId = !empty($slide['props']['id']) ? ' id="' . $slide['props']['id'] . '"' : '';
        $slideAttrs = !empty($slide['attrs']) ? ls_array_to_attr($slide['attrs']) : '';
        $postContent = false;

        // Check for the origami plugin
        if (!empty($slide['attrs']['transitionorigami'])) {
            $lsPlugins[] = 'origami';
        }

        // Post content
        // if (!isset($slide['props']['post_content']) || $slide['props']['post_content']) {
        $queryArgs = [
            'post_status' => 'publish',
            'limit' => 1,
            'img_size' => null,
        ];

        if (isset($slide['props']['post_offset'])) {
            if (-1 == $slide['props']['post_offset']) {
                $slide['props']['post_offset'] = $slidekey;
            }

            $queryArgs['offset'] = $slide['props']['post_offset'];
        }

        if (!empty($slides['properties']['props']['post_type'][0])) {
            $queryArgs['post_type'] = $slides['properties']['props']['post_type'];
        }

        if (!empty($slides['properties']['props']['post_orderby'])) {
            $queryArgs['orderby'] = $slides['properties']['props']['post_orderby'];
        }

        if (!empty($slides['properties']['props']['post_order'])) {
            $queryArgs['order'] = $slides['properties']['props']['post_order'];
        }

        if (!empty($slides['properties']['props']['post_categories'][0])) {
            $queryArgs['category__in'] = $slides['properties']['props']['post_categories'];
        }

        if (!empty($slides['properties']['props']['post_tags'][0])) {
            $queryArgs['tag__in'] = $slides['properties']['props']['post_tags'];
        }

        if (!empty($slides['properties']['props']['post_tax_terms'])) {
            $queryArgs['img_size'] = $slides['properties']['props']['post_tax_terms'];
        }

        $postContent = LsPosts::find($queryArgs);
        // }

        // Start of slide
        $slideAttrs = !empty($slideAttrs) ? 'data-ls="' . $slideAttrs . '"' : '';
        $lsMarkup[] = '<div class="ls-slide"' . $slideId . ' ' . $slideAttrs . '>';

        // Add slide background
        if (!empty($slide['props']['background'])) {
            $lsBG = '';
            $alt = empty($slide['props']['alt']) ? 'Slide background' : $slide['props']['alt'];
            $title = empty($slide['props']['title']) ? '' : 'title="' . $slide['props']['title'] . '"';

            if (!empty($slide['props']['backgroundId'])) {
                $lsBG = ls_get_attachment_image($slide['props']['backgroundId'], 'full', false, ['class' => 'ls-bg']);
            } elseif ('[image-url]' == $slide['props']['background']) {
                $src = $postContent->getWithFormat($slide['props']['background']);

                if (is_object($postContent->post)) {
                    $attchID = ls_get_post_thumbnail_id($postContent->post->ID);
                    $lsBG = ls_get_attachment_image($attchID, 'full', false, ['class' => 'ls-bg']);
                }
            } else {
                $src = $slide['props']['background'];
            }

            if (!empty($lsBG)) {
                if ($enhancedLazyLoad) {
                    $lsBG = str_replace(' src="', ' data-src="', $lsBG);
                    // $lsBG = str_replace(' srcset="', ' data-srcset="', $lsBG);
                }

                $lsMarkup[] = $lsBG;
            } elseif (!empty($src)) {
                if ($enhancedLazyLoad) {
                    $lsMarkup[] = '<img data-src="' . $src . '" class="ls-bg" alt="' . $alt . '" ' . $title . '/>';
                } else {
                    $lsMarkup[] = '<img src="' . $src . '" class="ls-bg" alt="' . $alt . '" ' . $title . '/>';
                }
            }
        }

        // Add slide thumbnail
        if (!isset($slides['properties']['attrs']['thumbnailNavigation']) || 'disabled' != $slides['properties']['attrs']['thumbnailNavigation']) {
            if (!empty($slide['props']['thumbnail'])) {
                $src = !empty($slide['props']['thumbnailId']) ? ls_apply_filters('ls_get_image', $slide['props']['thumbnailId'], $slide['props']['thumbnail']) : $slide['props']['thumbnail'];

                if ($enhancedLazyLoad) {
                    $lsMarkup[] = '<img data-src="' . $src . '" class="ls-tn" alt="Slide thumbnail" />';
                } else {
                    $lsMarkup[] = '<img src="' . $src . '" class="ls-tn" alt="Slide thumbnail" />';
                }
            } elseif (empty($slide['props']['background'])) {
                $skin = isset($slides['properties']['attrs']['skin']) ? $slides['properties']['attrs']['skin'] : 'v6';
                $src = _MODULE_DIR_ . 'layerslider/views/img/layerslider/skins/' . (false !== strpos($skin, 'dark') ? 'nothumb-dark.png' : 'nothumb.png');

                if ($enhancedLazyLoad) {
                    $lsMarkup[] = '<img data-src="' . $GLOBALS['context']->link->getMediaLink($src) . '" class="ls-tn" alt="Slide thumbnail" />';
                } else {
                    $lsMarkup[] = '<img src="' . $GLOBALS['context']->link->getMediaLink($src) . '" class="ls-tn" alt="Slide thumbnail" />';
                }
            }
        }

        // Add layers
        if (!empty($slide['layers']) && is_array($slide['layers'])) {
            foreach ($slide['layers'] as $layerkey => $layer) {
                // Skip this slide?
                if (!empty($layer['props']['skip'])) {
                    continue;
                }

                unset($layerAttributes);
                unset($innerAttributes);
                $layerAttributes = ['style' => '', 'class' => 'ls-l'];
                $innerAttributes = ['style' => '', 'class' => ''];

                if (empty($layer['props']['url'])) {
                    $innerAttributes = &$layerAttributes;
                }
                isset($layer['props']['html']) or $layer['props']['html'] = '';

                // Get layer type
                $layer['props']['media'] = !empty($layer['props']['media']) ? $layer['props']['media'] : '';
                if (!empty($layer['props']['media'])) {
                    switch ($layer['props']['media']) {
                        case 'img':
                            $layer['props']['type'] = 'img';
                            break;
                        case 'button':
                            $layer['props']['type'] = 'span';
                            break;
                        case 'html':
                        case 'media':
                            $layer['props']['type'] = 'div';
                            break;
                        case 'post':
                            $layer['props']['type'] = 'div';
                            break;
                    }
                }

                // Post layer
                if (!empty($layer['props']['media']) && 'post' == $layer['props']['media']) {
                    $layer['props']['post_text_length'] = !empty($layer['props']['post_text_length']) ? $layer['props']['post_text_length'] : 0;
                    $layer['props']['html'] = $postContent->getWithFormat($layer['props']['html'], $layer['props']['post_text_length']);
                }

                // Skip image layer without src
                if ('img' == $layer['props']['type'] && empty($layer['props']['image'])) {
                    continue;
                }

                // Create layer
                $first = Tools::substr($layer['props']['html'], 0, 1);
                $last = Tools::substr($layer['props']['html'], -1);

                // Image layer
                $layerIMG = false;
                if ('img' == $layer['props']['type']) {
                    $src = $enhancedLazyLoad ? 'data-src' : 'src';

                    if (!empty($layer['props']['imageId'])) {
                        $layerIMG = ls_get_attachment_image((int) $layer['props']['imageId'], 'full', false, ['class' => 'ls-l']);
                    } elseif ('[image-url]' == $layer['props']['image']) {
                        if (is_object($postContent->post)) {
                            $attchID = ls_get_post_thumbnail_id($postContent->post->ID);
                            $layerIMG = ls_get_attachment_image($attchID, 'full', false, ['class' => 'ls-l']);
                        } else {
                            $innerAttributes[$src] = $postContent->getWithFormat($layer['props']['image']);
                        }
                    } else {
                        $innerAttributes[$src] = $layer['props']['image'];

                        if (!empty($layer['props']['alt'])) {
                            $innerAttributes['alt'] = $layer['props']['alt'];
                        } else {
                            $innerAttributes['alt'] = '';
                        }
                    }
                }

                if ('post' == $layer['props']['media'] && ('<' == $first && '>' == $last)) {
                    $type = $layer['props']['html'];
                } else {
                    if (!empty($layerIMG) && $enhancedLazyLoad) {
                        $layerIMG = str_replace(' src="', ' data-src="', $layerIMG);
                        // $layerIMG = str_replace(' srcset="', ' data-srcset="', $layerIMG);
                    }

                    $type = !empty($layerIMG) ? $layerIMG : '<' . $layer['props']['type'] . '>';
                }

                if (!empty($layer['props']['url'])) {
                    $el = LsQuery::newDocumentHTML('<a>');
                    if ('[url]' == $layer['props']['url']) {
                        $layer['props']['url'] = $postContent->getWithFormat($layer['props']['url']);
                    }
                    $layerAttributes['href'] = $layer['props']['url'];
                    if (!empty($layer['props']['target'])) {
                        $layerAttributes['target'] = $layer['props']['target'];
                    }

                    $inner = $el->append($type)->children();
                } else {
                    $el = $inner = LsQuery::newDocumentHTML($type);
                }

                // HTML attributes
                $layerAttributes['class'] = 'ls-l';
                if (!empty($layer['props']['id'])) {
                    $innerAttributes['id'] = $layer['props']['id'];
                }
                if (!empty($layer['props']['class'])) {
                    $innerAttributes['class'] .= ' ' . $layer['props']['class'];
                }
                if (!empty($layer['props']['url'])) {
                    if (!empty($layer['props']['rel'])) {
                        $layerAttributes['rel'] = $layer['props']['rel'];
                    }
                    if (!empty($layer['props']['title'])) {
                        $layerAttributes['title'] = $layer['props']['title'];
                    }
                } else {
                    if (!empty($layer['props']['title'])) {
                        $innerAttributes['title'] = $layer['props']['title'];
                    }
                }

                if (isset($layer['attrs']) && isset($layer['props']['transition'])) {
                    $layerAttributes['data-ls'] = ls_array_to_attr($layer['attrs']);
                } elseif (isset($layer['attrs'])) {
                    $layerAttributes['style'] .= ls_array_to_attr($layer['attrs']);
                }

                if (!empty($layer['props']['style'])) {
                    if (';' != Tools::substr($layer['props']['style'], -1)) {
                        $layer['props']['style'] .= ';';
                    }
                    $innerAttributes['style'] .= preg_replace('/\s\s+/', ' ', $layer['props']['style']);
                }

                if (!empty($layer['props']['wordwrap']) || !empty($layer['props']['styles']['wordwrap'])) {
                    $innerAttributes['style'] .= 'white-space: normal;';
                }

                if (!empty($layer['props']['styles'])) {
                    unset($layer['props']['styles']['wordwrap']);
                    $innerAttributes['style'] .= ls_array_to_attr($layer['props']['styles'], 'css');
                }

                // Text / HTML layer
                if ('post' != $layer['props']['media'] || ('<' != $first && '>' != $last)) {
                    $inner->html(stripslashes($layer['props']['html']));
                }

                // Rewrite Youtube/Vimeo iframe src to data-src
                $video = $inner->find('iframe[src*="youtube-nocookie.com"], iframe[src*="youtube.com"], iframe[src*="youtu.be"], iframe[src*="player.vimeo"]');
                if ($video->length) {
                    $video->attr('data-src', $video->attr('src'));
                    $video->removeAttr('src');
                }

                // Device dependent responsive classes
                if (!empty($layer['props']['hide_on_desktop'])) {
                    $layerAttributes['class'] .= ' ls-hide-desktop';
                }

                if (!empty($layer['props']['hide_on_tablet'])) {
                    $layerAttributes['class'] .= ' ls-hide-tablet';
                }

                if (!empty($layer['props']['hide_on_phone'])) {
                    $layerAttributes['class'] .= ' ls-hide-phone';
                }

                $el->attr($layerAttributes);
                $inner->attr($innerAttributes);

                if (!empty($layer['props']['outerAttributes'])) {
                    foreach ($layer['props']['outerAttributes'] as $key => $val) {
                        if ('class' === $key) {
                            $el->addClass($val);
                        }
                        $el->attr($key, $val);
                    }
                }

                if (!empty($layer['props']['innerAttributes'])) {
                    foreach ($layer['props']['innerAttributes'] as $key => $val) {
                        if ('class' === $key) {
                            $inner->addClass($val);
                        }
                        $inner->attr($key, $val);
                    }
                }

                $lsMarkup[] = $el;
                LsQuery::unloadDocuments();
            }
        }

        // Link this slide
        if (!empty($slide['props']['linkUrl'])) {
            if (!empty($slide['props']['linkTarget'])) {
                $target = ' target="' . $slide['props']['linkTarget'] . '"';
            } else {
                $target = '';
            }

            if ('[url]' == $slide['props']['linkUrl']) {
                $slide['props']['linkUrl'] = $postContent->getWithFormat($slide['props']['linkUrl']);
            }

            $linkClass = 'ls-link';
            if (empty($slide['props']['linkType']) || 'over' === $slide['props']['linkType']) {
                $linkClass .= ' ls-link-on-top';
            }

            $lsMarkup[] = '<a href="' . $slide['props']['linkUrl'] . '"' . $target . ' class="' . $linkClass . '"></a>';
        }

        // End of slide
        $lsMarkup[] = '</div>';
    }
}

// End of slider container
$lsMarkup[] = '</div>';

// After slider content hook
if (ls_has_action('layerslider_after_slider_content')) {
    ls_do_action('layerslider_after_slider_content');
}
