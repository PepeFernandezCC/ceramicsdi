<?php

class AdminInspirationcardsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'inspirationcards';
        $this->identifier = 'id_inspiration';
        $this->className = 'Inspirationcards';
        $this->bootstrap = true;
        $this->lang = true;

        parent::__construct();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJS($this->module->getPathUri().'views/js/admin.js');
        $this->addCSS($this->module->getPathUri().'views/css/admin.css');

    }

    public function renderList()
    {
        $this->fields_list = [
            'image' => [
                'title' => $this->l('Foto'),
                'align' => 'center',
                'callback' => 'renderImageColumn',
                'orderby' => false,
                'search' => false,
            ],
            'name' => [
                'title' => $this->l('Nombre'),
            ],
            'slug' => [
                'title' => $this->l('slug'),
            ],
            'active' => [
                'title' => $this->l('Activo'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'class' => 'fixed-width-sm',
            ],
        ];

        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'inspirationcards_lang il 
            ON (a.id_inspiration = il.id_inspiration AND il.id_lang='.(int)$this->context->language->id.')';

        $this->_select = 'il.name';

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderImageColumn($value, $row)
    {
        if (empty($value)) {
            return '-';
        }

        $url = $this->module->getPathUri().'uploads/'.rawurlencode($value);

        return '<img src="'.$url.'" style="max-width:60px; max-height:60px;" />';
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => 'Inspiración',
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->l('Activo'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Sí'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => 'Slug',
                    'name' => 'slug',
                    'required' => true,
                    'lang' => true,
                    'form_group_class' => 'ic-row ic-row-name',
                ],
                [
                    'type' => 'file',
                    'label' => 'Imagen',
                    'name' => 'image',
                    'display_image' => true,
                    'image' => $this->getPreviewImage(),
                    'form_group_class' => 'ic-row ic-col ic-col-left',
                ],
                [
                    'type' => 'checkbox',
                    'label' => 'Espacio',
                    'name' => 'espacio',
                    'values' => [
                        'query' => [
                            ['id' => 13, 'label' => 'Baño'],
                            ['id' => 12, 'label' => 'Cocina'],
                            ['id' => 14, 'label' => 'Salón'],
                            ['id' => 15, 'label' => 'Dormitorio'],
                            ['id' => 16, 'label' => 'Exterior'],
                            ['id' => 37, 'label' => 'Piscina'],
                        ],
                        'id' => 'id',
                        'name' => 'label',
                        
                    ],
                    'form_group_class' => 'ic-row ic-col ic-col-right',
                ],
                [
                    'type' => 'checkbox',
                    'label' => 'Uso',
                    'name' => 'uso',
                    'values' => [
                        'query' => [
                            ['id' => 1770, 'label' => 'Suelo'],
                            ['id' => 1771, 'label' => 'Pared'],
                            ['id' => 9999, 'label' => 'Moodboards'],
                        ],
                        'id' => 'id',
                        'name' => 'label',
                    ],
                    'form_group_class' => 'ic-row ic-col ic-col-right ic-col-right-second',
                ],
                                [
                    'type' => 'checkbox',
                    'label' => 'Material',
                    'name' => 'material',
                    'values' => [
                        'query' => [
                            ['id' => 11, 'label' => 'Azulejo'],
                            ['id' => 82, 'label' => 'Piedra'],
                            ['id' => 88, 'label' => 'Terracota'],
                            ['id' => 81, 'label' => 'Mosaicos'],
                        ],
                        'id' => 'id',
                        'name' => 'label',
                    ],
                    'form_group_class' => 'ic-row ic-col ic-col-right ic-col-right-third',
                ],
                [
                    'type' => 'html',
                    'label' => 'Productos relacionados',
                    'name' => 'products_block',
                    'html_content' => $this->renderProductsBlock(),
                    'form_group_class' => 'ic-row ic-row-products',
                ],
                
                [
                    'type' => 'html',
                    'label' => $this->l('Características'),
                    'name' => 'features_block',
                    'html_content' => $this->renderFeaturesBlock(),
                    'form_group_class' => 'ic-row ic-row-features',
                ],
            ],
            'submit' => [
                'title' => 'Guardar',
            ],
            'enctype' => 'multipart/form-data',
        ];

        return parent::renderForm();
    }

    public function getFieldsValue($obj)
    {
        $fields = parent::getFieldsValue($obj);

        if (!Validate::isLoadedObject($obj)) {
            $fields['active'] = 1;
            return $fields;
        }

        $categories = Db::getInstance()->executeS('
            SELECT id_category
            FROM '._DB_PREFIX_.'inspirationcards_category
            WHERE id_inspiration = '.(int) $obj->id
        );

        foreach ($categories as $cat) {
            $fields['espacio_'.$cat['id_category']] = 1;
            $fields['uso_'.$cat['id_category']] = 1;
            $fields['material_'.$cat['id_category']] = 1;
        }

        return $fields;
    }

    public function processSave()
    {
        $id = (int)Tools::getValue('id_inspiration');

        if ($id > 0 && empty($_FILES['image']['tmp_name'])) {
            $currentImage = Db::getInstance()->getValue('
                SELECT image
                FROM '._DB_PREFIX_.'inspirationcards
                WHERE id_inspiration = '.(int)$id
            );

            if ($currentImage) {
                $_POST['image'] = $currentImage;
            }
        }

        parent::processSave();

        $id = (int)$this->object->id;

        if (
            isset($_FILES['image']) &&
            isset($_FILES['image']['tmp_name']) &&
            !empty($_FILES['image']['tmp_name'])
        ) {
            $this->saveImageInspiration($id);
        }

        $this->saveCategoryInspiration($id);
        $this->saveProductInspiration($id);
        $this->saveFeatureInspiration($id);
       
    }

    protected function saveCategoryInspiration($id)
    {
        Db::getInstance()->delete('inspirationcards_category', 'id_inspiration = '.(int)$id);

        $espacios = [13, 12, 14, 15, 16, 37];
        foreach ($espacios as $cat) {
            if (Tools::getValue('espacio_'.$cat)) {
                Db::getInstance()->insert('inspirationcards_category', [
                    'id_inspiration' => (int)$id,
                    'id_category' => (int)$cat,
                ]);
            }
        }

        $usos = [1770, 1771, 9999];
        foreach ($usos as $cat) {
            if (Tools::getValue('uso_'.$cat)) {
                Db::getInstance()->insert('inspirationcards_category', [
                    'id_inspiration' => (int)$id,
                    'id_category' => (int)$cat,
                ]);
            }
        }
        $material =  [11, 82, 88, 81];
        foreach ($material as $cat) {
            if (Tools::getValue('material_'.$cat)) {
                Db::getInstance()->insert('inspirationcards_category', [
                    'id_inspiration' => (int)$id,
                    'id_category' => (int)$cat,
                ]);
            }
        }
    }

    protected function saveImageInspiration($id)
    {
        $uploadDir = _PS_MODULE_DIR_.$this->module->name.'/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (
            !isset($_FILES['image']) ||
            empty($_FILES['image']['tmp_name']) ||
            !is_uploaded_file($_FILES['image']['tmp_name'])
        ) {
            return;
        }

        $originalName = $_FILES['image']['name'];
        $extension = Tools::strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);

        if (empty($extension)) {
            $extension = 'jpg';
        }

        $safeBaseName = Tools::link_rewrite($baseName);
        if (empty($safeBaseName)) {
            $safeBaseName = 'imagen';
        }

        $fileName = (int)$id . '_' . $safeBaseName . '.' . $extension;
        $destination = $uploadDir . $fileName;

        // 🔥 Guardar nombre en todos los idiomas
        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            Db::getInstance()->update(
                'inspirationcards_lang',
                [
                    'name' => pSQL($fileName)
                ],
                'id_inspiration = '.(int)$id.' AND id_lang = '.(int)$lang['id_lang']
            );
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $oldImage = Db::getInstance()->getValue('
                SELECT image
                FROM '._DB_PREFIX_.'inspirationcards
                WHERE id_inspiration = '.(int)$id
            );

            Db::getInstance()->update(
                'inspirationcards',
                ['image' => pSQL($fileName)],
                'id_inspiration = '.(int)$id
            );

            if (!empty($oldImage) && $oldImage !== $fileName) {
                $oldPath = $uploadDir . $oldImage;
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }
    }

    protected function saveProductInspiration ($id) {

        // PRODUCTOS RELACIONADOS
        $productsJson = Tools::getValue('products_json');
        $products = json_decode($productsJson, true);

        Db::getInstance()->delete('inspirationcards_product', 'id_inspiration = '.(int)$id);

        if (is_array($products)) {
            $position = 0;

            foreach ($products as $product) {
                $idProduct = (int)($product['id_product'] ?? 0);
                $productType = trim((string)($product['product_type'] ?? 'suelo'));
                
                if ($idProduct <= 0) {
                    continue;
                }

                if (!in_array($productType, ['suelo', 'pared', 'ambas'])) {
                    $productType = 'suelo';
                }

                $position++;

                Db::getInstance()->insert('inspirationcards_product', [
                    'id_inspiration' => (int)$id,
                    'id_product' => (int)$idProduct,
                    'position' => (int)$position,
                    'product_type' => $productType,
                ]);
            }
        }
        
    }

    protected function saveFeatureInspiration($id) {
        
        $featuresJson = Tools::getValue('features_json');
        $features = json_decode($featuresJson, true);

        Db::getInstance()->delete('inspirationcards_feature', 'id_inspiration = '.(int)$id);

        if (is_array($features)) {
            $position = 0;

            foreach ($features as $feature) {
                $idFeature = (int)($feature['id_feature'] ?? 0);
                $idFeatureValue = (int)($feature['id_feature_value'] ?? 0);
                $customValue = trim((string)($feature['custom_value'] ?? ''));

                if ($idFeature <= 0) {
                    continue;
                }

                $position++;

                Db::getInstance()->insert('inspirationcards_feature', [
                    'id_inspiration' => (int)$id,
                    'id_feature' => (int)$idFeature,
                    'id_feature_value' => $idFeatureValue ?: null,
                    'custom_value' => pSQL($customValue),
                    'position' => (int)$position,
                ]);
            }
        }

    }

    protected function getPreviewImage()
    {
        if (!$this->object || empty($this->object->image)) {
            return '';
        }

        $url = $this->module->getPathUri().'uploads/'.$this->object->image;

        return '<img src="'.$url.'" style="max-width:400px; height:auto;" />';
    }

    protected function renderProductsBlock()
    {
        $idInspiration = (int)Tools::getValue('id_inspiration');
        $products = [];

        if ($idInspiration) {
            $products = Db::getInstance()->executeS('
                SELECT ip.id_product, ip.position, ip.product_type, pl.name
                FROM '._DB_PREFIX_.'inspirationcards_product ip
                INNER JOIN '._DB_PREFIX_.'product_lang pl
                    ON (pl.id_product = ip.id_product AND pl.id_lang='.(int)$this->context->language->id.')
                WHERE ip.id_inspiration='.(int)$idInspiration.'
                ORDER BY ip.position ASC, ip.id_product ASC
            ');
        }

        $adminLink = $this->context->link->getAdminLink($this->controller_name, true);
        if (is_array($adminLink)) {
            $adminLink = $adminLink['url'] ?? (reset($adminLink) ?: '');
        }
        $adminLink = (string)$adminLink;

        $json = json_encode([
            'items' => $products,
            'ajax_url' => $adminLink,
        ]);

        return '
        <div id="inspiration-products-block"
            data-config="'.htmlspecialchars($json, ENT_QUOTES, 'UTF-8').'">

            <div style="display:flex;gap:8px;align-items:center;margin-bottom:10px;">
                <input type="text"
                    id="inspiration-product-search"
                    class="form-control"
                    placeholder="'.$this->l('Buscar producto...').'">
            </div>

            <div id="inspiration-product-results" style="margin-bottom:15px;"></div>

            <div id="inspiration-products-list"></div>

            <input type="hidden" name="products_json" id="products_json" value="'.htmlspecialchars(json_encode($products), ENT_QUOTES, 'UTF-8').'">

            <p class="help-block">
                '.$this->l('Busca productos y añádelos a la inspiración.').'
            </p>
        </div>';
    }

    public function ajaxProcessSearchProducts()
    {
        $q = trim(Tools::getValue('q', ''));
        $id_lang = (int)$this->context->language->id;

        if ($q === '' || Tools::strlen($q) < 2) {
            header('Content-Type: application/json');
            die(json_encode([]));
        }

        $like = '%'.pSQL($q).'%';

        $rows = Db::getInstance()->executeS('
            SELECT p.id_product, pl.name
            FROM '._DB_PREFIX_.'product p
            INNER JOIN '._DB_PREFIX_.'product_lang pl
                ON (pl.id_product = p.id_product AND pl.id_lang='.(int)$id_lang.')
            WHERE pl.name LIKE "'.$like.'"
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

    protected function renderFeaturesBlock()
    {
        $idInspiration = (int)Tools::getValue('id_inspiration');
        $idLang = (int)$this->context->language->id;

        $items = [];
        if ($idInspiration) {
            $items = Db::getInstance()->executeS('
                SELECT ife.id_feature, ife.id_feature_value, ife.custom_value, ife.position,
                    fl.name AS feature_name,
                    fvl.value AS feature_value_name
                FROM '._DB_PREFIX_.'inspirationcards_feature ife
                LEFT JOIN '._DB_PREFIX_.'feature_lang fl
                    ON (fl.id_feature = ife.id_feature AND fl.id_lang='.(int)$idLang.')
                LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl
                    ON (fvl.id_feature_value = ife.id_feature_value AND fvl.id_lang='.(int)$idLang.')
                WHERE ife.id_inspiration='.(int)$idInspiration.'
                ORDER BY ife.position ASC, ife.id_inspiration_feature ASC
            ');
        }

        $features = Db::getInstance()->executeS('
            SELECT f.id_feature, fl.name
            FROM '._DB_PREFIX_.'feature f
            INNER JOIN '._DB_PREFIX_.'feature_lang fl
                ON (fl.id_feature = f.id_feature AND fl.id_lang='.(int)$idLang.')
            ORDER BY fl.name ASC
        ');

        $featureValues = [];
        foreach ($features as $feature) {
            $values = Db::getInstance()->executeS('
                SELECT fv.id_feature_value, fvl.value
                FROM '._DB_PREFIX_.'feature_value fv
                INNER JOIN '._DB_PREFIX_.'feature_value_lang fvl
                    ON (fvl.id_feature_value = fv.id_feature_value AND fvl.id_lang='.(int)$idLang.')
                WHERE fv.id_feature='.(int)$feature['id_feature'].'
                ORDER BY fvl.value ASC
            ');

            $featureValues[(int)$feature['id_feature']] = $values;
        }

        $json = json_encode([
            'items' => $items,
            'features' => $features,
            'feature_values' => $featureValues,
        ]);

        return '
        <div id="inspiration-features-block"
            data-config="'.htmlspecialchars($json, ENT_QUOTES, 'UTF-8').'">

            <div id="inspiration-features-list"></div>

            <div style="margin-top:15px;">
                <button type="button" class="btn btn-primary" id="inspiration-add-feature">
                    '.$this->l('Añadir característica').'
                </button>
            </div>

            <input type="hidden" name="features_json" id="features_json"
                value="'.htmlspecialchars(json_encode($items), ENT_QUOTES, 'UTF-8').'">
        </div>';
    }
}