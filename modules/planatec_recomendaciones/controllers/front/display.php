<?php

/**
 * @author Julio Colás
 */
class planatec_recomendacionesDisplayModuleFrontController extends ModuleFrontController {
	
	public $template = 'module:planatec_recomendaciones/views/templates/pages/page.tpl';
	
	public function setMedia() {
		$js_path  = $this->module->getPathUri() . '/views/js/';
		$css_path = $this->module->getPathUri() . '/views/css/';
		
		parent::setMedia();
		$this->context->controller->addJS( $js_path . 'front.js' );
		$this->context->controller->addCSS( $css_path . 'front.css' );
	}
	
	public function initContent() {
		parent::initContent();
		
		$idPage = (int) Tools::getValue( 'recomendacion' );
		
		$recomendacionPagina = $this->getRecomendacionPaginaById( $idPage );
		
		$this->context->smarty->assign(
			[
				'image_baseurl'       => $this->module->getPathUri() . 'images/',
				'recomendacionPagina' => $recomendacionPagina
			]
		);
		
		$this->setTemplate( $this->template );
	}
	
	private function getRecomendacionPaginaById( $id ) {
		$this->context = Context::getContext();
		$id_lang       = $this->context->language->id;
		
		$recomendacionPagina = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT rp.`id_planatec_recomendaciones_paginas` as id_recomendacion_pagina,
			rp.`posicion`, rp.`activo`, rpl.`titulo`
			FROM ' . _DB_PREFIX_ . 'planatec_recomendaciones_paginas rp
			LEFT JOIN ' . _DB_PREFIX_ . 'planatec_recomendaciones_paginas_lang rpl ON (rp.id_planatec_recomendaciones_paginas = rpl.id_planatec_recomendaciones_paginas)
			WHERE rpl.id_lang = ' . (int) $id_lang . '
			AND rp.`id_planatec_recomendaciones_paginas` = ' . (int) $id . '
         ORDER BY rp.posicion'
		);
		
		$recomendacionPagina = $recomendacionPagina[ 0 ];
		
		$recomendacionPagina[ 'secciones' ] = $this->getRecomendacionesSeccionesByPagina(
			$recomendacionPagina[ 'id_recomendacion_pagina' ]
		);
		
		return $recomendacionPagina;
	}
	
	private function getRecomendacionesSeccionesByPagina( $idRecomendacionPagina, $active = null ) {
		$this->context = Context::getContext();
		$id_lang       = $this->context->language->id;
		
		$recomendacionesSecciones = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT rs.`id_planatec_recomendaciones_secciones` as id_recomendacion_seccion,
			rs.`posicion`, rs.`activo`, rsl.`titulo`, rsl.`imagen`, rsl.`id_planatec_recomendaciones_paginas`
			FROM ' . _DB_PREFIX_ . 'planatec_recomendaciones_secciones rs
			LEFT JOIN ' . _DB_PREFIX_ . 'planatec_recomendaciones_secciones_lang rsl ON (rs.id_planatec_recomendaciones_secciones = rsl.id_planatec_recomendaciones_secciones)
			WHERE rsl.id_lang = ' . (int) $id_lang . '
			AND rsl.id_planatec_recomendaciones_paginas = ' . (int) $idRecomendacionPagina .
			( $active ? ' AND rs.`activo` = 1' : ' ' ) . '
			ORDER BY rs.posicion'
		);
		
		if ( is_array( $recomendacionesSecciones ) ) {
			foreach ( $recomendacionesSecciones as &$recomendacionSeccion ) {
				$recomendacionSeccion[ 'apartados' ] = $this->getRecomendacionesApartadosBySeccion(
					$recomendacionSeccion[ 'id_recomendacion_seccion' ]
				);
			}
		}
		
		return $recomendacionesSecciones;
	}
	
	private function getRecomendacionesApartadosBySeccion( $idRecomendacionSeccion, $active = null ) {
		$this->context = Context::getContext();
		$id_lang       = $this->context->language->id;
		
		$recomendacionesApartados = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT ra.`id_planatec_recomendaciones_apartados` as id_recomendacion_apartado,
			ra.`posicion`, ra.`activo`, ral.`titulo`, ral.`contenido`, ral.`id_planatec_recomendaciones_secciones`
			FROM ' . _DB_PREFIX_ . 'planatec_recomendaciones_apartados ra
			LEFT JOIN ' . _DB_PREFIX_ . 'planatec_recomendaciones_apartados_lang ral ON (ra.id_planatec_recomendaciones_apartados = ral.id_planatec_recomendaciones_apartados)
			WHERE ral.id_lang = ' . (int) $id_lang . '
			AND ral.id_planatec_recomendaciones_secciones = ' . (int) $idRecomendacionSeccion .
			( $active ? ' AND ra.`activo` = 1' : ' ' ) . '
			ORDER BY ra.posicion'
		);
		
		return $recomendacionesApartados;
	}
}