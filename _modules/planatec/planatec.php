<?php

use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

if ( !defined( '_PS_VERSION_' ) ) {
	exit;
}

class Planatec extends Module implements WidgetInterface {
	
	public const CATEGORY_COLLECTION_ID  = '3';
	public const CATEGORY_BEST_SELLER_ID = '6';
	
	public const FEATURE_FORMATO_ID = '3';
	
	public const FAQ_NUM_QUESTIONS = 20;
	
	private $templateFile;
	private $bestSellersFile;
	
	public function __construct() {
		$this->name                   = 'planatec';
		$this->tab                    = 'front_office_features';
		$this->version                = '1.1.0';
		$this->author                 = 'Planatec';
		$this->need_instance          = 0;
		$this->ps_versions_compliancy = [
			'min' => '1.7.0',
			'max' => '1.7.9'
		];
		$this->bootstrap              = true;
		
		parent::__construct();
		
		$this->displayName = $this->l( 'Planatec' );
		$this->description = $this->l( 'Module working of Planatec' );
		
		$this->confirmUninstall = $this->l( 'Are you sure you want to uninstall?' );
		
		if ( !Configuration::get( 'PLANATEC_NAME' ) ) {
			$this->warning = $this->l( 'No name provided' );
		}
		
		$this->templateFile    = 'module:planatec/planatec.tpl';
		$this->bestSellersFile = 'module:planatec/views/templates/best_sellers/hook/planatec.tpl';
	}
	
	public function install() {
		if ( parent::install()
			&& $this->registerHook( 'displayHeader' )
			&& $this->registerHook( 'displayHome' )
			&& Configuration::updateValue( 'PLANATEC_NAME', 'Planatec' )
		) {
			$res = $this->createTables();
			
			return (bool) $res;
		}
		
		return false;
	}
	
	public function uninstall() {
		if ( parent::uninstall()
			&& Configuration::deleteByName( 'PLANATEC_NAME' )
		) {
			$res = $this->deleteTables();
			
			return (bool) $res;
		}
		
		return false;
	}
	
	protected function createTables() {
		$res = (bool) Db::getInstance()->execute(
			'
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'planatec_formatos` (
                `id_planatec_formatos` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_formato` int(10) unsigned NOT NULL,
                `url` varchar(255) NOT NULL,
                PRIMARY KEY (`id_planatec_formatos`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        '
		);
		
		$res &= (bool) Db::getInstance()->execute(
			'
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra` (
				`id_planatec_categorias_contenido_extra` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_categoria` int(10) unsigned NOT NULL,
				`content` text,
				`button_text` varchar(255),
				PRIMARY KEY (`id_planatec_categorias_contenido_extra`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
		'
		);
		
		$res &= (bool) Db::getInstance()->execute(
			'
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'planatec_coleccion_destacada` (
				`id_planatec_coleccion_destacada` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_coleccion` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id_planatec_coleccion_destacada`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
		'
		);
		
		$res &= (bool) Db::getInstance()->execute(
			'
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'planatec_home_section` (
			`id_planatec_home_section` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`image_url` varchar(255) NOT NULL,
			`content` text,
			`button_url` varchar(255),
			PRIMARY KEY (`id_planatec_home_section`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
			'
		);
		
		return $res;
	}
	
	protected function deleteTables() {
		$res = Db::getInstance()->execute(
			'
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'planatec_formatos`;
		'
		);
		
		$res &= Db::getInstance()->execute(
			'
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra`;
		'
		);
		
		$res &= Db::getInstance()->execute(
			'
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'planatec_coleccion_destacada`;
		'
		);
		
		$res &= Db::getInstance()->execute(
			'
			DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'planatec_home_section`;
			'
		);
		
		return $res;
	}
	
	public function getContent() {
		return $this->postProcess() . $this->displayFormImgFormatos() . $this->displayFormCategoriaContenidoExtra(
			) . $this->displayFormHomeSection() . $this->displayFormFeaturedCollection(
			) . $this->displayFormProductTransport() . $this->displayFormUsoMantenimiento(
			) . $this->displayFormEnviosDevoluciones() . $this->displayFormFAQ();
	}
	
	public function displayFormImgFormatos() {
		$dirImgFormatos = '/../modules/planatec/img/';
		
		/**
		 * Formulario para añadir imágenes a los formatos
		 */
		$helperFormImgFormatos = new HelperForm();
		
		$inputs = array();
		foreach ( $this->getFormatos() as $formato ) {
			$name = 'formato-' . $formato[ 'id_feature_value' ] . '-imagen';
			
			$inputFields = [
				'type'     => 'file',
				'label'    => $formato[ 'value' ],
				'name'     => $name,
				'size'     => 20,
				'required' => false
			];
			
			$row = Db::getInstance()->getRow(
				'
						SELECT * FROM `' . _DB_PREFIX_ . 'planatec_formatos`
						WHERE `id_formato` = ' . $formato[ 'id_feature_value' ] . '
					'
			);
			
			if ( $row ) {
				$imgUrl = $dirImgFormatos . DIRECTORY_SEPARATOR . $row[ 'url' ];
				$img    = '<div style="margin-bottom: 5px;"><img src="' . $imgUrl . '" class="img-thumbnail" width="100" loading="lazy"></div>';
				
				$inputFields[ 'display_image' ] = true;
				$inputFields[ 'image' ]         = $img;
			}
			
			$inputs[] = $inputFields;
			
			$helperFormImgFormatos->fields_value[ $name ] = Tools::getValue( $name, Configuration::get( $name ) );
		}
		
		$formImgFormatos = [
			'form' => [
				'legend' => [
					'title' => $this->l( 'Imágenes para los formatos' ),
				],
				'input'  => $inputs,
				'submit' => [
					'title' => $this->l( 'Save' ),
					'class' => 'btn btn-default pull-right',
					'icon'  => 'icon-save'
				],
			],
		];
		
		$helperFormImgFormatos->module          = $this;
		$helperFormImgFormatos->table           = $this->table;
		$helperFormImgFormatos->name_controller = $this->name;
		$helperFormImgFormatos->token           = Tools::getAdminTokenLite( 'AdminModules' );
		$helperFormImgFormatos->currentIndex    = AdminController::$currentIndex . '&' . http_build_query(
				[ 'configure' => $this->name ]
			);
		$helperFormImgFormatos->submit_action   = 'submitFormatos-' . $this->name;
		
		$helperFormImgFormatos->default_form_language = (int) Configuration::get( 'PS_LANG_DEFAULT' );
		
		return $helperFormImgFormatos->generateForm( [ $formImgFormatos ] );
	}
	
	public function displayFormCategoriaContenidoExtra() {
		/**
		 * Formulario para añadir contenido extra a las categorías
		 */
		$helperFormCategoriaContenidoExtra = new HelperForm();
		
		$inputs      = array();
		$fieldsValue = [];
		foreach ( Category::getCategories() as $categoryArray ) {
			foreach ( $categoryArray as $category ) {
				$category = $category[ 'infos' ];
				
				if ( $category[ 'id_category' ] !== "1" && $category[ 'id_category' ] !== "2" ) {
					$name = 'categoria-' . $category[ 'id_category' ] . '-contenido-extra';
					
					$inputFields = [
						'type'         => 'textarea',
						'label'        => $category[ 'name' ],
						'name'         => $name,
						'cols'         => 40,
						'rows'         => 10,
						'class'        => 'rte',
						'autoload_rte' => true,
						'required'     => false,
						'lang'         => true
					];
					
					$row = Db::getInstance()->getRow(
						'
								SELECT * FROM `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra`
								WHERE `id_categoria` = ' . $category[ 'id_category' ] . '
							'
					);
					
					if ( $row ) {
						foreach ( Language::getLanguages( false ) as $lang ) {
							$rowLang = Db::getInstance()->getRow(
								'
								SELECT * FROM `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra_lang`
								WHERE `id_planatec_categorias_contenido_extra` = ' . $row[ 'id_planatec_categorias_contenido_extra' ] . '
								AND `id_lang` = ' . $lang[ 'id_lang' ] . '
							'
							);
							
							$inputFields[ 'value' ][ $lang[ 'id_lang' ] ] = $rowLang[ 'content' ];
							$fieldsValue[ $name ][ $lang[ 'id_lang' ] ]   = $rowLang[ 'content' ];
						}
					}
					
					$inputs[] = $inputFields;
					
					$inputs[] = [
						'type'  => 'text',
						'label' => $this->l( 'Texto del botón' ) . ' ' . $category[ 'name' ],
						'name'  => 'categoria-' . $category[ 'id_category' ] . '-texto-boton',
						'desc'  => 'Si se deja en blanco, aparecerá "Ver todas las estancias"',
						'lang'  => true
					];
					
					if ( $row ) {
						foreach ( Language::getLanguages( false ) as $lang ) {
							$rowLang = Db::getInstance()->getRow(
								'
								SELECT * FROM `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra_lang`
								WHERE `id_planatec_categorias_contenido_extra` = ' . $row[ 'id_planatec_categorias_contenido_extra' ] . '
								AND `id_lang` = ' . $lang[ 'id_lang' ] . '
							'
							);
							
							$fieldsValue[ 'categoria-' . $category[ 'id_category' ] . '-texto-boton' ][ $lang[ 'id_lang' ] ] = $rowLang[ 'button_text' ];
						}
					}
					
					$helperFormCategoriaContenidoExtra->fields_value[ $name ] = Tools::getValue(
						$name,
						Configuration::get( $name )
					);
				}
			}
		}
		
		$formCategoriaContenidoExtra = [
			'form' => [
				'tinymce' => true,
				'legend'  => [
					'title' => $this->l( 'Contenido extra para las categorías' )
				],
				'input'   => $inputs,
				'submit'  => [
					'title' => $this->l( 'Save' ),
					'class' => 'btn btn-default pull-right',
					'icon'  => 'icon-save'
				],
			],
		];
		
		$helperFormCategoriaContenidoExtra->module          = $this;
		$helperFormCategoriaContenidoExtra->table           = $this->table;
		$helperFormCategoriaContenidoExtra->name_controller = $this->name;
		$helperFormCategoriaContenidoExtra->token           = Tools::getAdminTokenLite( 'AdminModules' );
		$helperFormCategoriaContenidoExtra->currentIndex    = AdminController::$currentIndex . '&' . http_build_query(
				[ 'configure' => $this->name ]
			);
		$helperFormCategoriaContenidoExtra->submit_action   = 'submitCategoriaContenidoExtra-' . $this->name;
		
		$helperFormCategoriaContenidoExtra->tpl_vars = [
			'fields_value' => $fieldsValue,
			'languages'    => $this->context->controller->getLanguages(),
			'id_language'  => $this->context->language->id,
			'language'     => [
				'id_lang'  => $this->context->language->id,
				'iso_code' => $this->context->language->iso_code
			]
		];
		
		$helperFormCategoriaContenidoExtra->default_form_language = (int) Configuration::get( 'PS_LANG_DEFAULT' );
		
		$helperFormCategoriaContenidoExtra->allow_employee_form_lang = Configuration::get(
			'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
		) ? Configuration::get( 'PS_BO_ALLOW_EMPLOYEE_FORM_LANG' ) : 0;
		
		return $helperFormCategoriaContenidoExtra->generateForm( [ $formCategoriaContenidoExtra ] );
	}
	
	public function displayFormFeaturedCollection() {
		/**
		 * Formulario para indicar la colección destacada
		 */
		$helperFormFeaturedCollection = new HelperForm();
		
		$options = $this->getCollections();
		
		$formFeaturedCollection = [
			'form' => [
				'legend' => [
					'title' => $this->l( 'Colección destacada' ),
				],
				'input'  => [
					[
						'type'     => 'select',
						'label'    => $this->l( 'Colección destacada' ),
						'name'     => 'featured-collection',
						'required' => true,
						'options'  => $options
					]
				],
				'submit' => [
					'title' => $this->l( 'Save' ),
					'class' => 'btn btn-default pull-right',
					'icon'  => 'icon-save'
				],
			],
		];
		
		$helperFormFeaturedCollection->fields_value[ 'featured-collection' ] = Tools::getValue(
			'featured-collection',
			Configuration::get(
				'featured-collection'
			)
		);
		
		$helperFormFeaturedCollection->module          = $this;
		$helperFormFeaturedCollection->table           = $this->table;
		$helperFormFeaturedCollection->name_controller = $this->name;
		$helperFormFeaturedCollection->token           = Tools::getAdminTokenLite( 'AdminModules' );
		$helperFormFeaturedCollection->currentIndex    = AdminController::$currentIndex . '&' . http_build_query(
				[ 'configure' => $this->name ]
			);
		$helperFormFeaturedCollection->submit_action   = 'submitColeccionDestacada-' . $this->name;
		
		$helperFormFeaturedCollection->tpl_vars = array(
			'fields_value' => array(
				'featured-collection' => Configuration::get( 'featured-collection' )
			)
		);
		
		$helperFormFeaturedCollection->default_form_language = (int) Configuration::get( 'PS_LANG_DEFAULT' );
		
		return $helperFormFeaturedCollection->generateForm( [ $formFeaturedCollection ] );
	}
	
	public function displayFormProductTransport() {
		/**
		 * Formulario para indicar el texto de transporte del producto
		 */
		$helperFormProductTransport = new HelperForm();
		
		$formProductTransport = [
			'form' => [
				'tinymce' => true,
				'legend'  => [
					'title' => $this->l( 'Información sobre transporte' ),
				],
				'input'   => [
					[
						'type'         => 'textarea',
						'label'        => $this->l( 'Entrega' ),
						'name'         => 'product-transport',
						'cols'         => 40,
						'rows'         => 10,
						'class'        => 'rte',
						'autoload_rte' => true,
						'required'     => true,
						'desc'         => $this->l(
							'Utiliza {dias_plazo} para introducir el plazo de entrega que se ha indicado en la ficha del producto.'
						),
						'lang'         => true
					],
					[
						'type'         => 'textarea',
						'label'        => $this->l( 'Muestras' ),
						'name'         => 'product-transport-samples',
						'cols'         => 40,
						'rows'         => 10,
						'class'        => 'rte',
						'autoload_rte' => true,
						'required'     => true,
						'desc'         => $this->l(
							'Utiliza {texto_muestra} para introducir el texto de la muestra que se ha indicado en la ficha del producto.'
						),
						'lang'         => true
					]
				],
				'submit'  => [
					'title' => $this->l( 'Save' ),
					'class' => 'btn btn-default pull-right',
					'icon'  => 'icon-save'
				],
			],
		];
		
		$fieldsValue = [];
		foreach ( Language::getLanguages( false ) as $lang ) {
			$fieldsValue[ 'product-transport' ][ $lang[ 'id_lang' ] ]         = Tools::getValue(
				'product-transport_' . $lang[ 'id_lang' ],
				Configuration::get(
					'product-transport',
					$lang[ 'id_lang' ]
				)
			);
			$fieldsValue[ 'product-transport-samples' ][ $lang[ 'id_lang' ] ] = Tools::getValue(
				'product-transport-samples_' . $lang[ 'id_lang' ],
				Configuration::get(
					'product-transport-samples',
					$lang[ 'id_lang' ]
				)
			);
		}
		
		$helperFormProductTransport->module          = $this;
		$helperFormProductTransport->table           = $this->table;
		$helperFormProductTransport->name_controller = $this->name;
		$helperFormProductTransport->token           = Tools::getAdminTokenLite( 'AdminModules' );
		$helperFormProductTransport->currentIndex    = AdminController::$currentIndex . '&' . http_build_query(
				[ 'configure' => $this->name ]
			);
		$helperFormProductTransport->submit_action   = 'submitProductTransport-' . $this->name;
		
		$helperFormProductTransport->tpl_vars = array(
			'fields_value' => $fieldsValue,
			'languages'    => $this->context->controller->getLanguages(),
			'id_language'  => $this->context->language->id,
			'language'     => [
				'id_lang'  => $this->context->language->id,
				'iso_code' => $this->context->language->iso_code
			]
		);
		
		$helperFormProductTransport->default_form_language = (int) Configuration::get( 'PS_LANG_DEFAULT' );
		
		$helperFormProductTransport->allow_employee_form_lang = Configuration::get(
			'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
		) ? Configuration::get( 'PS_BO_ALLOW_EMPLOYEE_FORM_LANG' ) : 0;
		
		return $helperFormProductTransport->generateForm( [ $formProductTransport ] );
	}
	
	public function displayFormUsoMantenimiento() {
		/**
		 * Formulario para indicar el texto de uso y mantenimiento
		 */
		$helperFormUsoMantenimiento = new HelperForm();
		
		$formUsoMantenimiento = [
			'form' => [
				'tinymce' => true,
				'legend'  => [
					'title' => $this->l( 'Información sobre uso y mantenimiento' ),
				],
				'input'   => [
					[
						'type'         => 'textarea',
						'label'        => $this->l( 'Información sobre uso y mantenimiento' ),
						'name'         => 'product-uso-mantenimiento',
						'cols'         => 40,
						'rows'         => 20,
						'class'        => 'rte',
						'autoload_rte' => true,
						'required'     => true,
						'lang'         => true
					]
				],
				'submit'  => [
					'title' => $this->l( 'Save' ),
					'class' => 'btn btn-default pull-right',
					'icon'  => 'icon-save'
				],
			],
		];
		
		$fieldsValue = [];
		foreach ( Language::getLanguages( false ) as $lang ) {
			$fieldsValue[ 'product-uso-mantenimiento' ][ $lang[ 'id_lang' ] ] = Tools::getValue(
				'product-uso-mantenimiento_' . $lang[ 'id_lang' ],
				Configuration::get(
					'product-uso-mantenimiento',
					$lang[ 'id_lang' ]
				)
			);
		}
		
		$helperFormUsoMantenimiento->module          = $this;
		$helperFormUsoMantenimiento->table           = $this->table;
		$helperFormUsoMantenimiento->name_controller = $this->name;
		$helperFormUsoMantenimiento->token           = Tools::getAdminTokenLite( 'AdminModules' );
		$helperFormUsoMantenimiento->currentIndex    = AdminController::$currentIndex . '&' . http_build_query(
				[ 'configure' => $this->name ]
			);
		$helperFormUsoMantenimiento->submit_action   = 'submitProductUsoMantenimiento-' . $this->name;
		
		$helperFormUsoMantenimiento->tpl_vars = array(
			'fields_value' => $fieldsValue,
			'languages'    => $this->context->controller->getLanguages(),
			'id_language'  => $this->context->language->id,
			'language'     => [
				'id_lang'  => $this->context->language->id,
				'iso_code' => $this->context->language->iso_code
			]
		);
		
		$helperFormUsoMantenimiento->default_form_language = (int) Configuration::get( 'PS_LANG_DEFAULT' );
		
		$helperFormUsoMantenimiento->allow_employee_form_lang = Configuration::get(
			'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
		) ? Configuration::get( 'PS_BO_ALLOW_EMPLOYEE_FORM_LANG' ) : 0;
		
		return $helperFormUsoMantenimiento->generateForm( [ $formUsoMantenimiento ] );
	}
	
	public function displayFormEnviosDevoluciones() {
		/**
		 * Formulario para indicar el texto de envíos y devoluciones
		 */
		$helperFormEnviosDevoluciones = new HelperForm();
		
		$formEnviosDevoluciones = [
			'form' => [
				'tinymce' => true,
				'legend'  => [
					'title' => $this->l( 'Información sobre envíos y devoluciones' ),
				],
				'input'   => [
					[
						'type'         => 'textarea',
						'label'        => $this->l( 'Información sobre envíos y devoluciones' ),
						'name'         => 'product-envios-devoluciones',
						'cols'         => 40,
						'rows'         => 20,
						'class'        => 'rte',
						'autoload_rte' => true,
						'required'     => true,
						'lang'         => true
					]
				],
				'submit'  => [
					'title' => $this->l( 'Save' ),
					'class' => 'btn btn-default pull-right',
					'icon'  => 'icon-save'
				],
			],
		];
		
		$fieldsValue = [];
		foreach ( Language::getLanguages( false ) as $lang ) {
			$fieldsValue[ 'product-envios-devoluciones' ][ $lang[ 'id_lang' ] ] = Tools::getValue(
				'product-envios-devoluciones_' . $lang[ 'id_lang' ],
				Configuration::get(
					'product-envios-devoluciones',
					$lang[ 'id_lang' ]
				)
			);
		}
		
		$helperFormEnviosDevoluciones->module          = $this;
		$helperFormEnviosDevoluciones->table           = $this->table;
		$helperFormEnviosDevoluciones->name_controller = $this->name;
		$helperFormEnviosDevoluciones->token           = Tools::getAdminTokenLite( 'AdminModules' );
		$helperFormEnviosDevoluciones->currentIndex    = AdminController::$currentIndex . '&' . http_build_query(
				[ 'configure' => $this->name ]
			);
		$helperFormEnviosDevoluciones->submit_action   = 'submitProductEnviosDevoluciones-' . $this->name;
		
		$helperFormEnviosDevoluciones->tpl_vars = array(
			'fields_value' => $fieldsValue,
			'languages'    => $this->context->controller->getLanguages(),
			'id_language'  => $this->context->language->id,
			'language'     => [
				'id_lang'  => $this->context->language->id,
				'iso_code' => $this->context->language->iso_code
			]
		);
		
		$helperFormEnviosDevoluciones->default_form_language = (int) Configuration::get( 'PS_LANG_DEFAULT' );
		
		$helperFormEnviosDevoluciones->allow_employee_form_lang = Configuration::get(
			'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
		) ? Configuration::get( 'PS_BO_ALLOW_EMPLOYEE_FORM_LANG' ) : 0;
		
		return $helperFormEnviosDevoluciones->generateForm( [ $formEnviosDevoluciones ] );
	}
	
	public function displayFormHomeSection() {
		$dirImgHomeSection = '/../modules/planatec/img/';
		
		/**
		 * Formulario para añadir imagen y contenido a la página de Inicio
		 */
		$helperFormHomeSection = new HelperForm();
		
		$row = Db::getInstance()->getRow(
			'
			SELECT * FROM `' . _DB_PREFIX_ . 'planatec_home_section`
			'
		);
		
		$inputImageField = [
			'type'     => 'file',
			'label'    => $this->l( 'Imagen' ),
			'name'     => 'home-section-image',
			'size'     => 20,
			'required' => true
		];
		
		$fieldsValue = [];
		if ( $row ) {
			$imgUrl = $dirImgHomeSection . DIRECTORY_SEPARATOR . $row[ 'image_url' ];
			$img    = '<div style="margin-bottom: 5px;"><img src="' . $imgUrl . '" class="img-thumbnail" width="600" loading="lazy"></div>';
			
			$inputImageField[ 'display_image' ] = true;
			$inputImageField[ 'image' ]         = $img;
			
			foreach ( Language::getLanguages( false ) as $lang ) {
				$rowLang = Db::getInstance()->getRow(
					'
					SELECT * FROM `' . _DB_PREFIX_ . 'planatec_home_section_lang`
					WHERE `id_planatec_home_section` = ' . $row[ 'id_planatec_home_section' ] . '
					AND `id_lang` = ' . $lang[ 'id_lang' ] . '
					'
				);
				
				$fieldsValue[ 'home-section-content' ][ $lang[ 'id_lang' ] ]    = $rowLang[ 'content' ];
				$fieldsValue[ 'home-section-button-url' ][ $lang[ 'id_lang' ] ] = $rowLang[ 'button_url' ];
			}
		}
		
		$formHomeSection = [
			'form' => [
				'tinymce' => true,
				'legend'  => [
					'title' => $this->l( 'Sección para la página de Inicio' )
				],
				'input'   => [
					$inputImageField,
					[
						'type'         => 'textarea',
						'label'        => $this->l( 'Contenido' ),
						'name'         => 'home-section-content',
						'cols'         => 40,
						'rows'         => 10,
						'class'        => 'rte',
						'autoload_rte' => true,
						'required'     => false,
						'lang'         => true
					],
					[
						'type'     => 'text',
						'label'    => $this->l( 'URL para el botón' ),
						'name'     => 'home-section-button-url',
						'required' => false,
						'lang'     => true
					]
				],
				'submit'  => [
					'title' => $this->l( 'Save' ),
					'class' => 'btn btn-default pull-right',
					'icon'  => 'icon-save'
				]
			]
		];
		
		foreach ( Language::getLanguages( false ) as $lang ) {
			$helperFormHomeSection->fields_value[ 'home-section-content' ][ $lang[ 'id_lang' ] ]    = Tools::getValue(
				'home-section-content_' . $lang[ 'id_lang' ]
			);
			$helperFormHomeSection->fields_value[ 'home-section-button-url' ][ $lang[ 'id_lang' ] ] = Tools::getValue(
				'home-section-button-url_' . $lang[ 'id_lang' ]
			);
		}
		
		$helperFormHomeSection->module          = $this;
		$helperFormHomeSection->table           = $this->table;
		$helperFormHomeSection->name_controller = $this->name;
		$helperFormHomeSection->token           = Tools::getAdminTokenLite( 'AdminModules' );
		$helperFormHomeSection->currentIndex    = AdminController::$currentIndex . '&' . http_build_query(
				[ 'configure' => $this->name ]
			);
		
		$helperFormHomeSection->submit_action = 'submitHomeSection-' . $this->name;
		
		$helperFormHomeSection->tpl_vars = array(
			'fields_value' => $fieldsValue,
			'languages'    => $this->context->controller->getLanguages(),
			'id_language'  => $this->context->language->id,
			'language'     => [
				'id_lang'  => $this->context->language->id,
				'iso_code' => $this->context->language->iso_code
			]
		);
		
		$helperFormHomeSection->default_form_language = (int) Configuration::get( 'PS_LANG_DEFAULT' );
		
		$helperFormHomeSection->allow_employee_form_lang = Configuration::get(
			'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
		) ? Configuration::get( 'PS_BO_ALLOW_EMPLOYEE_FORM_LANG' ) : 0;
		
		return $helperFormHomeSection->generateForm( [ $formHomeSection ] );
	}
	
	public function displayFormFAQ() {
		/**
		 * Formulario para indicar las preguntas FAQ
		 */
		$helperFormFAQ = new HelperForm();
		
		$inputs      = [];
		$fieldsValue = [];
		for ( $i = 1; $i <= self::FAQ_NUM_QUESTIONS; $i++ ) {
			$inputs[] = [
				'type'  => 'text',
				'label' => $this->l( 'Pregunta' ) . ' ' . $i,
				'name'  => 'faq-question-title-' . $i,
				'lang'  => true
			];
			
			$inputs[] = [
				'type'         => 'textarea',
				'label'        => $this->l( 'Respuesta' ) . ' ' . $i,
				'name'         => 'faq-question-answer-' . $i,
				'cols'         => 20,
				'rows'         => 10,
				'class'        => 'rte',
				'autoload_rte' => true,
				'lang'         => true
			];
			
			foreach ( Language::getLanguages( false ) as $lang ) {
				$fieldsValue[ 'faq-question-title-' . $i ][ $lang[ 'id_lang' ] ] = Tools::getValue(
					'faq-question-title-' . $i . '_' . $lang[ 'id_lang' ],
					Configuration::get(
						'faq-question-title-' . $i,
						$lang[ 'id_lang' ]
					)
				);
				
				$fieldsValue[ 'faq-question-answer-' . $i ][ $lang[ 'id_lang' ] ] = Tools::getValue(
					'faq-question-answer-' . $i . '_' . $lang[ 'id_lang' ],
					Configuration::get(
						'faq-question-answer-' . $i,
						$lang[ 'id_lang' ]
					)
				);
			}
		}
		
		$formFAQ = [
			'form' => [
				'tinymce' => true,
				'legend'  => [
					'title' => $this->l( 'FAQ' )
				],
				'input'   => $inputs,
				'submit'  => [
					'title' => $this->l( 'Save' ),
					'class' => 'btn btn-default pull-right',
					'icon'  => 'icon-save'
				]
			]
		];
		
		$helperFormFAQ->module          = $this;
		$helperFormFAQ->table           = $this->table;
		$helperFormFAQ->name_controller = $this->name;
		$helperFormFAQ->token           = Tools::getAdminTokenLite( 'AdminModules' );
		$helperFormFAQ->currentIndex    = AdminController::$currentIndex . '&' . http_build_query(
				[ 'configure' => $this->name ]
			);
		$helperFormFAQ->submit_action   = 'submitFAQ-' . $this->name;
		
		$helperFormFAQ->tpl_vars = [
			'fields_value' => $fieldsValue,
			'languages'    => $this->context->controller->getLanguages(),
			'id_language'  => $this->context->language->id,
			'language'     => [
				'id_lang'  => $this->context->language->id,
				'iso_code' => $this->context->language->iso_code
			]
		];
		
		$helperFormFAQ->default_form_language = (int) Configuration::get( 'PS_LANG_DEFAULT' );
		
		$helperFormFAQ->allow_employee_form_lang = Configuration::get(
			'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
		) ? Configuration::get( 'PS_BO_ALLOW_EMPLOYEE_FORM_LANG' ) : 0;
		
		return $helperFormFAQ->generateForm( [ $formFAQ ] );
	}
	
	public function postProcess() {
		$id_lang = (int) $this->context->language->id;
		
		if ( Tools::isSubmit( 'submitFormatos-' . $this->name ) ) {
			$values              = [];
			$update_image_values = false;
			
			foreach ( $this->getFormatos() as $formato ) {
				$name = 'formato-' . $formato[ 'id_feature_value' ] . '-imagen';
				
				if ( isset( $_FILES[ $name ] ) && isset( $_FILES[ $name ][ 'tmp_name' ] ) ) {
					if ( !empty( $_FILES[ $name ][ 'tmp_name' ] ) ) {
						if ( $error = ImageManager::validateUpload( $_FILES[ $name ], 4000000 ) ) {
							return $this->displayError( $error );
						} else {
							$ext       = substr( $_FILES[ $name ][ 'name' ], strrpos( $_FILES[ $name ][ 'name' ], '.' ) + 1 );
							$file_name = $formato[ 'id_feature_value' ] . '_image.' . $ext;
							
							if ( !is_dir( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'img' ) ) {
								mkdir( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'img' );
							}
							
							if ( !move_uploaded_file(
								$_FILES[ $name ][ 'tmp_name' ],
								dirname(
									__FILE__
								) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name
							) ) {
								return $this->displayError(
									$this->trans(
										'An error occurred while attempting to upload the file.',
										[],
										'Admin.Notifications.Error'
									)
								);
							} else {
								if ( Configuration::hasContext( $name, $id_lang, Shop::getContext() )
									&& Configuration::get( $name, $id_lang ) != $file_name ) {
									@unlink(
										dirname(
											__FILE__
										) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . Configuration::get(
											$name,
											$id_lang
										)
									);
								}
								
								$values[ $name ] = $file_name;
							}
						}
						
						$update_image_values = true;
					}
				}
				
				if ( $update_image_values && isset( $values[ $name ] ) ) {
					$res = Db::getInstance()->execute(
						'
						SELECT * FROM `' . _DB_PREFIX_ . 'planatec_formatos`
						WHERE `id_formato` = ' . $formato[ 'id_feature_value' ] . '
					'
					);
					
					if ( ( $res || is_array( $res ) ) && Db::getInstance()->numRows() === 0 ) {
						Db::getInstance()->execute(
							'
							INSERT INTO `' . _DB_PREFIX_ . 'planatec_formatos` (`id_formato`, `url`)
							VALUES (' . $formato[ 'id_feature_value' ] . ', "' . $values[ $name ] . '")
					'
						);
					}
				}
			}
			
			$this->_clearCache( $this->templateFile );
			
			return $this->displayConfirmation(
				$this->trans( 'The settings have been updated.', [], 'Admin.Notifications.Success' )
			);
		} elseif ( Tools::isSubmit( 'submitCategoriaContenidoExtra-' . $this->name ) ) {
			foreach ( Category::getCategories() as $categoryArray ) {
				foreach ( $categoryArray as $category ) {
					$category = $category[ 'infos' ];
					
					if ( $category[ 'id_category' ] !== "1" && $category[ 'id_category' ] !== "2" ) {
						foreach ( Language::getLanguages( false ) as $lang ) {
							$name = 'categoria-' . $category[ 'id_category' ] . '-contenido-extra_' . $lang[ 'id_lang' ];
							
							$res = Db::getInstance()->executeS(
								'
							SELECT * FROM `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra`
							WHERE `id_categoria` = ' . $category[ 'id_category' ] . '
							'
							);
							
							if ( ( $res || is_array( $res ) ) && Db::getInstance()->numRows() === 0 ) {
								Db::getInstance()->execute(
									'
								INSERT INTO `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra` (`id_categoria`)
								VALUES (' . $category[ 'id_category' ] . ' )
								'
								);
								$idInserted = Db::getInstance()->Insert_ID();
								Db::getInstance()->execute(
									'
									INSERT INTO `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra_lang` (`id_planatec_categorias_contenido_extra`, `id_lang`, `content`, `button_text`)
									VALUES (' . $idInserted . ', ' . $lang[ 'id_lang' ] . ', "' . str_replace(
										'"',
										'\"',
										Tools::getValue( $name )
									) . '", "' . Tools::getValue(
										'categoria-' . $category[ 'id_category' ] . '-texto-boton_' . $lang[ 'id_lang' ],
										''
									) . '")
									'
								);
							} else {
								$resLang = Db::getInstance()->execute(
									'
									SELECT * FROM `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra_lang`
									WHERE `id_planatec_categorias_contenido_extra` = ' . $res[ 0 ][ 'id_planatec_categorias_contenido_extra' ] . '
									AND `id_lang` = ' . $lang[ 'id_lang' ] . '
									'
								);
								
								if ( ( $resLang || is_array( $resLang ) ) && Db::getInstance()->numRows() === 0 ) {
									Db::getInstance()->execute(
										'
									INSERT INTO `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra_lang` (`id_planatec_categorias_contenido_extra`, `id_lang`, `content`, `button_text`)
									VALUES (' . $res[ 0 ][ 'id_planatec_categorias_contenido_extra' ] . ', ' . $lang[ 'id_lang' ] . ', "' . str_replace(
											'"',
											'\"',
											Tools::getValue( $name )
										) . '", "' . Tools::getValue(
											'categoria-' . $category[ 'id_category' ] . '-texto-boton_' . $lang[ 'id_lang' ],
											''
										) . '")
									'
									);
								} else {
									Db::getInstance()->execute(
										'
										UPDATE `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra_lang`
										SET `content` = "' . str_replace( '"', '\"', Tools::getValue( $name ) ) . '",
										`button_text` = "' . Tools::getValue(
											'categoria-' . $category[ 'id_category' ] . '-texto-boton_' . $lang[ 'id_lang' ],
											''
										) . '"
										WHERE `id_planatec_categorias_contenido_extra` = ' . $res[ 0 ][ 'id_planatec_categorias_contenido_extra' ] . '
										AND `id_lang` = ' . $lang[ 'id_lang' ] . '
										'
									);
								}
							}
						}
					}
				}
			}
			
			$this->_clearCache( $this->templateFile );
			
			return $this->displayConfirmation(
				$this->trans( 'The settings have been updated.', [], 'Admin.Notifications.Success' )
			);
		} elseif ( Tools::isSubmit( 'submitColeccionDestacada-' . $this->name ) ) {
			Configuration::updateValue( 'featured-collection', Tools::getValue( 'featured-collection' ) );
			
			$this->_clearCache( $this->templateFile );
			
			return $this->displayConfirmation(
				$this->trans( 'The settings have been updated.', [], 'Admin.Notifications.Success' )
			);
		} elseif ( Tools::isSubmit( 'submitProductTransport-' . $this->name ) ) {
			$productTransportValues        = [];
			$productTransportSamplesValues = [];
			foreach ( Language::getLanguages( false ) as $lang ) {
				$productTransportValues[ $lang[ 'id_lang' ] ]        = Tools::getValue(
					'product-transport_' . $lang[ 'id_lang' ]
				);
				$productTransportSamplesValues[ $lang[ 'id_lang' ] ] = Tools::getValue(
					'product-transport-samples_' . $lang[ 'id_lang' ]
				);
			}
			
			Configuration::updateValue( 'product-transport', $productTransportValues, true );
			Configuration::updateValue(
				'product-transport-samples',
				$productTransportSamplesValues,
				true
			);
			
			$this->_clearCache( $this->templateFile );
			
			return $this->displayConfirmation(
				$this->trans( 'The settings have been updated.', [], 'Admin.Notifications.Success' )
			);
		} elseif ( Tools::isSubmit( 'submitProductUsoMantenimiento-' . $this->name ) ) {
			$productUsoMantenimiento = [];
			foreach ( Language::getLanguages( false ) as $lang ) {
				$productUsoMantenimiento[ $lang[ 'id_lang' ] ] = Tools::getValue(
					'product-uso-mantenimiento_' . $lang[ 'id_lang' ]
				);
			}
			
			Configuration::updateValue(
				'product-uso-mantenimiento',
				$productUsoMantenimiento,
				true
			);
			
			$this->_clearCache( $this->templateFile );
			
			return $this->displayConfirmation(
				$this->trans( 'The settings have been updated.', [], 'Admin.Notifications.Success' )
			);
		} elseif ( Tools::isSubmit( 'submitProductEnviosDevoluciones-' . $this->name ) ) {
			$productEnviosDevoluciones = [];
			foreach ( Language::getLanguages( false ) as $lang ) {
				$productEnviosDevoluciones[ $lang[ 'id_lang' ] ] = Tools::getValue(
					'product-envios-devoluciones_' . $lang[ 'id_lang' ]
				);
			}
			
			Configuration::updateValue(
				'product-envios-devoluciones',
				$productEnviosDevoluciones,
				true
			);
			
			$this->_clearCache( $this->templateFile );
			
			return $this->displayConfirmation(
				$this->trans( 'The settings have been updated.', [], 'Admin.Notifications.Success' )
			);
		} elseif ( Tools::isSubmit( 'submitHomeSection-' . $this->name ) ) {
			$values              = [];
			$update_image_values = false;
			
			$name = 'home-section-image';
			
			if ( isset( $_FILES[ $name ] ) && isset( $_FILES[ $name ][ 'tmp_name' ] ) ) {
				if ( !empty( $_FILES[ $name ][ 'tmp_name' ] ) ) {
					if ( $error = ImageManager::validateUpload( $_FILES[ $name ], 4000000 ) ) {
						return $this->displayError( $error );
					} else {
						$ext       = substr( $_FILES[ $name ][ 'name' ], strrpos( $_FILES[ $name ][ 'name' ], '.' ) + 1 );
						$file_name = 'home-section-image.' . $ext;
						
						if ( !is_dir( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'img' ) ) {
							mkdir( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'img' );
						}
						
						if ( !move_uploaded_file(
							$_FILES[ $name ][ 'tmp_name' ],
							dirname(
								__FILE__
							) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name
						) ) {
							return $this->displayError(
								$this->trans(
									'An error ocurred while attempting to upload the file.',
									[],
									'Admin.Notifications.Error'
								)
							);
						} else {
							if ( Configuration::hasContext( $name, $id_lang, Shop::getContext() )
								&& Configuration::get( $name, $id_lang ) != $file_name ) {
								@unlink(
									dirname(
										__FILE__
									) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . Configuration::get(
										$name,
										$id_lang
									)
								);
							}
							
							$values[ $name ] = $file_name;
						}
					}
					
					$update_image_values = true;
				}
			}
			
			$res        = Db::getInstance()->executeS(
				'
					SELECT * FROM `' . _DB_PREFIX_ . 'planatec_home_section`
					'
			);
			$resNumRows = Db::getInstance()->numRows();
			
			foreach ( Language::getLanguages( false ) as $lang ) {
				$content   = Tools::getValue( 'home-section-content_' . $lang[ 'id_lang' ] );
				$buttonUrl = Tools::getValue( 'home-section-button-url_' . $lang[ 'id_lang' ] );
				
				if ( $update_image_values && isset( $values[ $name ] ) ) {
					if ( $res && $resNumRows === 0 ) {
						Db::getInstance()->execute(
							'
						INSERT INTO `' . _DB_PREFIX_ . 'planatec_home_section` (`image_url`)
						VALUES ("' . $values[ $name ] . '")
						'
						);
						$idInserted = Db::getInstance()->Insert_ID();
						Db::getInstance()->execute(
							'
							INSERT INTO `' . _DB_PREFIX_ . 'planatec_home_section_lang` (`id_planatec_home_section`, `content`, `button_url`, `id_lang`)
							VALUES (' . $idInserted . ', "' . $content . '", "' . $buttonUrl . '", ' . $lang[ 'id_lang' ] . ')
							'
						);
					} else {
						Db::getInstance()->execute(
							'
							UPDATE `' . _DB_PREFIX_ . 'planatec_home_section`
							SET `image_url` = "' . $values[ $name ] . '
							WHERE `id_planatec_home_section` = ' . $res[ 0 ][ 'id_planatec_home_section' ] . '
							'
						);
						
						$resLang = Db::getInstance()->execute(
							'
							SELECT * FROM `' . _DB_PREFIX_ . 'planatec_home_section_lang`
							WHERE `id_planatec_home_section` = ' . $res[ 0 ][ 'id_planatec_home_section' ] . '
							AND `id_lang` = ' . $lang[ 'id_lang' ] . '
							'
						);
						
						if ( ( $resLang || is_array( $resLang ) ) && Db::getInstance()->numRows() === 0 ) {
							Db::getInstance()->execute(
								'
									INSERT INTO `' . _DB_PREFIX_ . 'planatec_home_section_lang` (`id_planatec_home_section`, `id_lang`, `content`, `button_url`)
									VALUES (' . $res[ 0 ][ 'id_planatec_home_section' ] . ', ' . $lang[ 'id_lang' ] . ', "' . $content . '", "' . $buttonUrl . '")
									'
							);
						} else {
							Db::getInstance()->execute(
								'
								UPDATE `' . _DB_PREFIX_ . 'planatec_home_section_lang`
								SET `content` = "' . $content . '",
								`button_url` = "' . $buttonUrl . '"
								WHERE `id_planatec_home_section` = ' . $res[ 0 ][ 'id_planatec_home_section' ] . '
								AND `id_lang` = ' . $lang[ 'id_lang' ] . '
								'
							);
						}
					}
				} else {
					if ( $res && $resNumRows === 0 ) {
						Db::getInstance()->execute(
							'
						INSERT INTO `' . _DB_PREFIX_ . 'planatec_home_section` (`content`, `button_url`)
						VALUES ("' . $content . '", "' . $buttonUrl . '")
						'
						);
						$idInserted = Db::getInstance()->Insert_ID();
						Db::getInstance()->execute(
							'
							INSERT INTO `' . _DB_PREFIX_ . 'planatec_home_section_lang` (`id_planatec_home_section`, `content`, `button_url`, `id_lang`)
							VALUES (' . $idInserted . ', "' . $content . '", "' . $buttonUrl . '", ' . $lang[ 'id_lang' ] . ')
							'
						);
					} else {
						$resLang = Db::getInstance()->execute(
							'
							SELECT * FROM `' . _DB_PREFIX_ . 'planatec_home_section_lang`
							WHERE `id_planatec_home_section` = ' . $res[ 0 ][ 'id_planatec_home_section' ] . '
							AND `id_lang` = ' . $lang[ 'id_lang' ] . '
							'
						);
						
						if ( ( $resLang || is_array( $resLang ) ) && Db::getInstance()->numRows() === 0 ) {
							Db::getInstance()->execute(
								'
									INSERT INTO `' . _DB_PREFIX_ . 'planatec_home_section_lang` (`id_planatec_home_section`, `id_lang`, `content`, `button_url`)
									VALUES (' . $res[ 0 ][ 'id_planatec_home_section' ] . ', ' . $lang[ 'id_lang' ] . ', "' . $content . '", "' . $buttonUrl . '")
									'
							);
						} else {
							Db::getInstance()->execute(
								'
									UPDATE `' . _DB_PREFIX_ . 'planatec_home_section_lang`
									SET `content` = "' . $content . '",
									`button_url` = "' . $buttonUrl . '"
									WHERE `id_planatec_home_section` = ' . $res[ 0 ][ 'id_planatec_home_section' ] . '
									AND `id_lang`= ' . $lang[ 'id_lang' ] . '
									'
							);
						}
					}
				}
			}
			
			$this->_clearCache( $this->templateFile );
			
			return $this->displayConfirmation(
				$this->trans( 'The settings have been updated.', [], 'Admin.Notifications.Success' )
			);
		} elseif ( Tools::isSubmit( 'submitFAQ-' . $this->name ) ) {
			for ( $i = 1; $i < self::FAQ_NUM_QUESTIONS; $i++ ) {
				$faqQuestionTitleValues  = [];
				$faqQuestionAnswerValues = [];
				
				foreach ( Language::getLanguages( false ) as $lang ) {
					$faqQuestionTitleValues[ $lang[ 'id_lang' ] ]  = Tools::getValue(
						'faq-question-title-' . $i . '_' . $lang[ 'id_lang' ]
					);
					$faqQuestionAnswerValues[ $lang[ 'id_lang' ] ] = Tools::getValue(
						'faq-question-answer-' . $i . '_' . $lang[ 'id_lang' ]
					);
				}
				
				Configuration::updateValue( 'faq-question-title-' . $i, $faqQuestionTitleValues );
				Configuration::updateValue( 'faq-question-answer-' . $i, $faqQuestionAnswerValues, true );
			}
			
			$this->_clearCache( $this->templateFile );
			
			return $this->displayConfirmation(
				$this->trans( 'The settings have been updated.', [], 'Admin.Notifications.Success' )
			);
		}
		
		return '';
	}
	
	private function getFormatos() {
		$id_lang = (int) $this->context->language->id;
		
		$features = Feature::getFeatures( $id_lang );
		$formatos = array();
		foreach ( $features as &$feature ) {
			if ( $feature[ 'id_feature' ] === self::FEATURE_FORMATO_ID ) {
				$formatos[] = FeatureValue::getFeatureValuesWithLang(
					(int) $this->context->language->id,
					$feature[ 'id_feature' ],
					true
				);
			}
		}
		
		return $formatos[ 0 ];
	}
	
	private function getCollections() {
		$id_lang = (int) $this->context->language->id;
		
		$collections = Category::getChildren( self::CATEGORY_COLLECTION_ID, $id_lang );
		
		$query = array();
		if ( !empty( $collections ) ) {
			foreach ( $collections as $collection ) {
				$query[] = array(
					'id_category' => $collection[ 'id_category' ],
					'name'        => $collection[ 'name' ]
				);
			}
		}
		
		return array(
			'query' => $query,
			'id'    => 'id_category',
			'name'  => 'name'
		);
	}
	
	public function renderWidget( $hookName, array $configuration ) {
		if ( !$this->isCached( $this->templateFile, $this->getCacheId( 'planatec' ) ) ) {
			$variables = $this->getWidgetVariables( $hookName, $configuration );
			
			if ( empty( $variables ) ) {
				return false;
			}
			
			$this->smarty->assign( $variables );
		}
		
		return $this->fetch( $this->templateFile, $this->getCacheId( 'planatec' ) );
	}
	
	public function getWidgetVariables( $hookName, array $configuration ) {
		return false;
	}
	
	public function hookDisplayHome() {
		$this->smarty->assign( 'bestSellerProducts', $this->getBestSellersProducts() );
		
		return $this->fetch( $this->bestSellersFile, $this->getCacheId( 'planatec' ) );
	}
	
	protected function getBestSellersProducts() {
		$category = new Category( (int) self::CATEGORY_BEST_SELLER_ID );
		
		$searchProvider = new CategoryProductSearchProvider(
			$this->context->getTranslator(),
			$category
		);
		
		$context = new ProductSearchContext( $this->context );
		
		$query = new ProductSearchQuery();
		$query->setSortOrder( new SortOrder( 'product', 'name', 'asc' ) );
		
		$result = $searchProvider->runQuery(
			$context,
			$query
		);
		
		$assembler = new ProductAssembler( $this->context );
		
		$presenterFactory     = new ProductPresenterFactory( $this->context );
		$presentationSettings = $presenterFactory->getPresentationSettings();
		$presenter            = $presenterFactory->getPresenter();
		
		$products_for_template = [];
		
		foreach ( $result->getProducts() as $rawProduct ) {
			$products_for_template[] = $presenter->present(
				$presentationSettings,
				$assembler->assembleProduct( $rawProduct ),
				$this->context->language
			);
		}
		
		return $products_for_template;
	}
}