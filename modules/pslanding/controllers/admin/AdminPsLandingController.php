<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'pslanding/classes/PsLandingModel.php';

class AdminPsLandingController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'pslanding';
        $this->className = 'PsLandingModel';
        $this->identifier = 'id_pslanding';
        $this->lang = true;
        $this->bootstrap = true;

        parent::__construct();

        $this->_select = 'pl.title';
        $this->_join = ' LEFT JOIN `' . _DB_PREFIX_ . 'pslanding_lang` pl ON (a.id_pslanding = pl.id_pslanding AND pl.id_lang = ' . (int)$this->context->language->id . ')';

        $this->fields_list = [
            'id_pslanding' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'title' => [
                'title' => $this->l('Title'),
                'filter_key' => 'pl!title',
            ],
            'template' => [
                'title' => $this->l('Template'),
            ],
            'active' => [
                'title' => $this->l('Active'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
            ],
        ];
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJS($this->module->getPathUri() . 'views/assets/js/back.js');
        // $this->addCSS($this->module->getPathUri().'views/assets/css/back.css');
    }

    public function renderForm()
    {
        $templates = [
            ['id' => 'landing-default', 'name' => 'Landing default'],
            ['id' => 'landing-simple',  'name' => 'Landing simple'],
        ];

        // Para previews: al editar, el objeto está cargado.
        $heroFilename   = (Validate::isLoadedObject($this->object) && is_string($this->object->hero_media)) ? $this->object->hero_media : '';
        $b2Filename     = (Validate::isLoadedObject($this->object) && is_string($this->object->block2_image)) ? $this->object->block2_image : '';
        $b3Filename     = (Validate::isLoadedObject($this->object) && is_string($this->object->block3_image)) ? $this->object->block3_image : '';
        $b4Filename     = (Validate::isLoadedObject($this->object) && is_string($this->object->block4_image)) ? $this->object->block4_image : '';


        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Landing page'),
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Template'),
                    'name' => 'template',
                    'options' => [
                        'query' => $templates,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Colección (Feature 57)'),
                    'name' => 'id_feature_value_collection',
                    'options' => [
                        'query' => $this->getCollectionOptions(),
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Categoría'),
                    'name' => 'id_category',
                    'options' => [
                        'query' => $this->getCategoryPathOptions(),
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'lang' => true,
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Friendly URL (slug)'),
                    'name' => 'slug',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Solo letras, números, guiones. Ej: coleccion-primavera'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Titulo Slider'),
                    'name' => 'hero_title',
                    'lang' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Texto Slider'),
                    'name' => 'hero_subtitle',
                    'lang' => true,
                    'autoload_rte' => true,
                ],

                // HERO MEDIA
                [
                    'type' => 'html',
                    'label' => $this->l('Archivo actual (Slider)'),
                    'name' => 'hero_media_preview',
                    'html_content' => $this->renderMediaPreviewHtml((string)$heroFilename),
                ],
                [
                    'type'  => 'file',
                    'label' => $this->l('Archivo Slider (image/video)'),
                    'name'  => 'hero_media',
                    'desc'  => $this->l('Sube una imagen o un video (mp4/webm) o imagen (jpg/png/webp). Si no subes nada, se mantiene el actual.'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Eliminar archivo actual (Slider)'),
                    'name' => 'hero_media_delete',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'hero_media_delete_on', 'value' => 1, 'label' => $this->l('Sí')],
                        ['id' => 'hero_media_delete_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                ],
                [
                    'type' => 'hidden',
                    'name' => 'hero_media_old',
                ],

                // BLOCK 2
                [
                    'type' => 'text',
                    'label' => $this->l('Titulo Bloque 1'),
                    'name' => 'block2_title',
                    'lang' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Texto Bloque 1'),
                    'name' => 'block2_text',
                    'lang' => true,
                    'autoload_rte' => true,
                ],

                [
                    'type' => 'html',
                    'label' => $this->l('Archivo actual (Bloque 1)'),
                    'name' => 'block2_image_preview',
                    'html_content' => $this->renderMediaPreviewHtml((string)$b2Filename),
                ],
                [
                    'type'  => 'file',
                    'label' => $this->l('Archivo Bloque 1'),
                    'name'  => 'block2_image',
                    'desc'  => $this->l('Sube una imagen o un video (mp4/webm) o imagen (jpg/png/webp). Si no subes nada, se mantiene el actual.'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Eliminar archivo actual (Bloque 1)'),
                    'name' => 'block2_image_delete',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'block2_image_delete_on', 'value' => 1, 'label' => $this->l('Sí')],
                        ['id' => 'block2_image_delete_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                ],
                [
                    'type' => 'hidden',
                    'name' => 'block2_image_old',
                ],

                // BLOCK 3 (tpl-simple)
                [
                    'type' => 'text',
                    'label' => $this->l('Titulo Bloque 2'),
                    'name' => 'block3_title',
                    'lang' => true,
                    'form_group_class' => 'js-tpl tpl-simple',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Texto Bloque 2'),
                    'name' => 'block3_text',
                    'lang' => true,
                    'autoload_rte' => true,
                    'form_group_class' => 'js-tpl tpl-simple',
                ],

                [
                    'type' => 'html',
                    'label' => $this->l('Archivo actual (Bloque 2)'),
                    'name' => 'block3_image_preview',
                    'html_content' => $this->renderMediaPreviewHtml((string)$b3Filename),
                    'form_group_class' => 'js-tpl tpl-simple',
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Archivo Bloque 2'),
                    'name' => 'block3_image',
                    'desc' => $this->l('Sube una imagen o un video (mp4/webm) o imagen (jpg/png/webp). Si no subes nada, se mantiene el actual.'),
                    'form_group_class' => 'js-tpl tpl-simple',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Eliminar archivo actual (Bloque 2)'),
                    'name' => 'block3_image_delete',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'block3_image_delete_on', 'value' => 1, 'label' => $this->l('Sí')],
                        ['id' => 'block3_image_delete_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                    'form_group_class' => 'js-tpl tpl-simple',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'block3_image_old',
                    'form_group_class' => 'js-tpl tpl-simple',
                ],

                // BLOCK 4 (tpl-simple)
                [
                    'type' => 'text',
                    'label' => $this->l('Titulo Bloque 3'),
                    'name' => 'block4_title',
                    'lang' => true,
                    'form_group_class' => 'js-tpl tpl-simple',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Texto Bloque 3'),
                    'name' => 'block4_text',
                    'lang' => true,
                    'autoload_rte' => true,
                    'form_group_class' => 'js-tpl tpl-simple',
                ],

                [
                    'type' => 'html',
                    'label' => $this->l('Archivo actual (Bloque 3)'),
                    'name' => 'block4_image_preview',
                    'html_content' => $this->renderMediaPreviewHtml((string)$b4Filename),
                    'form_group_class' => 'js-tpl tpl-simple',
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Archivo Bloque 3'),
                    'name' => 'block4_image',
                    'desc' => $this->l('Sube una imagen o un video (mp4/webm) o imagen (jpg/png/webp). Si no subes nada, se mantiene el actual.'),
                    'form_group_class' => 'js-tpl tpl-simple',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Eliminar archivo actual (Bloque 3)'),
                    'name' => 'block4_image_delete',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'block4_image_delete_on', 'value' => 1, 'label' => $this->l('Sí')],
                        ['id' => 'block4_image_delete_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                    'form_group_class' => 'js-tpl tpl-simple',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'block4_image_old',
                    'form_group_class' => 'js-tpl tpl-simple',
                ],

                // CARACTERISTICAS (tpl-default)
                [
                    'type' => 'html',
                    'label' => $this->l('Características'),
                    'name' => 'characteristics_block',
                    'html_content' => $this->renderCharacteristicsBlock(),
                    'form_group_class' => 'js-tpl tpl-default',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'characteristics_json',
                    'form_group_class' => 'js-tpl tpl-default',
                ],

                // SLIDES
                [
                    'type' => 'html',
                    'label' => $this->l('Carrusel'),
                    'name' => 'slides_block',
                    'html_content' => $this->renderSlidesBlock(),
                ],
                [
                    'type' => 'hidden',
                    'name' => 'slides_json',
                ],
            ],
            'submit' => [
                'title' => $this->l('Guardar'),
            ],
        ];

        return parent::renderForm();
    }

    protected function renderSlidesBlock()
    {
        $idLanding = (int)Tools::getValue('id_pslanding');

        $items = [];
        if ($idLanding) {
            $items = $this->getSlides($idLanding);
        }

        $json = json_encode([
            'items' => $items,
            'ajax_url' => $this->context->link->getAdminLink($this->controller_name, true),
            'token' => Tools::getAdminTokenLite($this->controller_name),
        ]);

        return '
        <div id="pslanding-slides" data-config="' . htmlspecialchars($json, ENT_QUOTES, 'UTF-8') . '">
            <div id="pslanding-slides-list"></div>

            <div style="margin-top:15px">
                <button type="button" class="btn btn-primary" id="pslanding-add-slide">
                    ' . $this->l('Añadir slide') . '
                </button>
            </div>

            <p class="help-block">
                ' . $this->l('Cada slide tiene imagen + producto. Puedes añadir varios.') . '
            </p>
        </div>';
    }

    protected function getSlides($idLanding)
    {
        return Db::getInstance()->executeS('
            SELECT id_pslanding_slide, position, image, id_product, id_category, active
            FROM ' . _DB_PREFIX_ . 'pslanding_slide
            WHERE id_pslanding=' . (int)$idLanding . '
            ORDER BY position ASC, id_pslanding_slide ASC
        ');
    }

    public function ajaxProcessSearchProducts()
    {
        $q = trim(Tools::getValue('q', ''));
        $id_lang = (int)$this->context->language->id;

        if ($q === '' || Tools::strlen($q) < 2) {
            die(json_encode([]));
        }

        $like = '%' . pSQL($q) . '%';

        $rows = Db::getInstance()->executeS('
            SELECT p.id_product, pl.name
            FROM ' . _DB_PREFIX_ . 'product p
            INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl
                ON (pl.id_product = p.id_product AND pl.id_lang=' . (int)$id_lang . ')
            WHERE pl.name LIKE "' . $like . '"
            ORDER BY pl.name ASC
            LIMIT 20
        ');

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id_product' => (int)$r['id_product'],
                'name' => $r['name'],
            ];
        }

        header('Content-Type: application/json');
        die(json_encode($out));
    }

    protected function saveSlidesFromJson($idLanding)
    {
        $json = Tools::getValue('slides_json');
        if (!$json) {
            return;
        }

        $items = json_decode($json, true);
        if (!is_array($items)) {
            return;
        }

        Db::getInstance()->delete('pslanding_slide', 'id_pslanding=' . (int)$idLanding);

        $uploadDir = $this->getUploadDir();
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        if (!file_exists($uploadDir . 'index.php')) {
            @file_put_contents($uploadDir . 'index.php', "<?php\n");
        }

        $position = 0;
        foreach ($items as $it) {
            $position++;

            $active = !empty($it['active']) ? 1 : 0;
            $id_product = !empty($it['id_product']) ? (int)$it['id_product'] : null;
            $id_category = !empty($it['id_category']) ? (int)$it['id_category'] : null;
            $oldImage = !empty($it['image']) ? (string)$it['image'] : '';

            $idx = isset($it['idx']) ? (int)$it['idx'] : 0;
            $field = 'slide_image_' . $idx;

            $finalFilename = $oldImage;

            if (!empty($_FILES[$field]['name']) && is_uploaded_file($_FILES[$field]['tmp_name'])) {
                $ext = Tools::strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                if (!in_array($ext, $allowed, true)) {
                    $this->errors[] = sprintf('Slide %d: extensión no permitida', $position);
                } else {
                    $finalFilename = 'slide_' . $idLanding . '_' . sha1(uniqid('', true)) . '.' . $ext;
                    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDir . $finalFilename)) {
                        $this->errors[] = sprintf('Slide %d: no se pudo mover la imagen', $position);
                    }
                }
            }

            Db::getInstance()->insert('pslanding_slide', [
                'id_pslanding' => (int)$idLanding,
                'position' => (int)$position,
                'image' => pSQL($finalFilename),
                'id_product' => $id_product,
                'id_category' => $id_category,
                'active' => (int)$active,
            ]);
        }
    }

    protected function renderCharacteristicsBlock()
    {
        $idLanding = (int)Tools::getValue('id_pslanding');
        $id_shop = (int)$this->context->shop->id;

        $languages = Language::getLanguages(false);
        $defaultLang = (int)$this->context->language->id;

        $items = [];
        if ($idLanding) {
            $items = $this->getCharacteristics($idLanding, $id_shop);
        }

        $json = json_encode([
            'items' => $items,
            'languages' => $languages,
            'default_lang' => $defaultLang,
        ]);

        return '
        <div id="pslanding-characteristics"
            data-config="' . htmlspecialchars($json, ENT_QUOTES, 'UTF-8') . '">

            <div id="pslanding-characteristics-list"></div>

            <div style="margin-bottom:15px">
                <button type="button" class="btn btn-primary" id="pslanding-add-characteristic">
                    ' . $this->l('Añadir característica') . '
                </button>
            </div>

            <p class="help-block">
                ' . $this->l('Añade tantas características como quieras. Se guardan por idioma.') . '
            </p>
        </div>';
    }

    protected function getCharacteristics($idLanding, $id_shop)
    {
        $rows = Db::getInstance()->executeS('
            SELECT c.id_pslanding_characteristic, c.position, cl.id_lang, cl.title, cl.text
            FROM ' . _DB_PREFIX_ . 'pslanding_characteristic c
            LEFT JOIN ' . _DB_PREFIX_ . 'pslanding_characteristic_lang cl
                ON (cl.id_pslanding_characteristic = c.id_pslanding_characteristic)
            WHERE c.id_pslanding = ' . (int)$idLanding . '
            ORDER BY c.position ASC
        ');

        $items = [];
        foreach ($rows as $r) {
            $idc = (int)$r['id_pslanding_characteristic'];
            if (!isset($items[$idc])) {
                $items[$idc] = [
                    'id' => $idc,
                    'position' => (int)$r['position'],
                    'title' => [],
                    'text' => [],
                ];
            }
            if (!empty($r['id_lang'])) {
                $items[$idc]['title'][(int)$r['id_lang']] = (string)$r['title'];
                $items[$idc]['text'][(int)$r['id_lang']] = (string)$r['text'];
            }
        }

        return array_values($items);
    }

    public function getFieldsValue($obj)
    {
        $fields = parent::getFieldsValue($obj);

        if (!Validate::isLoadedObject($obj)) {
            // Defaults para switches de delete incluso en "add"
            $fields['hero_media_delete'] = 0;
            $fields['block2_image_delete'] = 0;
            $fields['block3_image_delete'] = 0;
            $fields['block4_image_delete'] = 0;
            return $fields;
        }

        $id_lang = (int)$this->context->language->id;

        $fields['hero_media']   = $this->normalizeFileValue($obj->hero_media, $id_lang);
        $fields['block2_image'] = $this->normalizeFileValue($obj->block2_image, $id_lang);
        $fields['block3_image'] = $this->normalizeFileValue($obj->block3_image, $id_lang);
        $fields['block4_image'] = $this->normalizeFileValue($obj->block4_image, $id_lang);

        $fields['hero_media_old']   = $fields['hero_media'];
        $fields['block2_image_old'] = $fields['block2_image'];
        $fields['block3_image_old'] = $fields['block3_image'];
        $fields['block4_image_old'] = $fields['block4_image'];

        // Defaults delete (desmarcado)
        $fields['hero_media_delete'] = 0;
        $fields['block2_image_delete'] = 0;
        $fields['block3_image_delete'] = 0;
        $fields['block4_image_delete'] = 0;

        return $fields;
    }

    protected function normalizeFileValue($value, $id_lang)
    {
        if (is_array($value)) {
            if (isset($value[$id_lang]) && is_string($value[$id_lang])) {
                return $value[$id_lang];
            }
            foreach ($value as $v) {
                if (is_string($v) && $v !== '') {
                    return $v;
                }
            }
            return '';
        }

        return is_string($value) ? $value : '';
    }

    protected function getCollectionOptions()
    {
        $id_lang = (int)$this->context->language->id;

        $rows = Db::getInstance()->executeS('
            SELECT fv.id_feature_value, fvl.value
            FROM ' . _DB_PREFIX_ . 'feature_value fv
            INNER JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl
                ON (fvl.id_feature_value = fv.id_feature_value AND fvl.id_lang = ' . (int)$id_lang . ')
            WHERE fv.id_feature = 57
            ORDER BY fvl.value ASC
        ');

        $options = [
            ['id' => 0, 'name' => $this->l('-- Select collection --')],
        ];

        foreach ($rows as $r) {
            $options[] = ['id' => (int)$r['id_feature_value'], 'name' => $r['value']];
        }

        return $options;
    }

    protected function getCategoryPathOptions()
    {
        $id_lang = (int)$this->context->language->id;
        $id_shop = (int)$this->context->shop->id;

        $options = [
            ['id' => 0, 'name' => $this->l('-- Select category --')],
        ];

        $rows = Db::getInstance()->executeS('
            SELECT c.id_category
            FROM ' . _DB_PREFIX_ . 'category c
            INNER JOIN ' . _DB_PREFIX_ . 'category_shop cs
                ON (cs.id_category = c.id_category AND cs.id_shop = ' . (int)$id_shop . ')
            WHERE c.active = 1
            ORDER BY c.id_category ASC
        ');

        foreach ($rows as $r) {
            $id_cat = (int)$r['id_category'];
            $c = new Category($id_cat, $id_lang, $id_shop);
            if (!Validate::isLoadedObject($c)) {
                continue;
            }

            $parents = $c->getParentsCategories($id_lang);
            $parts = [];

            if (is_array($parents) && !empty($parents)) {
                $parents = array_reverse($parents);
                foreach ($parents as $p) {
                    if (!empty($p['name'])) {
                        $parts[] = $p['name'];
                    }
                }
            } else {
                $parts[] = $c->name;
            }

            $options[] = ['id' => $id_cat, 'name' => implode(' > ', $parts)];
        }

        return $options;
    }

    protected function saveCharacteristicsFromJson($idLanding)
    {
        $json = Tools::getValue('characteristics_json');
        if (!$json) {
            return;
        }

        $items = json_decode($json, true);
        if (!is_array($items)) {
            return;
        }

        $existing = Db::getInstance()->executeS('
            SELECT id_pslanding_characteristic
            FROM ' . _DB_PREFIX_ . 'pslanding_characteristic
            WHERE id_pslanding=' . (int)$idLanding
        );

        foreach ($existing as $e) {
            Db::getInstance()->delete(
                'pslanding_characteristic_lang',
                'id_pslanding_characteristic=' . (int)$e['id_pslanding_characteristic']
            );
        }

        Db::getInstance()->delete('pslanding_characteristic', 'id_pslanding=' . (int)$idLanding);

        $position = 0;
        foreach ($items as $it) {
            $position++;

            Db::getInstance()->insert('pslanding_characteristic', [
                'id_pslanding' => (int)$idLanding,
                'position' => (int)$position,
            ]);

            $idc = (int)Db::getInstance()->Insert_ID();

            $titles = (isset($it['title']) && is_array($it['title'])) ? $it['title'] : [];
            $texts  = (isset($it['text']) && is_array($it['text'])) ? $it['text'] : [];

            foreach (Language::getLanguages(false) as $lang) {
                $id_lang = (int)$lang['id_lang'];

                Db::getInstance()->insert('pslanding_characteristic_lang', [
                    'id_pslanding_characteristic' => (int)$idc,
                    'id_lang' => (int)$id_lang,
                    'title' => pSQL($titles[$id_lang] ?? ''),
                    'text'  => pSQL($texts[$id_lang] ?? '', true),
                ]);
            }
        }
    }

    public function processSave()
    {
        // Mantener media actuales si no se sube nada nuevo (pero NO si hay delete marcado)
        $this->keepOldFileIfNoUpload('hero_media');
        $this->keepOldFileIfNoUpload('block2_image');
        $this->keepOldFileIfNoUpload('block3_image');
        $this->keepOldFileIfNoUpload('block4_image');

        $res = parent::processSave();
        if (!$res) {
            return false;
        }

        $idLanding = (int)Tools::getValue('id_pslanding');
        if (!$idLanding && isset($this->object) && Validate::isLoadedObject($this->object)) {
            $idLanding = (int)$this->object->id;
        }

        if ($idLanding) {
            // Si core lo vació, restaurar (salvo delete)
            $this->restoreOldFileIfCleared($idLanding, 'hero_media');
            $this->restoreOldFileIfCleared($idLanding, 'block2_image');
            $this->restoreOldFileIfCleared($idLanding, 'block3_image');
            $this->restoreOldFileIfCleared($idLanding, 'block4_image');

            // Borrado explícito (prioridad)
            $this->applyDeleteIfRequested($idLanding, 'hero_media');
            $this->applyDeleteIfRequested($idLanding, 'block2_image');
            $this->applyDeleteIfRequested($idLanding, 'block3_image');
            $this->applyDeleteIfRequested($idLanding, 'block4_image');

            // Subidas nuevas
            $this->handleUploadGlobal($idLanding, 'hero_media', ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm']);
            $this->handleUploadGlobal($idLanding, 'block2_image', ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm']);
            $this->handleUploadGlobal($idLanding, 'block3_image', ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm']);
            $this->handleUploadGlobal($idLanding, 'block4_image', ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm']);

            $this->saveCharacteristicsFromJson($idLanding);
            $this->saveSlidesFromJson($idLanding);
        }

        if (!empty($this->errors)) {
            return false;
        }

        return $res;
    }

    protected function handleUploadGlobal($idLanding, $field, array $allowedExt)
    {
        $uploadDir = $this->getUploadDir();
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        if (!file_exists($uploadDir . 'index.php')) {
            @file_put_contents($uploadDir . 'index.php', "<?php\n");
        }

        if (empty($_FILES[$field]['name']) || !is_uploaded_file($_FILES[$field]['tmp_name'])) {
            return; // no hay upload nuevo
        }

        $ext = Tools::strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            $this->errors[] = sprintf('%s: extensión no permitida', $field);
            return;
        }

        $id_shop = (int)$this->context->shop->id;
        $finalFilename = $field . '_' . (int)$idLanding . '_' . (int)$id_shop . '_' . sha1(uniqid('', true)) . '.' . $ext;

        if (!move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDir . $finalFilename)) {
            $this->errors[] = sprintf('%s: no se pudo mover el archivo', $field);
            return;
        }

        // Borrar archivo antiguo si existe (solo cuando sube uno nuevo)
        $old = (string)Tools::getValue($field . '_old', '');
        if ($old && $old !== $finalFilename && file_exists($uploadDir . $old)) {
            @unlink($uploadDir . $old);
        }

        Db::getInstance()->update(
            'pslanding',
            [$field => pSQL($finalFilename)],
            'id_pslanding=' . (int)$idLanding
        );
    }

    protected function keepOldFileIfNoUpload(string $field): void
    {
        $delete = (bool)Tools::getValue($field . '_delete', 0);
        if ($delete) {
            return; // prioridad: si va a borrar, no reinyectar old
        }

        $hasNewUpload = !empty($_FILES[$field]['name']) && is_uploaded_file($_FILES[$field]['tmp_name']);
        if ($hasNewUpload) {
            return;
        }

        $old = (string)Tools::getValue($field . '_old', '');
        if ($old !== '') {
            $_POST[$field] = $old;
            $_REQUEST[$field] = $old;
        }
    }

    protected function restoreOldFileIfCleared(int $idLanding, string $field): void
    {
        $delete = (bool)Tools::getValue($field . '_delete', 0);
        if ($delete) {
            return; // si pide borrar, no restaurar
        }

        $hasNewUpload = !empty($_FILES[$field]['name']) && is_uploaded_file($_FILES[$field]['tmp_name']);
        if ($hasNewUpload) {
            return;
        }

        $old = (string)Tools::getValue($field . '_old', '');
        if ($old === '') {
            return;
        }

        $current = Db::getInstance()->getValue('
            SELECT `' . bqSQL($field) . '`
            FROM `' . _DB_PREFIX_ . 'pslanding`
            WHERE id_pslanding=' . (int)$idLanding
        );

        if ((string)$current === '') {
            Db::getInstance()->update(
                'pslanding',
                [$field => pSQL($old)],
                'id_pslanding=' . (int)$idLanding
            );
        }
    }

    protected function applyDeleteIfRequested(int $idLanding, string $field): void
    {
        $delete = (bool)Tools::getValue($field . '_delete', 0);
        if (!$delete) {
            return;
        }

        $uploadDir = $this->getUploadDir();
        $old = (string)Tools::getValue($field . '_old', '');

        if ($old !== '' && file_exists($uploadDir . $old)) {
            @unlink($uploadDir . $old);
        }

        Db::getInstance()->update(
            'pslanding',
            [$field => ''],
            'id_pslanding=' . (int)$idLanding
        );

        // Evita restauraciones posteriores
        $_POST[$field . '_old'] = '';
        $_REQUEST[$field . '_old'] = '';
    }

    protected function getUploadDir(): string
    {
        return _PS_MODULE_DIR_ . 'pslanding/uploads/';
    }

    protected function getUploadBaseUrl(): string
    {
        return $this->module->getPathUri() . 'uploads/';
    }

    protected function renderMediaPreviewHtml(string $filename): string
    {
        if ($filename === '') {
            return '<em>' . $this->l('No hay archivo subido.') . '</em>';
        }

        $url = $this->getUploadBaseUrl() . $filename;
        $ext = Tools::strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $isVideo = in_array($ext, ['mp4', 'webm', 'ogg'], true);

        $html = '<div style="margin-top:6px">';
        if ($isVideo) {
            $html .= '<video style="max-width:50px; width:100%; height:auto; display:block" controls preload="metadata" src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"></video>';
        } else {
            $html .= '<img style="max-width:50px; width:100%; height:auto; display:block" src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" alt="">';
        }
        $html .= '<p class="help-block" style="margin-top:6px"><code>' . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') . '</code></p>';
        $html .= '</div>';

        return $html;
    }
}
