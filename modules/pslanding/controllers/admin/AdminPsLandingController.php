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
        $this->addCSS($this->module->getPathUri() . 'views/assets/css/back.css');
    }

    public function renderForm()
    {
        $templates = [
            ['id' => 'landing-morocco', 'name' => 'Landing Morocco'],
            ['id' => 'landing-muestras', 'name' => 'Landing Muestras'],
            ['id' => 'landing-piedras',  'name' => 'Landing Piedras'],
            ['id' => 'landing-color',  'name' => 'Landing Colores'],
            ['id' => 'landing-vitri',  'name' => 'Landing vitri'],
            ['id' => 'landing-external', 'name' => 'External Landing'],
        ];

        // Para previews: al editar, el objeto está cargado.
        $heroFilename  = (Validate::isLoadedObject($this->object) && is_string($this->object->hero_media)) ? (string)$this->object->hero_media : '';
        $heroMobileFilename = (Validate::isLoadedObject($this->object) && is_string($this->object->hero_media_mobile)) ? (string)$this->object->hero_media_mobile : '';
        $hero2Filename = (Validate::isLoadedObject($this->object) && is_string($this->object->hero2_media)) ? (string)$this->object->hero2_media : '';

        $b2Filename = (Validate::isLoadedObject($this->object) && is_string($this->object->block2_image)) ? (string)$this->object->block2_image : '';
        $b3Filename = (Validate::isLoadedObject($this->object) && is_string($this->object->block3_image)) ? (string)$this->object->block3_image : '';
        $b4Filename = (Validate::isLoadedObject($this->object) && is_string($this->object->block4_image)) ? (string)$this->object->block4_image : '';
        $b5Filename = (Validate::isLoadedObject($this->object) && is_string($this->object->block5_image)) ? (string)$this->object->block5_image : '';
        $b6Filename = (Validate::isLoadedObject($this->object) && is_string($this->object->block6_image)) ? (string)$this->object->block6_image : '';
        $b7Filename = (Validate::isLoadedObject($this->object) && is_string($this->object->block7_image)) ? (string)$this->object->block7_image : '';
        // HERO2 product (id + name)
        $hero2ProductId = (Validate::isLoadedObject($this->object) && (int)$this->object->hero2_product > 0) ? (int)$this->object->hero2_product : NULL;

        $hero2ProductName = '';
        if ($hero2ProductId) {
            $hero2ProductName = (string)Db::getInstance()->getValue('
                SELECT name
                FROM '._DB_PREFIX_.'product_lang
                WHERE id_product='.(int)$hero2ProductId.'
                AND id_lang='.(int)$this->context->language->id.'
            ');
        }

        // Admin link (tu override a veces devuelve array)
        $adminLink = $this->context->link->getAdminLink($this->controller_name, true);
        if (is_array($adminLink)) {
            $adminLink = $adminLink['url'] ?? (reset($adminLink) ?: '');
        }
        $adminLink = (string)$adminLink;

        $inputs = array_merge(
            $this->addBlockBasics($templates),
            $this->addBlockExternal(),
            $this->addBlockMainHero($heroFilename, $heroMobileFilename),
            $this->addBlockCarousel2(),
            $this->addBlock1($b2Filename),
            $this->addBlock2($b3Filename),
            $this->addBlock3($b4Filename),
            $this->addBlock4($b5Filename),
            $this->addBlock5($b6Filename),
            $this->addBlock6($b7Filename),
            $this->addBlockCarousel(),
            $this->addBlockCharacteristicks(),
            $this->addBlockHero2($adminLink, $hero2ProductName, $hero2Filename)
        );

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Landing page'),
            ],

            'input' => $inputs,

            'submit' => [
                'title' => $this->l('Guardar'),
            ],
        ];



        return parent::renderForm();
    }


    protected function htmlInput(string $name, string $html, string $formGroupClass = ''): array
    {
        $out = [
            'type' => 'html',
            'name' => $name,
            'html_content' => $html,
        ];
        if ($formGroupClass) {
            $out['form_group_class'] = $formGroupClass;
        }
        return $out;
    }

    protected function cardStartInput(string $key, string $title, string $tone = 'default', string $extraClass = ''): array
    {

        $html = '
        <div class="landing-block-title" data-psl-card="'.$key.'">
        
            <h3 >'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'</h3>
    
            
        ';

        return $this->htmlInput('_psl_'.$key.'_start', $html, $extraClass);
    }

    protected function cardEndInput(string $key, string $extraClass = ''): array
    {
        $html = '
          
        </div>
        ';

        return $this->htmlInput('_psl_'.$key.'_end', $html, $extraClass);
    }



    protected function renderSlidesBlock(
        string $slot = 'carousel_1',
        string $inputName = 'slides_json_carousel_1',
        string $rootId = 'pslanding-slides-carousel-1',
        string $listId = 'pslanding-slides-list-carousel-1',
        string $btnId  = 'pslanding-add-slide-carousel-1'
    
    ) {
        $idLanding = (int)Tools::getValue('id_pslanding');

        $items = [];
        if ($idLanding) {
            $items = $this->getSlides($idLanding, $slot);
        }

        $currentTemplate = (Validate::isLoadedObject($this->object)
            ? (string)$this->object->template
            : (string)Tools::getValue('template', 'landing-morocco')
        );

        $languages = Language::getLanguages(false);
        $defaultLang = (int)$this->context->language->id;

        $json = json_encode([
            'items' => $items,
            'template' => $currentTemplate,
            'languages' => $languages,
            'default_lang' => $defaultLang,
            'ajax_url' => $this->context->link->getAdminLink($this->controller_name, true),
            'token' => Tools::getAdminTokenLite($this->controller_name),

            // IMPORTANTES para multi-instancia:
            'slot' => $slot,
            'input_name' => $inputName,
            'root_id' => $rootId,
            'list_id' => $listId,
            'btn_id' => $btnId,
        ]);

        return '
        <div id="'.htmlspecialchars($rootId, ENT_QUOTES, 'UTF-8').'" class="pslanding-slides-root"
            data-config="' . htmlspecialchars($json, ENT_QUOTES, 'UTF-8') . '">

            <div id="'.htmlspecialchars($listId, ENT_QUOTES, 'UTF-8').'"></div>

            <div style="margin-top:15px">
                <button type="button" class="btn btn-primary" id="'.htmlspecialchars($btnId, ENT_QUOTES, 'UTF-8').'">
                    ' . $this->l('Añadir slide') . '
                </button>
            </div>

            <p class="help-block">
                ' . $this->l('Cada slide tiene 1 imagen por idioma.') . '
            </p>
        </div>';
    }

    protected function getSlides(int $idLanding, string $slot = 'carousel_1')
    {
        $slides = Db::getInstance()->executeS('
            SELECT id_pslanding_slide, position, id_product, id_category, active, slot
            FROM '._DB_PREFIX_.'pslanding_slide
            WHERE id_pslanding='.(int)$idLanding.'
            AND slot="'.pSQL($slot).'"
            ORDER BY position ASC, id_pslanding_slide ASC
        ');

        if (!$slides) return [];

        $ids = array_map('intval', array_column($slides, 'id_pslanding_slide'));
        $rows = Db::getInstance()->executeS('
            SELECT id_pslanding_slide, id_lang, image
            FROM '._DB_PREFIX_.'pslanding_slide_lang
            WHERE id_pslanding_slide IN ('.implode(',', $ids).')
        ');

        $bySlide = [];
        foreach ($rows as $r) {
            $sid = (int)$r['id_pslanding_slide'];
            $lid = (int)$r['id_lang'];
            $bySlide[$sid][$lid] = (string)$r['image'];
        }

        foreach ($slides as &$s) {
            $sid = (int)$s['id_pslanding_slide'];
            $s['images'] = $bySlide[$sid] ?? [];
        }
        unset($s);

        return $slides;
    }


    protected function saveSlidesFromJson(int $idLanding, string $slot = 'carousel_1', string $inputName = 'slides_json_carousel_1')
    {
        $json = Tools::getValue($inputName);
        if (!$json) {
            return;
        }

        $items = json_decode($json, true);
        if (!is_array($items)) {
            return;
        }

        // 1) Borrar langs + slides anteriores SOLO del slot
        Db::getInstance()->execute('
            DELETE sl
            FROM '._DB_PREFIX_.'pslanding_slide_lang sl
            INNER JOIN '._DB_PREFIX_.'pslanding_slide s ON (s.id_pslanding_slide = sl.id_pslanding_slide)
            WHERE s.id_pslanding='.(int)$idLanding.'
            AND s.slot="'.pSQL($slot).'"
        ');

        Db::getInstance()->delete(
            'pslanding_slide',
            'id_pslanding='.(int)$idLanding.' AND slot="'.pSQL($slot).'"'
        );

        // 2) Preparar uploads
        $uploadDir = $this->getUploadDir();
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        if (!file_exists($uploadDir.'index.php')) {
            @file_put_contents($uploadDir.'index.php', "<?php\n");
        }

        $languages = Language::getLanguages(false);
        $position = 0;

        foreach ($items as $it) {
            $position++;

            $active      = !empty($it['active']) ? 1 : 0;
            $id_product  = !empty($it['id_product']) ? (int)$it['id_product'] : null;
            $id_category = !empty($it['id_category']) ? (int)$it['id_category'] : null;

            $idx = isset($it['idx']) ? (int)$it['idx'] : $position;

            // images existentes por idioma (del JSON)
            $existingImages = (!empty($it['images']) && is_array($it['images'])) ? $it['images'] : [];

            // 3) Insertar slide base
            Db::getInstance()->insert('pslanding_slide', [
                'id_pslanding' => (int)$idLanding,
                'slot'         => pSQL($slot),
                'position'     => (int)$position,
                'id_product'   => $id_product,
                'id_category'  => $id_category,
                'active'       => (int)$active,
            ]);

            $idSlide = (int)Db::getInstance()->Insert_ID();

            // 4) Guardar/actualizar imagen por idioma
            foreach ($languages as $lang) {
                $id_lang = (int)$lang['id_lang'];

                /**
                 * IMPORTANTE:
                 * El name del file input debe ser único por carrusel:
                 * slide_image_{slot}_{idx}_{id_lang}
                 */
                $fileField = 'slide_image_'.$slot.'_'.$idx.'_'.$id_lang;

                $oldFilename = isset($existingImages[$id_lang]) ? (string)$existingImages[$id_lang] : '';
                $finalFilename = $oldFilename;

                if (!empty($_FILES[$fileField]['name']) && is_uploaded_file($_FILES[$fileField]['tmp_name'])) {
                    $ext = Tools::strtolower(pathinfo($_FILES[$fileField]['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

                    if (!in_array($ext, $allowed, true)) {
                        $this->errors[] = sprintf('Slide %d (%s, lang %d): extensión no permitida', $position, $slot, $id_lang);
                    } else {
                        $finalFilename = 'slide_'.$idLanding.'_'.$slot.'_'.$idx.'_'.$id_lang.'_'.sha1(uniqid('', true)).'.'.$ext;

                        if (!move_uploaded_file($_FILES[$fileField]['tmp_name'], $uploadDir.$finalFilename)) {
                            $this->errors[] = sprintf('Slide %d (%s, lang %d): no se pudo mover la imagen', $position, $slot, $id_lang);
                            $finalFilename = $oldFilename;
                        } else {
                            if ($oldFilename && $oldFilename !== $finalFilename && file_exists($uploadDir.$oldFilename)) {
                                @unlink($uploadDir.$oldFilename);
                            }
                        }
                    }
                }

                Db::getInstance()->insert('pslanding_slide_lang', [
                    'id_pslanding_slide' => (int)$idSlide,
                    'id_lang'            => (int)$id_lang,
                    'image'              => pSQL($finalFilename),
                ]);
            }
        }
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


    public function ajaxProcessSearchCategories()
    {
        $q = trim(Tools::getValue('q', ''));
        $id_lang = (int)$this->context->language->id;
        $id_shop = (int)$this->context->shop->id;

        if ($q === '' || Tools::strlen($q) < 2) {
            header('Content-Type: application/json');
            die(json_encode([]));
        }

        $like = '%'.pSQL($q).'%';

        $rows = Db::getInstance()->executeS('
            SELECT c.id_category
            FROM '._DB_PREFIX_.'category c
            INNER JOIN '._DB_PREFIX_.'category_shop cs
                ON (cs.id_category = c.id_category AND cs.id_shop='.(int)$id_shop.')
            INNER JOIN '._DB_PREFIX_.'category_lang cl
                ON (cl.id_category = c.id_category AND cl.id_lang='.(int)$id_lang.' AND cl.id_shop='.(int)$id_shop.')
            WHERE c.active = 1
            AND cl.name LIKE "'.$like.'"
            ORDER BY cl.name ASC
            LIMIT 20
        ');

        $out = [];

        foreach ($rows as $r) {
            $id_category = (int)$r['id_category'];
            $cat = new Category($id_category, $id_lang, $id_shop);
            if (!Validate::isLoadedObject($cat)) {
                continue;
            }

            // Devuelve desde la categoría hasta Home (según versión)
            $parents = $cat->getParentsCategories($id_lang);
            $parts = [];

            if (is_array($parents) && !empty($parents)) {
                $parents = array_reverse($parents); // Home -> ... -> Actual
                foreach ($parents as $p) {
                    if (!empty($p['name'])) {
                        $parts[] = $p['name'];
                    }
                }
            } else {
                $parts[] = $cat->name;
            }

            // Si no quieres que salga "Home", descomenta:
            if (!empty($parts) && Tools::strtolower($parts[0]) === 'home') array_shift($parts);

            $path = implode(' / ', $parts);

            $out[] = [
                'id_category' => $id_category,
                'name' => $cat->name,
                'path' => $path,
                'label' => '#'.$id_category.' - '.$path,
            ];
        }

        header('Content-Type: application/json');
        die(json_encode($out));
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
            $fields['hero_media_mobile_delete'] = 0;
            $fields['hero2_media_delete'] = 0;
            $fields['hero2_product'] = 0;
            $fields['block2_image_delete'] = 0;
            $fields['block3_image_delete'] = 0;
            $fields['block4_image_delete'] = 0;
            $fields['block5_image_delete'] = 0;
            $fields['block6_image_delete'] = 0;
            $fields['block7_image_delete'] = 0;
            return $fields;
        }

        $id_lang = (int)$this->context->language->id;

        $fields['hero_media']   = $this->normalizeFileValue($obj->hero_media, $id_lang);
        $fields['hero_media_mobile']   = $this->normalizeFileValue($obj->hero_media_mobile, $id_lang);
        $fields['hero2_media']   = $this->normalizeFileValue($obj->hero2_media, $id_lang);
        $fields['hero2_product'] = (int)$obj->hero2_product;

        $fields['block2_image'] = $this->normalizeFileValue($obj->block2_image, $id_lang);
        $fields['block3_image'] = $this->normalizeFileValue($obj->block3_image, $id_lang);
        $fields['block4_image'] = $this->normalizeFileValue($obj->block4_image, $id_lang);
        $fields['block5_image'] = $this->normalizeFileValue($obj->block5_image, $id_lang);
        $fields['block6_image'] = $this->normalizeFileValue($obj->block6_image, $id_lang);
        $fields['block7_image'] = $this->normalizeFileValue($obj->block7_image, $id_lang);

        $fields['hero_media_old']   = $fields['hero_media'];
        $fields['hero_media_mobile_old']   = $fields['hero_media_mobile'];
        $fields['hero2_media_old']   = $fields['hero2_media'];
        $fields['block2_image_old'] = $fields['block2_image'];
        $fields['block3_image_old'] = $fields['block3_image'];
        $fields['block4_image_old'] = $fields['block4_image'];
        $fields['block5_image_old'] = $fields['block5_image'];
        $fields['block6_image_old'] = $fields['block6_image'];
        $fields['block7_image_old'] = $fields['block7_image'];

        // Defaults delete (desmarcado)
        $fields['hero_media_delete'] = 0;
        $fields['hero2_media_delete'] = 0;
        $fields['block2_image_delete'] = 0;
        $fields['block3_image_delete'] = 0;
        $fields['block4_image_delete'] = 0;
        $fields['block5_image_delete'] = 0;
        $fields['block6_image_delete'] = 0;
        $fields['block7_image_delete'] = 0;

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
            WHERE fv.id_feature = 75
            ORDER BY fvl.value ASC
        ');

        $options = [
            ['id' => 0, 'name' => $this->l('-- Seleccionar Campaña --')],
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
        $this->keepOldFileIfNoUpload('hero_media_mobile');
        $this->keepOldFileIfNoUpload('block2_image');
        $this->keepOldFileIfNoUpload('block3_image');
        $this->keepOldFileIfNoUpload('block4_image');
        $this->keepOldFileIfNoUpload('block5_image');
        $this->keepOldFileIfNoUpload('block6_image');
        $this->keepOldFileIfNoUpload('block7_image');

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
            $this->restoreOldFileIfCleared($idLanding, 'hero_media_mobile');
            $this->restoreOldFileIfCleared($idLanding, 'hero2_media');
            $this->restoreOldFileIfCleared($idLanding, 'block2_image');
            $this->restoreOldFileIfCleared($idLanding, 'block3_image');
            $this->restoreOldFileIfCleared($idLanding, 'block4_image');
            $this->restoreOldFileIfCleared($idLanding, 'block5_image');
            $this->restoreOldFileIfCleared($idLanding, 'block6_image');
            $this->restoreOldFileIfCleared($idLanding, 'block7_image');

            // Borrado explícito (prioridad)
            $this->applyDeleteIfRequested($idLanding, 'hero_media');
            $this->applyDeleteIfRequested($idLanding, 'hero_media_mobile');
            $this->applyDeleteIfRequested($idLanding, 'hero2_media');
            $this->applyDeleteIfRequested($idLanding, 'block2_image');
            $this->applyDeleteIfRequested($idLanding, 'block3_image');
            $this->applyDeleteIfRequested($idLanding, 'block4_image');
            $this->applyDeleteIfRequested($idLanding, 'block5_image');
            $this->applyDeleteIfRequested($idLanding, 'block6_image');
            $this->applyDeleteIfRequested($idLanding, 'block7_image');

            // Subidas nuevas
            $this->handleUploadGlobal($idLanding, 'hero_media', ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm']);
            $this->handleUploadGlobal($idLanding, 'hero_media_mobile', ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm']);
            $this->handleUploadGlobal($idLanding, 'hero2_media', ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm']);
            $this->handleUploadGlobal($idLanding, 'block2_image', ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm']);
            $this->handleUploadGlobal($idLanding, 'block3_image', ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm']);
            $this->handleUploadGlobal($idLanding, 'block4_image', ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm']);
            $this->handleUploadGlobal($idLanding, 'block5_image', ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm']);
            $this->handleUploadGlobal($idLanding, 'block6_image', ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm']);
            $this->handleUploadGlobal($idLanding, 'block7_image', ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm']);

            $this->saveCharacteristicsFromJson($idLanding);
            $this->saveSlidesFromJson($idLanding, 'carousel_1', 'slides_json_carousel_1');
            $this->saveSlidesFromJson($idLanding, 'carousel_2', 'slides_json_carousel_2');
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

    /* FUNCIONES DE LOS BLOQUES */
    protected function addBlockBasics($templates): array {
                
        return [
                // =========================
                // CARD: Básicos
                // =========================
                $this->cardStartInput('basic', 'Datos básicos', 'primary', 'landing-admin-card'),

                [
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'active_on',  'value' => 1, 'label' => $this->l('Enabled')],
                        ['id' => 'active_off', 'value' => 0, 'label' => $this->l('Disabled')],
                    ],
                    'form_group_class' => 'psl-field psl-field--basic',
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
                    'form_group_class' => 'psl-field psl-field--basic',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Campaña (Feature 75)'),
                    'name' => 'id_feature_value_collection',
                    'options' => [
                        'query' => $this->getCollectionOptions(),
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'form_group_class' => 'psl-field psl-field--basic',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'lang' => true,
                    'required' => true,
                    'form_group_class' => 'psl-field psl-field--basic',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Friendly URL (slug)'),
                    'name' => 'slug',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Solo letras, números, guiones. Ej: coleccion-primavera'),
                    'form_group_class' => 'psl-field psl-field--basic',
                ],

                $this->cardEndInput('basic'),
        ];

    }

    protected function addBlockExternal(): array {
        return [
                // =========================
                // CARD: External Landing (iframe)
                // =========================
                $this->cardStartInput('external', 'External Landing', 'default', 'landing-admin-card js-tpl tpl-external'),

                [
                    'type' => 'text',
                    'label' => $this->l('URL externa'),
                    'name' => 'external_url',
                    'lang' => true,
                    'hint' => $this->l('URL completa (https://...) que se cargará en un iframe. Puedes indicar una distinta por idioma.'),
                    'form_group_class' => 'psl-field psl-field--external js-tpl tpl-external',
                ],

                $this->cardEndInput('external', 'js-tpl tpl-external'),
        ];
    }

    protected function addBlockMainHero($heroFilename, $heroMobileFilename): array {
        return [
                // =========================
                // CARD: Hero principal
                // =========================
                $this->cardStartInput('hero', 'Hero principal', 'info' , 'landing-admin-card js-tpl tpl-default tpl-simple tpl-stone tpl-color'),

                [
                    'type' => 'text',
                    'label' => $this->l('Titulo Slider'),
                    'name' => 'hero_title',
                    'lang' => true,
                    'form_group_class' => 'psl-field psl-field--hero',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Texto Slider'),
                    'name' => 'hero_subtitle',
                    'lang' => true,
                    'autoload_rte' => true,
                    'form_group_class' => 'psl-field psl-field--hero',
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('Archivo actual (Slider)'),
                    'name' => 'hero_media_preview',
                    'html_content' => $this->renderMediaPreviewHtml((string)$heroFilename),
                    'form_group_class' => 'psl-field psl-field--hero',
                ],
                [
                    'type'  => 'file',
                    'label' => $this->l('Archivo Slider (image/video)'),
                    'name'  => 'hero_media',
                    'desc'  => $this->l('Sube una imagen o un video (mp4/webm) o imagen (jpg/png/webp). Si no subes nada, se mantiene el actual.'),
                    'form_group_class' => 'psl-field psl-field--hero',
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
                    'form_group_class' => 'psl-field psl-field--hero',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'hero_media_old',
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('Archivo actual (Slider)'),
                    'name' => 'hero_media_mobile_preview',
                    'html_content' => $this->renderMediaPreviewHtml((string)$heroMobileFilename),
                    'form_group_class' => 'psl-field psl-field--hero',
                ],
                [
                    'type'  => 'file',
                    'label' => $this->l('Archivo Slider Móvil (image/video)'),
                    'name'  => 'hero_media_mobile',
                    'desc'  => $this->l('Sube una imagen o un video para versión móvil (mp4/webm) o imagen (jpg/png/webp). Si no subes nada, se mantiene el actual.'),
                    'form_group_class' => 'psl-field psl-field--hero',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Eliminar archivo actual (Slider)'),
                    'name' => 'hero_media_mobile_delete',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'hero_media_mobile_delete_on', 'value' => 1, 'label' => $this->l('Sí')],
                        ['id' => 'hero_media_mobile_delete_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                    'form_group_class' => 'psl-field psl-field--hero',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'hero_media_mobile_old',
                ],

                $this->cardEndInput('hero', 'js-tpl tpl-default tpl-simple tpl-stone tpl-color'),
            ];
    }

    protected function addBlock1($b2Filename): array {
        return [
                // =========================
                // CARD: Bloque 1
                // =========================
                $this->cardStartInput('block1', 'Bloque 1', 'default', 'landing-admin-card js-tpl tpl-default tpl-simple tpl-stone tpl-color'),

                [
                    'type' => 'text',
                    'label' => $this->l('Titulo Bloque 1'),
                    'name' => 'block2_title',
                    'lang' => true,
                    'form_group_class' => 'psl-field psl-field--block1',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Texto Bloque 1'),
                    'name' => 'block2_text',
                    'lang' => true,
                    'autoload_rte' => true,
                    'form_group_class' => 'psl-field psl-field--block1',
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('Archivo actual (Bloque 1)'),
                    'name' => 'block2_image_preview',
                    'html_content' => $this->renderMediaPreviewHtml((string)$b2Filename),
                    'form_group_class' => 'psl-field psl-field--block1',
                ],
                [
                    'type'  => 'file',
                    'label' => $this->l('Archivo Bloque 1'),
                    'name'  => 'block2_image',
                    'desc'  => $this->l('Sube una imagen o un video (mp4/webm) o imagen (jpg/png/webp). Si no subes nada, se mantiene el actual.'),
                    'form_group_class' => 'psl-field psl-field--block1',
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
                    'form_group_class' => 'psl-field psl-field--block1',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'block2_image_old',
                ],

                $this->cardEndInput('block1', 'js-tpl tpl-default tpl-simple tpl-stone tpl-color'),

        ];
    }

    protected function addBlock2($b3Filename): array {
        return [
                // =========================
                // CARD: Bloque 2 (tpl-simple)
                // =========================
                $this->cardStartInput('block2', 'Bloque 2', 'default', 'landing-admin-card js-tpl tpl-simple tpl-stone tpl-color'),

                [
                    'type' => 'text',
                    'label' => $this->l('Titulo Bloque 2'),
                    'name' => 'block3_title',
                    'lang' => true,
                    'form_group_class' => 'psl-field psl-field--block2 ',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Texto Bloque 2'),
                    'name' => 'block3_text',
                    'lang' => true,
                    'autoload_rte' => true,
                    'form_group_class' => 'psl-field psl-field--block2 ',
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('Archivo actual (Bloque 2)'),
                    'name' => 'block3_image_preview',
                    'html_content' => $this->renderMediaPreviewHtml((string)$b3Filename),
                    'form_group_class' => 'psl-field psl-field--block2 ',
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Archivo Bloque 2'),
                    'name' => 'block3_image',
                    'desc' => $this->l('Sube una imagen o un video (mp4/webm) o imagen (jpg/png/webp). Si no subes nada, se mantiene el actual.'),
                    'form_group_class' => 'psl-field psl-field--block2 ',
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
                    'form_group_class' => 'psl-field psl-field--block2 ',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'block3_image_old',
                ],

                $this->cardEndInput('block2', 'js-tpl tpl-simple tpl-stone'),

        ];
    }

    protected function addBlock3($b4Filename): array {
        return [
                // =========================
                // CARD: Bloque 3 (tpl-simple)
                // =========================
                $this->cardStartInput('block3', 'Bloque 3', 'default', 'landing-admin-card js-tpl tpl-simple tpl-stone tpl-color'),

                [
                    'type' => 'text',
                    'label' => $this->l('Titulo Bloque 3'),
                    'name' => 'block4_title',
                    'lang' => true,
                    'form_group_class' => 'psl-field psl-field--block3 ',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Texto Bloque 3'),
                    'name' => 'block4_text',
                    'lang' => true,
                    'autoload_rte' => true,
                    'form_group_class' => 'psl-field psl-field--block3 ',
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('Archivo actual (Bloque 3)'),
                    'name' => 'block4_image_preview',
                    'html_content' => $this->renderMediaPreviewHtml((string)$b4Filename),
                    'form_group_class' => 'psl-field psl-field--block3 ',
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Archivo Bloque 3'),
                    'name' => 'block4_image',
                    'desc' => $this->l('Sube una imagen o un video (mp4/webm) o imagen (jpg/png/webp). Si no subes nada, se mantiene el actual.'),
                    'form_group_class' => 'psl-field psl-field--block3 ',
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
                    'form_group_class' => 'psl-field psl-field--block3 ',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'block4_image_old',
                ],

                $this->cardEndInput('block3', 'js-tpl tpl-simple tpl-stone'),

        ];
    }

    protected function addBlock4($b4Filename): array {
        return [
                // =========================
                // CARD: Bloque 4 (tpl-color)
                // =========================
                $this->cardStartInput('block4', 'Bloque 4', 'default', 'landing-admin-card js-tpl tpl-color'),

                [
                    'type' => 'text',
                    'label' => $this->l('Titulo Bloque 4'),
                    'name' => 'block5_title',
                    'lang' => true,
                    'form_group_class' => 'psl-field psl-field--block4 ',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Texto Bloque 4'),
                    'name' => 'block5_text',
                    'lang' => true,
                    'autoload_rte' => true,
                    'form_group_class' => 'psl-field psl-field--block4 ',
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('Archivo actual (Bloque 4)'),
                    'name' => 'block5_image_preview',
                    'html_content' => $this->renderMediaPreviewHtml((string)$b4Filename),
                    'form_group_class' => 'psl-field psl-field--block4 ',
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Archivo Bloque 4'),
                    'name' => 'block5_image',
                    'desc' => $this->l('Sube una imagen o un video (mp4/webm) o imagen (jpg/png/webp). Si no subes nada, se mantiene el actual.'),
                    'form_group_class' => 'psl-field psl-field--block4 ',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Eliminar archivo actual (Bloque 4)'),
                    'name' => 'block5_image_delete',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'block5_image_delete_on', 'value' => 1, 'label' => $this->l('Sí')],
                        ['id' => 'block5_image_delete_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                    'form_group_class' => 'psl-field psl-field--block3 ',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'block4_image_old',
                ],

                $this->cardEndInput('block4', 'js-tpl tpl-color'),

        ];
    }

    protected function addBlock5($b4Filename): array {
        return [
                // =========================
                // CARD: Bloque 5 (tpl-color)
                // =========================
                $this->cardStartInput('block5', 'Bloque 5', 'default', 'landing-admin-card js-tpl tpl-color'),

                [
                    'type' => 'text',
                    'label' => $this->l('Titulo Bloque 5'),
                    'name' => 'block6_title',
                    'lang' => true,
                    'form_group_class' => 'psl-field psl-field--block5 ',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Texto Bloque 5'),
                    'name' => 'block6_text',
                    'lang' => true,
                    'autoload_rte' => true,
                    'form_group_class' => 'psl-field psl-field--block5 ',
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('Archivo actual (Bloque 5)'),
                    'name' => 'block6_image_preview',
                    'html_content' => $this->renderMediaPreviewHtml((string)$b4Filename),
                    'form_group_class' => 'psl-field psl-field--block5 ',
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Archivo Bloque 5'),
                    'name' => 'block6_image',
                    'desc' => $this->l('Sube una imagen o un video (mp4/webm) o imagen (jpg/png/webp). Si no subes nada, se mantiene el actual.'),
                    'form_group_class' => 'psl-field psl-field--block5 ',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Eliminar archivo actual (Bloque 5)'),
                    'name' => 'block6_image_delete',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'block6_image_delete_on', 'value' => 1, 'label' => $this->l('Sí')],
                        ['id' => 'block6_image_delete_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                    'form_group_class' => 'psl-field psl-field--block5 ',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'block6_image_old',
                ],

                $this->cardEndInput('block5', 'js-tpl tpl-color'),

        ];
    }

    protected function addBlock6($b4Filename): array {
        return [
                // =========================
                // CARD: Bloque 6 (tpl-color)
                // =========================
                $this->cardStartInput('block6', 'Bloque 6', 'default', 'landing-admin-card js-tpl tpl-color'),

                [
                    'type' => 'text',
                    'label' => $this->l('Titulo Bloque 6'),
                    'name' => 'block7_title',
                    'lang' => true,
                    'form_group_class' => 'psl-field psl-field--block6 ',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Texto Bloque 6'),
                    'name' => 'block7_text',
                    'lang' => true,
                    'autoload_rte' => true,
                    'form_group_class' => 'psl-field psl-field--block6 ',
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('Archivo actual (Bloque 6)'),
                    'name' => 'block7_image_preview',
                    'html_content' => $this->renderMediaPreviewHtml((string)$b4Filename),
                    'form_group_class' => 'psl-field psl-field--block6 ',
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Archivo Bloque 6'),
                    'name' => 'block7_image',
                    'desc' => $this->l('Sube una imagen o un video (mp4/webm) o imagen (jpg/png/webp). Si no subes nada, se mantiene el actual.'),
                    'form_group_class' => 'psl-field psl-field--block6 ',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Eliminar archivo actual (Bloque 6)'),
                    'name' => 'block7_image_delete',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'block7_image_delete_on', 'value' => 1, 'label' => $this->l('Sí')],
                        ['id' => 'block7_image_delete_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                    'form_group_class' => 'psl-field psl-field--block6 ',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'block7_image_old',
                ],

                $this->cardEndInput('block6', 'js-tpl tpl-color'),

        ];
    }


    protected function addBlockCharacteristicks(): array {
        return [
                // =========================
                // CARD: Características (tpl-default)
                // =========================
                $this->cardStartInput('chars', 'Características', 'warning', 'landing-admin-card js-tpl tpl-default'),

                [
                    'type' => 'html',
                    'label' => $this->l('Características'),
                    'name' => 'characteristics_block',
                    'html_content' => $this->renderCharacteristicsBlock(),
                    'form_group_class' => 'psl-field psl-field--chars js-tpl tpl-default',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'characteristics_json',
                    'form_group_class' => 'js-tpl tpl-default',
                ],

                $this->cardEndInput('chars', 'js-tpl tpl-default'),

        ];
    }
    
    protected function addBlockCarousel(): array {
        return [

                // =========================
                // CARD: Carrusel
                // =========================
                $this->cardStartInput('carousel', 'Carrusel', 'success', 'js-tpl tpl-simple tpl-stone tpl-default landing-admin-card'),

                [
                    'type' => 'html',
                    'label' => $this->l('Carrusel'),
                    'name' => 'slides_block_1',
                    'html_content' => $this->renderSlidesBlock('carousel_1', 'slides_json_carousel_1', 'pslanding-slides-carousel-1', 'pslanding-slides-list-carousel-1', 'pslanding-add-slide-carousel-1'),
                    'form_group_class' => 'psl-field psl-field--carousel',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'slides_json_carousel_1',
                ],

                $this->cardEndInput('carousel'),


        ];
    }
    
    protected function addBlockCarousel2(): array {
        return [

                // =========================
                // CARD: Carrusel 2
                // =========================
                $this->cardStartInput('carousel2', 'Carrusel2', 'success', 'js-tpl tpl-stone landing-admin-card'),

                [
                    'type' => 'html',
                    'label' => $this->l('Carrusel'),
                    'name' => 'slides_block_2',
                    'html_content' => $this->renderSlidesBlock('carousel_2', 'slides_json_carousel_2', 'pslanding-slides-carousel-2', 'pslanding-slides-list-carousel-2', 'pslanding-add-slide-carousel-2'),
                    'form_group_class' => 'psl-field psl-field--carousel',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'slides_json_carousel_2',
                ],

                $this->cardEndInput('carousel2'),


        ];
    }
    

    protected function addBlockHero2($adminLink, $hero2ProductName, $hero2Filename): array {
        return [
            
                // =========================
                // CARD: Hero 2
                // =========================
                $this->cardStartInput('hero2', 'Hero 2', 'info', 'landing-admin-card js-tpl tpl-stone tpl-color'),

                [
                    'type' => 'textarea',
                    'label' => $this->l('Texto Slider'),
                    'name' => 'hero2_title',
                    'lang' => true,
                    'autoload_rte' => true,
                    'form_group_class' => 'psl-field psl-field--hero2',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Texto Botón'),
                    'name' => 'hero2_button',
                    'lang' => true,
                    'form_group_class' => 'psl-field psl-field--hero2',
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('Producto HERO 2'),
                    'name' => 'hero2_product_block',
                    'html_content' => '
                        <div id="pslanding-hero2-product" data-ajax-url="'.htmlspecialchars($adminLink, ENT_QUOTES, 'UTF-8').'">
                            <div style="display:flex;gap:8px;align-items:center;">
                                <input type="text" class="form-control"
                                    placeholder="'.$this->l('Buscar producto...').'"
                                    value="'.htmlspecialchars((string)$hero2ProductName, ENT_QUOTES, 'UTF-8').'"
                                    data-action="hero2-product-search">
                                <button type="button" class="btn btn-default" data-action="hero2-product-clear">'.$this->l('Limpiar').'</button>
                            </div>
                            <div class="help-block">'.$this->l('Escribe para buscar y haz click en un resultado. Se guardará el id_product.').'</div>
                            <div data-action="hero2-product-results" style="margin-top:6px"></div>
                        </div>
                    ',
                    'form_group_class' => 'psl-field psl-field--hero2',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'hero2_product',
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('Archivo actual (Slider)'),
                    'name' => 'hero2_media_preview',
                    'html_content' => $this->renderMediaPreviewHtml((string)$hero2Filename),
                    'form_group_class' => 'psl-field psl-field--hero2',
                ],
                [
                    'type'  => 'file',
                    'label' => $this->l('Archivo Slider (image/video)'),
                    'name'  => 'hero2_media',
                    'desc'  => $this->l('Sube una imagen o un video (mp4/webm) o imagen (jpg/png/webp). Si no subes nada, se mantiene el actual.'),
                    'form_group_class' => 'psl-field psl-field--hero2',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Eliminar archivo actual (Slider)'),
                    'name' => 'hero2_media_delete',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'hero2_media_delete_on', 'value' => 1, 'label' => $this->l('Sí')],
                        ['id' => 'hero2_media_delete_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                    'form_group_class' => 'psl-field psl-field--hero2',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'hero2_media_old',
                ],

                $this->cardEndInput('hero2'),

        ];
    }
}
