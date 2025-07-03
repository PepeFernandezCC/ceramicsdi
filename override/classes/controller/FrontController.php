<?php

class FrontController extends FrontControllerCore {

	public const ID_FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA = '74';
	public const FAQ_NUM_QUESTIONS = 20;

	//COUNTRIES
	public const ALEMANIA = 1;
	public const AUSTRIA = 2;
	public const BELGICA = 3;
	public const BULGARIA = 233;
	public const CHEQUIA = 16;
	public const CROACIA = 74;
	public const DINAMARCA = 20;
	public const ESLOVENIA = 191;
	public const ESPANA = 6;
	public const FINLANDIA = 7;
	public const FRANCIA = 8;
	public const HUNGRIA = 142;
	public const ITALIA = 10;
	public const LUXEMBURGO = 12;
	public const PAISES_BAJOS = 13;
	public const PORTUGAL = 15;
	public const SUECIA = 18;

	//FEATURES
	public const FEATURE_TIPO_ESTANCIA_ID      = '1';
	public const FEATURE_FORMATO_ID            = '3';
	public const FEATURE_MEDIDA_ID             = '4';
	public const FEATURE_ACABADO			   = '7';
	public const FEATURE_ESPESOR_ID            = '5';
	public const FEATURE_TIPOLOGIA_PRECIO_ID   = '16';
	public const FEATURE_M2_CAJA_ID            = '17';
	public const FEATURE_M2_PIEZA_ID           = '30';
	public const FEATURE_PIEZAS_CAJA_ID        = '18';
	public const FEATURE_JUNTA_RECOMENDADA_ID  = '19';
	public const FEATURE_DIAS_PLAZO_ENTREGA_ID = '20';
	public const FEATURE_TEXTO_MUESTRA_ID      = '21';
	public const FEATURE_JUNTAS_ID       	   = '24';
	public const FEATURE_HERRAMIENTAS_ID 	   = '25';
	public const FEATURE_PRODUCTOS_ID    	   = '27';
	public const FEATURE_MARCA_ID        	   = '39';
	public const FEATURE_WEB_PRICE       	   = '44';
	public const FEATURE_MATERIAL		 	   = '45';
	public const FEATURE_COLOR		 	   	   = '46';
	public const FEATURE_SHOW_STOCK 	 	   = '55';
	public const FEATURE_SAMPLE_AVAILABLE	   = '56';
	public const FEATURE_PRIORITY			   = '58';
	public const FEATURE_USE_IMAGE			   = '59';
	public const FEATURE_PREPARE_DAYS		   = '60';
	public const FEATURE_DELIVERY_SIZE		   = '61';
	public const FEATURE_CHANNABLE			   = '62';
	public const FEATURE_DESCRIPTION_ES		   = '63';
	public const FEATURE_DESCRIPTION_FR		   = '64';
	public const FEATURE_DESCRIPTION_EN		   = '65';
	public const FEATURE_DESCRIPTION_DE		   = '66';
	public const FEATURE_DESCRIPTION_PT		   = '67';
	public const FEATURE_DESCRIPTION_NL		   = '68';
	public const FEATURE_MUESTRA_DE_PAGO_ID = '43'; // En la demo es el 31
	public const FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2       = 'Por m2';
	public const FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_FR    = 'Par m2';
	public const FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_EN    = 'Per m2';
	public const FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_DE    = 'Pro m2';
	public const FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_PT    = 'Por m2';
	public const FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_NL    = 'Per m2';
	public const ID_FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2    = '73';
	public const FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA    = 'Por pieza';
	public const FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_FR = 'Par pièce';
	public const FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_EN = 'Per piece';
	public const FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_DE = 'Per Stück';
	public const FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_DE_NAN = 'Ja';
	public const FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_PT = 'Por peça';
	public const FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_NL = 'Per stuk';
	
	//ATTRIBUTE
	public const ATTRIBUTE_MUESTRA_ID = '2';
	
	//CATEGORY
	public const CATEGORY_CERAMICA                 = '3';
	public const CATEGORY_INSTALACION_Y_MONTAJE_ID = '5';
	public const CATEGORY_AZULEJOS                 = '11';
	public const CATEGORY_INSTALACION_ID           = '36';
	public const CATEGORY_MANTENIMIENTO_ID         = '67';
	public const CATEGORY_OTROS_MATERIALES_ID      = '80';
	public const CATEGORY_FORMA					   = '101';
	public const CATEGORY_ARTICULATIONS			   = '94';
	
	//SUBCATEGORY
	public const SUBCATEGORY_CERAMICA__MADERA_ID         = '20';
	public const SUBCATEGORY_CERAMICA__HIDRAULICOS_ID    = '19';
	public const SUBCATEGORY_CERAMICA__MONOCOLOR_ID      = '26';
	public const SUBCATEGORY_CERAMICA__PIEDRA_ID         = '4';
	public const SUBCATEGORY_CERAMICA__CEMENTO_ID        = '18';
	public const SUBCATEGORY_CERAMICA__TERRAZO_ID        = '25';
	public const SUBCATEGORY_CERAMICA__METRO_ID          = '30';
	public const SUBCATEGORY_CERAMICA__MARMOL_ID         = '27';
	public const SUBCATEGORY_CERAMICA__MANUAL_ID         = '28';
	public const SUBCATEGORY_CERAMICA__TRADICIONAL_ID    = '29';
	public const SUBCATEGORY_CERAMICA__ESTILO_MODERNO_ID = '31';
	public const SUBCATEGORY_CERAMICA__PORCELANICO_ID    = '33';
	public const SUBCATEGORY_CERAMICA__DECORATIVOS_ID    = '46';
	public const SUBCATEGORY_CERAMICA__BARRO_ID          = '48';
	public const SUBCATEGORY_CERAMICA__INDUSTRIAL_ID     = '71';
	public const SUBCATEGORY_CERAMICA__MOSAICOS_ID       = '32';
	public const SUBCATEGORY_CERAMICA__FRESCO_ID         = '72';
	
	/**
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function assignGeneralPurposeVariables() {

		$id_lang = (int) $this->context->language->getId();
	
		// Incorpora la página del CMS con ID 7 que se utiliza para la primera columna del footer
		$cms = new CMS( 7, $id_lang );
		$this->context->smarty->assign( 'cmsFooterColumn1', $cms->content );
		
		$this->context->smarty->assign( 'socialLinksFooter', $this->getSocialLinks() );
		
		$dirImgFormatos = '/modules/planatec/img/';
		$imgFormatos    = array();
		foreach ( $this->getFormatos() as $formato ) {
			$row = Db::getInstance()->getRow(
				'SELECT * FROM `' . _DB_PREFIX_ . 'planatec_formatos` WHERE `id_formato` = ' . $formato[ 'id_feature_value' ] . ''
			);
			
			if ( $row ) {
				$imgFormatos[ $formato[ 'id_feature_value' ] ] = $dirImgFormatos . DIRECTORY_SEPARATOR . $row[ 'url' ];
			}
		}

		
		$this->context->smarty->assign( 'FEATURE_TIPO_ESTANCIA_ID', self::FEATURE_TIPO_ESTANCIA_ID );
		$this->context->smarty->assign( 'FEATURE_FORMATO_ID', self::FEATURE_FORMATO_ID );
		$this->context->smarty->assign( 'imgFormatos', $imgFormatos );
		$this->context->smarty->assign( 'FEATURE_MEDIDA_ID', self::FEATURE_MEDIDA_ID );
		$this->context->smarty->assign( 'FEATURE_ESPESOR_ID', self::FEATURE_ESPESOR_ID );
		$this->context->smarty->assign( 'FEATURE_ACABADO', self::FEATURE_ACABADO );
		$this->context->smarty->assign( 'FEATURE_DIAS_PLAZO_ENTREGA_ID', self::FEATURE_DIAS_PLAZO_ENTREGA_ID );
		$this->context->smarty->assign( 'FEATURE_TEXTO_MUESTRA_ID', self::FEATURE_TEXTO_MUESTRA_ID );
		$this->context->smarty->assign( 'FEATURE_JUNTAS_ID', self::FEATURE_JUNTAS_ID );
		$this->context->smarty->assign( 'FEATURE_HERRAMIENTAS_ID', self::FEATURE_HERRAMIENTAS_ID );
		$this->context->smarty->assign( 'FEATURE_PRODUCTOS_ID', self::FEATURE_PRODUCTOS_ID );
		$this->context->smarty->assign( 'FEATURE_MARCA_ID', self::FEATURE_MARCA_ID );
		$this->context->smarty->assign( 'FEATURE_WEB_PRICE', self::FEATURE_WEB_PRICE );
		$this->context->smarty->assign( 'FEATURE_MATERIAL', self::FEATURE_MATERIAL );
		$this->context->smarty->assign( 'FEATURE_COLOR', self::FEATURE_COLOR );
		$this->context->smarty->assign( 'FEATURE_MUESTRA_DE_PAGO_ID', self::FEATURE_MUESTRA_DE_PAGO_ID );
		$this->context->smarty->assign( 'FEATURE_TIPOLOGIA_PRECIO_ID', self::FEATURE_TIPOLOGIA_PRECIO_ID );
		$this->context->smarty->assign( 'FEATURE_M2_CAJA_ID', self::FEATURE_M2_CAJA_ID );
		$this->context->smarty->assign( 'FEATURE_M2_PIEZA_ID', self::FEATURE_M2_PIEZA_ID );
		$this->context->smarty->assign( 'FEATURE_PIEZAS_CAJA_ID', self::FEATURE_PIEZAS_CAJA_ID );
		$this->context->smarty->assign( 'FEATURE_JUNTA_RECOMENDADA_ID', self::FEATURE_JUNTA_RECOMENDADA_ID );
		$this->context->smarty->assign( 'CATEGORY_CERAMICA_ID', self::CATEGORY_CERAMICA );
		$this->context->smarty->assign( 'CATEGORY_INSTALACION_Y_MONTAJE_ID', self::CATEGORY_INSTALACION_Y_MONTAJE_ID );
		$this->context->smarty->assign( 'CATEGORY_AZULEJOS', self::CATEGORY_AZULEJOS );
		$this->context->smarty->assign( 'CATEGORY_FORMA', self::CATEGORY_FORMA );
		$this->context->smarty->assign( 'CATEGORY_ARTICULATIONS', self::CATEGORY_ARTICULATIONS );
		$this->context->smarty->assign( 'CATEGORY_INSTALACION_ID', self::CATEGORY_INSTALACION_ID );
		$this->context->smarty->assign( 'CATEGORY_MANTENIMIENTO_ID', self::CATEGORY_MANTENIMIENTO_ID );
		$this->context->smarty->assign( 'CATEGORY_OTROS_MATERIALES_ID', self::CATEGORY_OTROS_MATERIALES_ID );
		
		$this->context->smarty->assign('FEATURE_SHOW_STOCK', self::FEATURE_SHOW_STOCK);
		$this->context->smarty->assign('FEATURE_SAMPLE_AVAILABLE', self::FEATURE_SAMPLE_AVAILABLE);
		$this->context->smarty->assign('FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2', self::FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2);
		$this->context->smarty->assign('FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_FR',self::FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_FR);
		$this->context->smarty->assign('FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_EN',self::FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_EN);
		$this->context->smarty->assign('FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_DE',self::FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_DE);
		$this->context->smarty->assign('FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_PT',self::FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_PT);
		$this->context->smarty->assign('FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_NL',self::FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2_NL);

		$this->context->smarty->assign('FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA',self::FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA);
		$this->context->smarty->assign('FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_FR',self::FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_FR);
		$this->context->smarty->assign('FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_EN',self::FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_EN);
		$this->context->smarty->assign('FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_DE',self::FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_DE);
		$this->context->smarty->assign('FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_DE_NAN',self::FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_DE_NAN);
		$this->context->smarty->assign('FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_PT',self::FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_PT);
		$this->context->smarty->assign('FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_NL',self::FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA_NL);

		$this->context->smarty->assign('ID_FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2',self::ID_FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2);
		$this->context->smarty->assign('ID_FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA',self::ID_FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA);
		
		$this->context->smarty->assign('ATTRIBUTE_MUESTRA_ID',self::ATTRIBUTE_MUESTRA_ID);
		$this->context->smarty->assign( 'SUBCATEGORY_CERAMICA__MADERA_ID', self::SUBCATEGORY_CERAMICA__MADERA_ID );
		$this->context->smarty->assign('SUBCATEGORY_CERAMICA__HIDRAULICOS_ID',self::SUBCATEGORY_CERAMICA__HIDRAULICOS_ID);
		$this->context->smarty->assign( 'SUBCATEGORY_CERAMICA__MONOCOLOR_ID', self::SUBCATEGORY_CERAMICA__MONOCOLOR_ID );
		$this->context->smarty->assign( 'SUBCATEGORY_CERAMICA__PIEDRA_ID', self::SUBCATEGORY_CERAMICA__PIEDRA_ID );
		$this->context->smarty->assign( 'SUBCATEGORY_CERAMICA__CEMENTO_ID', self::SUBCATEGORY_CERAMICA__CEMENTO_ID );
		$this->context->smarty->assign( 'SUBCATEGORY_CERAMICA__TERRAZO_ID', self::SUBCATEGORY_CERAMICA__TERRAZO_ID );
		$this->context->smarty->assign( 'SUBCATEGORY_CERAMICA__METRO_ID', self::SUBCATEGORY_CERAMICA__METRO_ID );
		$this->context->smarty->assign( 'SUBCATEGORY_CERAMICA__MARMOL_ID', self::SUBCATEGORY_CERAMICA__MARMOL_ID );
		$this->context->smarty->assign( 'SUBCATEGORY_CERAMICA__MANUAL_ID', self::SUBCATEGORY_CERAMICA__MANUAL_ID );
		$this->context->smarty->assign('SUBCATEGORY_CERAMICA__TRADICIONAL_ID',self::SUBCATEGORY_CERAMICA__TRADICIONAL_ID);
		$this->context->smarty->assign('SUBCATEGORY_CERAMICA__ESTILO_MODERNO_ID',self::SUBCATEGORY_CERAMICA__ESTILO_MODERNO_ID);
		$this->context->smarty->assign('SUBCATEGORY_CERAMICA__PORCELANICO_ID',self::SUBCATEGORY_CERAMICA__PORCELANICO_ID);
		$this->context->smarty->assign('SUBCATEGORY_CERAMICA__DECORATIVOS_ID',self::SUBCATEGORY_CERAMICA__DECORATIVOS_ID);
		$this->context->smarty->assign( 'SUBCATEGORY_CERAMICA__BARRO_ID', self::SUBCATEGORY_CERAMICA__BARRO_ID );
		$this->context->smarty->assign('SUBCATEGORY_CERAMICA__INDUSTRIAL_ID',self::SUBCATEGORY_CERAMICA__INDUSTRIAL_ID);
		$this->context->smarty->assign( 'SUBCATEGORY_CERAMICA__MOSAICOS_ID', self::SUBCATEGORY_CERAMICA__MOSAICOS_ID );
		$this->context->smarty->assign( 'SUBCATEGORY_CERAMICA__FRESCO_ID', self::SUBCATEGORY_CERAMICA__FRESCO_ID );
		

		$this->context->smarty->assign(
			'DONT_SHOW_THIS_FEATURES', 
			[
				self::FEATURE_M2_CAJA_ID,
				self::FEATURE_TEXTO_MUESTRA_ID,
				//self::FEATURE_PIEZAS_CAJA_ID,
				self::FEATURE_TIPOLOGIA_PRECIO_ID,
				self::FEATURE_JUNTA_RECOMENDADA_ID,
				self::FEATURE_DIAS_PLAZO_ENTREGA_ID,
				self::FEATURE_JUNTAS_ID,
				self::FEATURE_HERRAMIENTAS_ID,
				self::FEATURE_PRODUCTOS_ID,
				self::FEATURE_MARCA_ID,
				self::FEATURE_WEB_PRICE,
				self::FEATURE_MATERIAL,
				self::FEATURE_SHOW_STOCK,
				self::FEATURE_SAMPLE_AVAILABLE,
				self::FEATURE_PRIORITY,
				self::FEATURE_PREPARE_DAYS,
				self::FEATURE_DELIVERY_SIZE,
				self::FEATURE_CHANNABLE,
				self::FEATURE_DESCRIPTION_ES,
				self::FEATURE_DESCRIPTION_FR,
				self::FEATURE_DESCRIPTION_EN,
				self::FEATURE_DESCRIPTION_DE,
				self::FEATURE_DESCRIPTION_PT,
				self::FEATURE_DESCRIPTION_NL
			]
		);
		$this->context->smarty->assign(
			'VALID_COUNTRIES', 
			[
				self::ALEMANIA,
				self::AUSTRIA,
				self::BELGICA,
				self::BULGARIA,
				self::CHEQUIA,
				self::CROACIA,
				self::DINAMARCA,
				self::ESLOVENIA,
				self::ESPANA,
				self::FINLANDIA,
				self::FRANCIA,
				self::HUNGRIA,
				self::ITALIA,
				self::LUXEMBURGO,
				self::PAISES_BAJOS,
				self::PORTUGAL,
				self::SUECIA
			]
		);

		$this->context->smarty->assign(
			'FILTER_ESTILO',
			[
				self::SUBCATEGORY_CERAMICA__MANUAL_ID,
				self::SUBCATEGORY_CERAMICA__TRADICIONAL_ID,
				self::SUBCATEGORY_CERAMICA__ESTILO_MODERNO_ID,
				self::SUBCATEGORY_CERAMICA__DECORATIVOS_ID,
				self::SUBCATEGORY_CERAMICA__INDUSTRIAL_ID,
				self::SUBCATEGORY_CERAMICA__FRESCO_ID
			]
		);
		
		$this->context->smarty->assign(
			'FILTER_ASPECTO',
			[
				self::SUBCATEGORY_CERAMICA__MADERA_ID,
				self::SUBCATEGORY_CERAMICA__CEMENTO_ID,
				self::SUBCATEGORY_CERAMICA__MONOCOLOR_ID,
				self::SUBCATEGORY_CERAMICA__HIDRAULICOS_ID,
				self::SUBCATEGORY_CERAMICA__PIEDRA_ID,
				self::SUBCATEGORY_CERAMICA__MARMOL_ID,
				self::SUBCATEGORY_CERAMICA__TERRAZO_ID,
				self::SUBCATEGORY_CERAMICA__METRO_ID,
				self::SUBCATEGORY_CERAMICA__BARRO_ID
			]
		);
		
		// Obtiene e incorpora la colección destacada
		$featuredcollection = new Category( Configuration::get( 'featured-collection' ), $id_lang );
		$this->context->smarty->assign( 'featuredCollection', $featuredcollection );
		
		// Obtiene e incorpora la información de transporte
		$this->context->smarty->assign( 'productTransport', Configuration::get( 'product-transport', $id_lang ) );
		$this->context->smarty->assign('productTransportSamples',Configuration::get( 'product-transport-samples', $id_lang ));
		
		// Obtiene e incorpora la información de uso y mantenimiento
		$this->context->smarty->assign('productUsoMantenimiento',Configuration::get( 'product-uso-mantenimiento', $id_lang ));
		
		// Obtiene e incorpora la información de envíos y devoluciones
		$this->context->smarty->assign(
			'productEnviosDevoluciones',Configuration::get( 'product-envios-devoluciones', $id_lang ));
		
		// Obtiene los 4 productos sugeridos en el carrito
		$this->context->smarty->assign( 'suggestedProductsInCart', $this->getSuggestedProductsInCart( $id_lang ) );
		
		// Obtiene la FAQ
		$this->context->smarty->assign( 'faq', $this->getFAQ() );
		
		// Obtiene la sección para la Home
		$dirImgSectionHome = '/modules/planatec/img/';
		
		$row = Db::getInstance()->getRow(
			'
			SELECT * FROM `' . _DB_PREFIX_ . 'planatec_home_section`
			'
		);
		
		$rowLang = Db::getInstance()->getRow(
			'
			SELECT * FROM `' . _DB_PREFIX_ . 'planatec_home_section_lang`
			WHERE `id_planatec_home_section` = ' . $row[ 'id_planatec_home_section' ] . '
				AND `id_lang` = ' . $this->context->language->id . '
			'
		);
		
		$imgSectionHome       = '';
		$contentSectionHome   = '';
		$buttonUrlSectionHome = '';
		if ( $row && $rowLang ) {
			$imgSectionHome       = $dirImgSectionHome . DIRECTORY_SEPARATOR . $row[ 'image_url' ];
			$contentSectionHome   = $rowLang[ 'content' ];
			$buttonUrlSectionHome = $rowLang[ 'button_url' ];
		}
		$this->context->smarty->assign( 'imgSectionHome', $imgSectionHome );
		$this->context->smarty->assign( 'contentSectionHome', $contentSectionHome );
		$this->context->smarty->assign( 'buttonUrlSectionHome', $buttonUrlSectionHome );
		
		// Obtiene el contenido extra de la categoría actual
		if ( property_exists( Context::getContext(), 'controller' )
			&& property_exists( Context::getContext()->controller, 'category' )
			&& !is_null( Context::getContext()->controller->category )
			&& !is_null( Context::getContext()->controller->category->id_category )
		) {
			$res = Db::getInstance()->getRow(
				'
			SELECT id_planatec_categorias_contenido_extra FROM `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra`
			WHERE `id_categoria` = ' . Context::getContext()->controller->category->id_category . '
			'
			);
			
			if($res) {

				$res = Db::getInstance()->getRow(
					'
					SELECT content, button_text FROM `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra_lang`
					WHERE `id_planatec_categorias_contenido_extra` = ' . $res[ 'id_planatec_categorias_contenido_extra' ] . '
					AND `id_lang` = ' . $this->context->language->id . '
					'
				);
				
			}

			if ( $res && Db::getInstance()->numRows() > 0 ) {
				$this->context->smarty->assign( 'categoria_contenido_extra', $res[ 'content' ] );
				$this->context->smarty->assign( 'categoria_texto_boton', $res[ 'button_text' ] );
			} else {
				$this->context->smarty->assign( 'categoria_contenido_extra', null );
				$this->context->smarty->assign( 'categoria_texto_boton', null );
			}
		}

		$stepsTitle = array();
		if ( Context::getContext()->controller->page_name === 'checkout' ) {
			foreach ( Context::getContext()->controller->getCheckoutProcess()->getSteps() as $step ) {
				$stepsTitle[] = $step->getTitle();
			}
			$this->context->smarty->assign( 'stepsTitle', $stepsTitle );
		}
	
		return parent::assignGeneralPurposeVariables();
	}
	
	private function getSuggestedProductsInCart( $id_lang ) {
		$products = Product::getProducts( $id_lang, 0, 4, 'id_product', 'ASC', self::CATEGORY_INSTALACION_Y_MONTAJE_ID );
		
		$assembler = new ProductAssembler( $this->context );
		
		$presenterFactory     = new ProductPresenterFactory( $this->context );
		$presentationSettings = $presenterFactory->getPresentationSettings();
		$presenter            = new \PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter(
			new \PrestaShop\PrestaShop\Adapter\Image\ImageRetriever(
				$this->context->link
			),
			$this->context->link,
			new \PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
			new \PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
			$this->context->getTranslator()
		);
		
		$products_for_template = [];
		
		foreach ( $products as $rawProduct ) {
			$products_for_template[] = $presenter->present(
				$presentationSettings,
				$assembler->assembleProduct( $rawProduct ),
				$this->context->language
			);
		}
		
		return $products_for_template;
	}
	
	private function getFAQ() {
		$id_lang = (int) $this->context->language->id;
		
		$questions = array();
		for ( $i = 1; $i <= self::FAQ_NUM_QUESTIONS; $i++ ) {
			if ( $question = Configuration::get( 'faq-question-title-' . $i, $id_lang ) ) {
				$questions[ $i ] = array(
					'title'  => $question,
					'answer' => Configuration::get( 'faq-question-answer-' . $i, $id_lang )
				);
			}
		}
		
		return $questions;
	}
	
	private function getSocialLinks() {
		$social_links = array();
		$id_lang      = (int) $this->context->language->id;
		
		if ( $sf_facebook = Configuration::get( 'BLOCKSOCIAL_FACEBOOK', $id_lang ) ) {
			$social_links[ 'facebook' ] = array(
				'label' => $this->trans( 'Facebook', array(), 'Modules.Socialfollow.Shop' ),
				'class' => 'facebook',
				'url'   => $sf_facebook,
			);
		}
		
		if ( $sf_twitter = Configuration::get( 'BLOCKSOCIAL_TWITTER', $id_lang ) ) {
			$social_links[ 'twitter' ] = array(
				'label' => $this->trans( 'Twitter', array(), 'Modules.Socialfollow.Shop' ),
				'class' => 'twitter',
				'url'   => $sf_twitter,
			);
		}
		
		if ( $sf_rss = Configuration::get( 'BLOCKSOCIAL_RSS', $id_lang ) ) {
			$social_links[ 'rss' ] = array(
				'label' => $this->trans( 'Rss', array(), 'Modules.Socialfollow.Shop' ),
				'class' => 'rss',
				'url'   => $sf_rss,
			);
		}
		
		if ( $sf_youtube = Configuration::get( 'BLOCKSOCIAL_YOUTUBE', $id_lang ) ) {
			$social_links[ 'youtube' ] = array(
				'label' => $this->trans( 'YouTube', array(), 'Modules.Socialfollow.Shop' ),
				'class' => 'youtube',
				'url'   => $sf_youtube,
			);
		}
		
		if ( $sf_instagram = Configuration::get( 'BLOCKSOCIAL_INSTAGRAM', $id_lang ) ) {
			$social_links[ 'instagram' ] = array(
				'label' => $this->trans( 'Instagram', array(), 'Modules.Socialfollow.Shop' ),
				'class' => 'instagram',
				'url'   => $sf_instagram,
			);
		}
		
		if ( $sf_pinterest = Configuration::get( 'BLOCKSOCIAL_PINTEREST', $id_lang ) ) {
			$social_links[ 'pinterest' ] = array(
				'label' => $this->trans( 'Pinterest', array(), 'Modules.Socialfollow.Shop' ),
				'class' => 'pinterest',
				'url'   => $sf_pinterest,
			);
		}
		
		if ( $sf_vimeo = Configuration::get( 'BLOCKSOCIAL_VIMEO', $id_lang ) ) {
			$social_links[ 'vimeo' ] = array(
				'label' => $this->trans( 'Vimeo', array(), 'Modules.Socialfollow.Shop' ),
				'class' => 'vimeo',
				'url'   => $sf_vimeo,
			);
		}
		
		if ( $sf_linkedin = Configuration::get( 'BLOCKSOCIAL_LINKEDIN', $id_lang ) ) {
			$social_links[ 'linkedin' ] = array(
				'label' => $this->trans( 'LinkedIn', array(), 'Modules.Socialfollow.Shop' ),
				'class' => 'linkedin',
				'url'   => $sf_linkedin,
			);
		}
		
		if ( $sf_tiktok = Configuration::get( 'BLOCKSOCIAL_TIKTOK', $id_lang ) ) {
			$social_links[ 'tiktok' ] = [
				'label' => $this->trans( 'TikTok', [], 'Modules.Socialfollow.Shop' ),
				'class' => 'tiktok',
				'url'   => $sf_tiktok,
			];
		}
		
		if ( $sf_discord = Configuration::get( 'BLOCKSOCIAL_DISCORD', $id_lang ) ) {
			$social_links[ 'discord' ] = [
				'label' => $this->trans( 'Discord', [], 'Modules.Socialfollow.Shop' ),
				'class' => 'ps-socialfollow-discord',
				'url'   => $sf_discord,
			];
		}
		
		return $social_links;
	}
	
	private function getFormatos() {
		$id_lang = (int) $this->context->language->id;
		
		$features = Feature::getFeatures( $id_lang );
		$formatos = array();
		foreach ($features as &$feature) {
			if ($feature['id_feature'] === self::FEATURE_FORMATO_ID || $feature['id_feature'] === self::FEATURE_COLOR) {
				$featureValues = FeatureValue::getFeatureValuesWithLang(
					(int) $this->context->language->id,
					$feature['id_feature'],
					true
				);
				
				// Fusionar los valores en lugar de agregarlos como nuevos elementos
				$formatos = array_merge($formatos, $featureValues);
			}
		}
		
		return $formatos;
	}

	public function getLastParentCategoryInfo( $id ) {
		$category                      = new Category( $id, Context::getContext()->language->id );
		$lastParentCategoryId          = $category->id;
		$lastParentCategoryLinkRewrite = $category->link_rewrite;
		
		while ( $category->isRootCategory() === false ) {
			$category = new Category( $category->id_parent, Context::getContext()->language->id );
			
			if ( !$category->isRootCategory() && $category->id !== 2 ) {
				$lastParentCategoryId          = $category->id;
				$lastParentCategoryLinkRewrite = $category->link_rewrite;
			}
		}
		
		return array(
			'id'           => $lastParentCategoryId,
			'link_rewrite' => $lastParentCategoryLinkRewrite
		);
	}

	    /**
     * Sets controller CSS and JS files.
     *
     * @return bool
     */
    public function setMedia()
    {
        $this->registerStylesheet('theme-main', '/assets/css/theme.css', ['media' => 'all', 'priority' => 50]);
        $this->registerStylesheet('theme-custom', '/assets/css/custom.css', ['media' => 'all', 'priority' => 1000]);
        $this->registerStylesheet('theme-jost', '/assets/css/jost.css', ['media' => 'all', 'priority' => 1000]);

		//FONT AWESOME

		//$this->registerStylesheet('theme-fa', '/assets/css/fontawesome/fontawesome.min.css', ['media' => 'all', 'priority' => 1000]);

        if ($this->context->language->is_rtl) {
            $this->registerStylesheet('theme-rtl', '/assets/css/rtl.css', ['media' => 'all', 'priority' => 900]);
        }

        $this->registerJavascript('corejs', '/themes/core.js', ['position' => 'bottom', 'priority' => 0]);
        $this->registerJavascript('theme-main', '/assets/js/theme.js', ['position' => 'bottom', 'priority' => 50]);
        $this->registerJavascript('theme-custom', '/assets/js/custom.js', ['position' => 'bottom', 'priority' => 1000]);
		

        $assets = $this->context->shop->theme->getPageSpecificAssets($this->php_self);
        if (!empty($assets)) {
            foreach ($assets['css'] as $css) {
                $this->registerStylesheet($css['id'], $css['path'], $css);
            }
            foreach ($assets['js'] as $js) {
                $this->registerJavascript($js['id'], $js['path'], $js);
            }
        }

        // Execute Hook FrontController SetMedia
        Hook::exec('actionFrontControllerSetMedia', []);

        return true;
    }
}