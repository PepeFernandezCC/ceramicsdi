<?php
/**
 * 2007-2023 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2023 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

if ( !defined( '_PS_VERSION_' ) ) {
	exit;
}

include_once __DIR__ . '/Ps_RecomendacionesPagina.php';
include_once __DIR__ . '/Ps_RecomendacionesSeccion.php';
include_once __DIR__ . '/Ps_RecomendacionesApartado.php';

class Planatec_recomendaciones extends Module implements WidgetInterface {
	
	const SUBMIT_RECOMENDACION_PAGINA               = 'submitRecomendacionPagina';
	const SUBMIT_RECOMENDACION_PAGINA_CHANGE_STATUS = 'recomendacionPaginaChangeStatus';
	const ID_RECOMENDACION_PAGINA                   = 'idRecomendacionPagina';
	const ADD_RECOMENDACION_PAGINA                  = 'addRecomendacionPagina';
	const DELETE_RECOMENDACION_PAGINA               = 'deleteRecomendacionPagina';
	
	const SUBMIT_RECOMENDACION_SECCION                = 'submitRecomendacionSeccion';
	const SUBMIT_SECCION_CHANGE_STATUS                = 'recomendacionSeccionChangeStatus';
	const ID_RECOMENDACION_SECCION                    = 'idRecomendacionSeccion';
	const ADD_RECOMENDACION_SECCION                   = 'addRecomendacionSeccion';
	const DELETE_RECOMENDACION_SECCION                = 'deleteRecomendacionSeccion';
	const ID_RECOMENDACION_SECCION_PAGINA_RELACIONADA = 'idPaginaRelacionada';
	
	const SUBMIT_RECOMENDACION_APARTADO                 = 'submitRecomendacionApartado';
	const SUBMIT_APARTADO_CHANGE_STATUS                 = 'recomendacionApartadoChangeStatus';
	const ID_RECOMENDACION_APARTADO                     = 'idRecomendacionApartado';
	const ADD_RECOMENDACION_APARTADO                    = 'addRecomendacionApartado';
	const DELETE_RECOMENDACION_APARTADO                 = 'deleteRecomendacionApartado';
	const ID_RECOMENDACION_APARTADO_SECCION_RELACIONADA = 'idSeccionRelacionada';
	
	protected $_html = '';
	protected $templateFile;
	
	public function __construct() {
		$this->name          = 'planatec_recomendaciones';
		$this->tab           = 'front_office_features';
		$this->version       = '1.0.0';
		$this->author        = 'Planatec';
		$this->need_instance = 1;
		
		/**
		 * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
		 */
		$this->bootstrap = true;
		
		parent::__construct();
		
		$this->displayName = $this->l( 'Planatec - Recomendaciones' );
		$this->description = $this->l( 'Módulo que gestiona las páginas de Recomendaciones' );
		
		$this->confirmUninstall = $this->l( '¿Estás seguro de que deseas desinstalar el módulo?' );
		
		$this->ps_versions_compliancy = array( 'min' => '1.7', 'max' => _PS_VERSION_ );
	}
	
	public function install() {
		if ( parent::install()
			&& $this->registerHook( 'displayHeader' )
			&& $this->registerHook( 'ModuleRoutes' )
			&& Configuration::updateValue( 'PLANATEC_RECOMENDACIONES_NAME', 'Planatec Recomendaciones' )
		) {
			$res = $this->createTables();
			
			return (bool) $res;
		}
		
		return false;
	}
	
	public function uninstall() {
		if ( parent::uninstall()
			&& Configuration::deleteByName( 'PLANATEC_RECOMENDACIONES_NAME' )
		) {
			$res = $this->deleteTables();
			
			return (bool) $res;
		}
		
		return false;
	}
	
	public function enable( $force_all = false ) {
		return parent::enable( $force_all )/* && $this->installTab()*/ ;
	}
	
	public function disable( $force_all = false ) {
		return parent::disable( $force_all )/* && $this->uninstallTab()*/ ;
	}
	
	/*private function installTab() {
		$tabId = (int) Tab::getIdFromClassName( 'PlanatecRecomendaciones' );
		if ( !$tabId ) {
			$tabId = null;
		}
		
		$tab             = new Tab( $tabId );
		$tab->active     = 1;
		$tab->class_name = 'AdminModules&configure=planatec_recomendaciones';
		$tab->name       = [];
		foreach ( Language::getLanguages() as $lang ) {
			$tab->name[ $lang[ 'id_lang' ] ] = $this->trans(
				'Recomendaciones',
				array(),
				'Modules.PlanatecRecomendaciones.Admin',
				$lang[ 'locale' ]
			);
		}
		$tab->id_parent = (int) Tab::getIdFromClassName( 'ShopParameters' );
		$tab->module    = $this->name;
		
		return $tab->save();
	}
	
	private function uninstallTab() {
		$tabId = (int) Tab::getIdFromClassName( 'PlanatecRecomendaciones' );
		if ( !$tabId ) {
			return true;
		}
		
		$tab = new Tab( $tabId );
		
		return $tab->delete();
	}*/
	
	protected function createTables() {
		// Configuración de las páginas
		$res = (bool) Db::getInstance()->execute(
			'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'planatec_recomendaciones_paginas` (
			`id_planatec_recomendaciones_paginas` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`posicion` int(10) unsigned NOT NULL DEFAULT \'0\',
			`activo` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			PRIMARY KEY (`id_planatec_recomendaciones_paginas`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;'
		);
		
		// Configuración de las páginas por idioma
		$res &= (bool) Db::getInstance()->execute(
			'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'planatec_recomendaciones_paginas_lang` (
			`id_planatec_recomendaciones_paginas` int(10) unsigned NOT NULL,
			`id_lang` int(10) unsigned NOT NULL,
			`titulo` varchar(255) NOT NULL,
			PRIMARY KEY (`id_planatec_recomendaciones_paginas`, `id_lang`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;'
		);
		
		// Configuración de las secciones
		$res &= (bool) Db::getInstance()->execute(
			'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'planatec_recomendaciones_secciones` (
			`id_planatec_recomendaciones_secciones` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`posicion` int(10) unsigned NOT NULL DEFAULT \'0\',
			`activo` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			PRIMARY KEY (`id_planatec_recomendaciones_secciones`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;'
		);
		
		
		// Configuración de las secciones por idioma
		$res &= (bool) Db::getInstance()->execute(
			'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'planatec_recomendaciones_secciones_lang` (
			`id_planatec_recomendaciones_secciones` int(10) unsigned NOT NULL,
			`id_planatec_recomendaciones_paginas` int(10) unsigned NOT NULL,
			`id_lang` int(10) unsigned NOT NULL,
			`titulo` varchar(255) NOT NULL,
			`imagen` varchar(255) NOT NULL,
			PRIMARY KEY (`id_planatec_recomendaciones_secciones`, `id_planatec_recomendaciones_paginas`, `id_lang`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;'
		);
		
		// Configuración de los apartados
		$res &= (bool) Db::getInstance()->execute(
			'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'planatec_recomendaciones_apartados` (
			`id_planatec_recomendaciones_apartados` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`posicion` int(10) unsigned NOT NULL DEFAULT \'0\',
			`activo` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			PRIMARY KEY (`id_planatec_recomendaciones_apartados`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;'
		);
		
		
		// Configuración de los apartados por idioma
		$res &= (bool) Db::getInstance()->execute(
			'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'planatec_recomendaciones_apartados_lang` (
			`id_planatec_recomendaciones_apartados` int(10) unsigned NOT NULL,
			`id_planatec_recomendaciones_secciones` int(10) unsigned NOT NULL,
			`id_lang` int(10) unsigned NOT NULL,
			`titulo` varchar(255) NOT NULL,
			`contenido` text NOT NULL,
			PRIMARY KEY (`id_planatec_recomendaciones_apartados`, `id_planatec_recomendaciones_secciones`, `id_lang`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;'
		);
		
		return $res;
	}
	
	protected function deleteTables() {
		$recomendacionesPaginas = $this->getRecomendacionesPaginas();
		foreach ( $recomendacionesPaginas as $recomendacionPagina ) {
			$to_del = new Ps_RecomendacionesPagina( $recomendacionPagina[ 'id_recomendacion_pagina' ] );
			$to_del->delete();
		}
		
		$recomendacionesSecciones = $this->getRecomendacionesSecciones();
		foreach ( $recomendacionesSecciones as $recomendacionSeccion ) {
			$to_del = new Ps_RecomendacionesSeccion( $recomendacionSeccion[ 'id_recomendacion_seccion' ] );
			$to_del->delete();
		}
		
		$recomendacionesApartados = $this->getRecomendacionesApartados();
		foreach ( $recomendacionesApartados as $recomendacionApartado ) {
			$to_del = new Ps_RecomendacionesApartado( $recomendacionApartado[ 'id_recomendacion_apartado' ] );
			$to_del->delete();
		}
		
		return Db::getInstance()->execute(
			'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'planatec_recomendaciones_paginas`, `' .
			_DB_PREFIX_ . 'planatec_recomendaciones_paginas_lang`, `' .
			_DB_PREFIX_ . 'planatec_recomendaciones_secciones`, `' .
			_DB_PREFIX_ . 'planatec_recomendaciones_secciones_lang`, `' .
			_DB_PREFIX_ . 'planatec_recomendaciones_apartados`, `' .
			_DB_PREFIX_ . 'planatec_recomendaciones_apartados_lang`;'
		);
	}
	
	/*public function hookModuleRoutes( $params ) {
		return [
			'module-planatec_recomendaciones-display' => [
				'controller' => 'display',
				'rule'       => 'recomendaciones/{recomendacion}',
				'keywords'   => [
					//'controller' => [ 'regexp' => '[\w]+', 'param' => 'controller' ],
					'recomendacion'     => [ 'regexp' => '[0-9]+', 'param' => 'recomendacion' ]
				],
				'params'     => [
					'fc'         => 'module',
					'module'     => $this->name,
					'controller' => 'display'
				]
			]
		];
	}*/
	
	public function getContent() {
		//$this->_html .= $this->headerHTML();
		
		if ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_PAGINA )
			|| Tools::isSubmit( self::DELETE_RECOMENDACION_PAGINA )
			|| Tools::isSubmit( self::SUBMIT_RECOMENDACION_PAGINA_CHANGE_STATUS ) ) {
			if ( $this->_postValidation() ) {
				$this->_postProcess();
				$this->_html .= $this->renderPaginasList();
			} else {
				$this->_html .= $this->renderPaginasAddForm();
			}
			
			$this->clearCache();
		} elseif ( Tools::isSubmit( self::ADD_RECOMENDACION_PAGINA )
			|| ( Tools::isSubmit( self::ID_RECOMENDACION_PAGINA )
				&& $this->recomendacionPaginaExists( (int) Tools::getValue( self::ID_RECOMENDACION_PAGINA ) ) )
		) {
			$this->_html .= $this->renderPaginasAddForm();
			if ( !Tools::isSubmit( self::ADD_RECOMENDACION_PAGINA ) ) {
				$this->_html .= $this->renderSeccionesList( Tools::getValue( self::ID_RECOMENDACION_PAGINA ) );
			}
		} elseif ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_SECCION )
			|| Tools::isSubmit( self::DELETE_RECOMENDACION_SECCION )
			|| Tools::isSubmit( self::SUBMIT_SECCION_CHANGE_STATUS ) ) {
			if ( $this->_postValidation() ) {
				$this->_postProcess();
				$this->_html .= $this->renderSeccionesList(
					Tools::getValue( self::ID_RECOMENDACION_SECCION_PAGINA_RELACIONADA )
				);
			} else {
				$recomendacionPagina = $this->getRecomendacionPaginaById(
					Tools::getValue( self::ID_RECOMENDACION_SECCION_PAGINA_RELACIONADA )
				);
				
				$this->_html .= '<h2><strong>' . $recomendacionPagina[ 0 ][ 'titulo' ] . '</strong></h2>';
				$this->_html .= $this->renderSeccionesAddForm(
					Tools::getValue( self::ID_RECOMENDACION_SECCION_PAGINA_RELACIONADA )
				);
			}
			
			$this->clearCache();
		} elseif ( Tools::isSubmit( self::ADD_RECOMENDACION_SECCION )
			|| ( Tools::isSubmit( self::ID_RECOMENDACION_SECCION )
				&& $this->recomendacionSeccionExists( (int) Tools::getValue( self::ID_RECOMENDACION_SECCION ) ) )
		) {
			if ( Tools::getValue( self::ID_RECOMENDACION_SECCION_PAGINA_RELACIONADA ) ) {
				$recomendacionPagina = $this->getRecomendacionPaginaById(
					Tools::getValue( self::ID_RECOMENDACION_SECCION_PAGINA_RELACIONADA )
				);
			} else {
				$recomendacionSeccion = $this->getRecomendacionSeccionById(
					Tools::getValue( self::ID_RECOMENDACION_SECCION )
				);
				$recomendacionPagina  = $this->getRecomendacionPaginaById(
					$recomendacionSeccion[ 0 ][ 'id_planatec_recomendaciones_paginas' ]
				);
			}
			
			$this->_html .= '<h2><strong>' . $recomendacionPagina[ 0 ][ 'titulo' ] . '</strong></h2>';
			
			$this->_html .= $this->renderSeccionesAddForm(
				Tools::getValue( self::ID_RECOMENDACION_SECCION_PAGINA_RELACIONADA )
			);
			if ( !Tools::isSubmit( self::ADD_RECOMENDACION_SECCION ) ) {
				$this->_html .= $this->renderApartadosList( Tools::getValue( self::ID_RECOMENDACION_SECCION ) );
			}
		} elseif ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_APARTADO )
			|| Tools::isSubmit( self::DELETE_RECOMENDACION_APARTADO )
			|| Tools::isSubmit( self::SUBMIT_APARTADO_CHANGE_STATUS ) ) {
			if ( $this->_postValidation() ) {
				$this->_postProcess();
				$this->_html .= $this->renderApartadosList(
					Tools::getValue( self::ID_RECOMENDACION_APARTADO_SECCION_RELACIONADA )
				);
			} else {
				$this->_html .= $this->renderApartadosAddForm(
					Tools::getValue( self::ID_RECOMENDACION_APARTADO_SECCION_RELACIONADA )
				);
			}
			
			$this->clearCache();
		} elseif ( Tools::isSubmit( self::ADD_RECOMENDACION_APARTADO )
			|| ( Tools::isSubmit( self::ID_RECOMENDACION_APARTADO )
				&& $this->recomendacionApartadoExists( (int) Tools::getValue( self::ID_RECOMENDACION_APARTADO ) ) )
		) {
			$recomendacionSeccion = $this->getRecomendacionSeccionById(
				Tools::getValue( self::ID_RECOMENDACION_APARTADO_SECCION_RELACIONADA )
			);
			$recomendacionPagina  = $this->getRecomendacionPaginaById(
				$recomendacionSeccion[ 0 ][ 'id_planatec_recomendaciones_paginas' ]
			);
			
			$this->_html .= '<h2>' . $recomendacionPagina[ 0 ][ 'titulo' ] . ' > <strong>' . $recomendacionSeccion[ 0 ][ 'titulo' ] . '</strong></h2>';
			
			$this->_html .= $this->renderApartadosAddForm(
				Tools::getValue( self::ID_RECOMENDACION_APARTADO_SECCION_RELACIONADA )
			);
		} else {
			$this->_html .= $this->renderPaginasList();
		}
		
		return $this->_html;
	}
	
	public function recomendacionPaginaExists( $idRecomendacionPagina ) {
		$req = 'SELECT rp.`id_planatec_recomendaciones_paginas` as id_recomendacion_pagina
				FROM `' . _DB_PREFIX_ . 'planatec_recomendaciones_paginas` rp
				WHERE rp.`id_planatec_recomendaciones_paginas` = ' . (int) $idRecomendacionPagina;
		$row = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->getRow( $req );
		
		return $row;
	}
	
	public function recomendacionSeccionExists( $idRecomendacionSeccion ) {
		$req = 'SELECT rs.`id_planatec_recomendaciones_secciones` as id_recomendacion_seccion
				FROM `' . _DB_PREFIX_ . 'planatec_recomendaciones_secciones` rs
				WHERE rs.`id_planatec_recomendaciones_secciones` = ' . (int) $idRecomendacionSeccion;
		$row = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->getRow( $req );
		
		return $row;
	}
	
	public function recomendacionApartadoExists( $idRecomendacionApartado ) {
		$req = 'SELECT ra.`id_planatec_recomendaciones_apartados` as id_recomendacion_apartado
				FROM `' . _DB_PREFIX_ . 'planatec_recomendaciones_apartados` ra
				WHERE ra.`id_planatec_recomendaciones_apartados` = ' . (int) $idRecomendacionApartado;
		$row = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->getRow( $req );
		
		return $row;
	}
	
	public function getRecomendacionesPaginas( $active = null ) {
		$this->context = Context::getContext();
		$id_lang       = $this->context->language->id;
		
		$recomendacionesPaginas = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT rp.`id_planatec_recomendaciones_paginas` as id_recomendacion_pagina,
			rp.`posicion`, rp.`activo`, rpl.`titulo`
			FROM ' . _DB_PREFIX_ . 'planatec_recomendaciones_paginas rp
			LEFT JOIN ' . _DB_PREFIX_ . 'planatec_recomendaciones_paginas_lang rpl ON (rp.id_planatec_recomendaciones_paginas = rpl.id_planatec_recomendaciones_paginas)
			WHERE rpl.id_lang = ' . (int) $id_lang .
			( $active ? ' AND rp.`activo` = 1' : ' ' ) . '
         ORDER BY rp.posicion'
		);
		
		return $recomendacionesPaginas;
	}
	
	public function getRecomendacionPaginaById( $id ) {
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
		
		return $recomendacionPagina;
	}
	
	public function getRecomendacionesSecciones( $active = null ) {
		$this->context = Context::getContext();
		$id_lang       = $this->context->language->id;
		
		$recomendacionesSecciones = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT rs.`id_planatec_recomendaciones_secciones` as id_recomendacion_seccion,
			rs.`posicion`, rs.`activo`, rsl.`titulo`, rsl.`imagen`, rsl.`id_planatec_recomendaciones_paginas`
			FROM ' . _DB_PREFIX_ . 'planatec_recomendaciones_secciones rs
			LEFT JOIN ' . _DB_PREFIX_ . 'planatec_recomendaciones_secciones_lang rsl ON (rs.id_planatec_recomendaciones_secciones = rsl.id_planatec_recomendaciones_secciones)
			WHERE rsl.id_lang = ' . (int) $id_lang .
			( $active ? ' AND rs.`activo` = 1' : ' ' ) . '
			ORDER BY rs.posicion'
		);
		
		return $recomendacionesSecciones;
	}
	
	public function getRecomendacionesSeccionesByPagina( $idRecomendacionPagina, $active = null ) {
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
		
		return $recomendacionesSecciones;
	}
	
	public function getRecomendacionSeccionById( $id ) {
		$this->context = Context::getContext();
		$id_lang       = $this->context->language->id;
		
		$recomendacionSeccion = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT rs.`id_planatec_recomendaciones_secciones` as id_recomendacion_seccion,
			rs.`posicion`, rs.`activo`, rsl.`titulo`, rsl.`imagen`, rsl.`id_planatec_recomendaciones_paginas`
			FROM ' . _DB_PREFIX_ . 'planatec_recomendaciones_secciones rs
			LEFT JOIN ' . _DB_PREFIX_ . 'planatec_recomendaciones_secciones_lang rsl ON (rs.id_planatec_recomendaciones_secciones = rsl.id_planatec_recomendaciones_secciones)
			WHERE rsl.id_lang = ' . (int) $id_lang . '
			AND rs.`id_planatec_recomendaciones_secciones` = ' . (int) $id . '
         ORDER BY rs.posicion'
		);
		
		return $recomendacionSeccion;
	}
	
	public function getRecomendacionesApartados( $active = null ) {
		$this->context = Context::getContext();
		$id_lang       = $this->context->language->id;
		
		$recomendacionesApartados = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT ra.`id_planatec_recomendaciones_apartados` as id_recomendacion_apartado,
			ra.`posicion`, ra.`activo`, ral.`titulo`, ral.`contenido`, ral.`id_planatec_recomendaciones_secciones`
			FROM ' . _DB_PREFIX_ . 'planatec_recomendaciones_apartados ra
			LEFT JOIN ' . _DB_PREFIX_ . 'planatec_recomendaciones_apartados_lang ral ON (ra.id_planatec_recomendaciones_apartados = ral.id_planatec_recomendaciones_apartados)
			WHERE ral.id_lang = ' . (int) $id_lang .
			( $active ? ' AND ra.`activo` = 1' : ' ' ) . '
         ORDER BY ra.posicion'
		);
		
		return $recomendacionesApartados;
	}
	
	public function getRecomendacionesApartadosBySeccion( $idRecomendacionSeccion, $active = null ) {
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
	
	public function getRecomendacionApartadoById( $id ) {
		$this->context = Context::getContext();
		$id_lang       = $this->context->language->id;
		
		$recomendacionApartado = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT ra.`id_planatec_recomendaciones_apartados` as id_recomendacion_apartado,
			ra.`posicion`, ra.`activo`, ral.`titulo`, ral.`contenido`, ral.`id_planatec_recomendaciones_secciones`
			FROM ' . _DB_PREFIX_ . 'planatec_recomendaciones_apartados ra
			LEFT JOIN ' . _DB_PREFIX_ . 'planatec_recomendaciones_apartados_lang ral ON (ra.id_planatec_recomendaciones_apartados = ral.id_planatec_recomendaciones_apartados)
			WHERE ral.id_lang = ' . (int) $id_lang . '
			AND ra.`id_planatec_recomendaciones_apartados` = ' . (int) $id . '
         ORDER BY ra.posicion'
		);
		
		return $recomendacionApartado;
	}
	
	protected function _postValidation() {
		$errors = [];
		
		if ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_PAGINA ) ) {
			if ( !Validate::isInt( Tools::getValue( 'activo' ) )
				|| ( Tools::getValue( 'activo' ) != 0
					&& Tools::getValue( 'activo' ) != 1 )
			) {
				$errors[] = $this->getTranslator()->trans( 'Invalid state.', [], 'Modules.PlanatecRecomendaciones.Admin' );
			}
			
			if ( Tools::isSubmit( self::ID_RECOMENDACION_PAGINA ) ) {
				if ( !Validate::isInt(
						Tools::getValue( self::ID_RECOMENDACION_PAGINA )
					) && !$this->recomendacionPaginaExists( Tools::getValue( self::ID_RECOMENDACION_PAGINA ) ) ) {
					$errors[] = $this->getTranslator()->trans(
						'Invalid página ID',
						[],
						'Modules.PlanatecRecomendaciones.Admin'
					);
				}
			}
			
			$languages = Language::getLanguages( false );
			foreach ( $languages as $language ) {
				if ( Tools::strlen( Tools::getValue( 'titulo_' . $language[ 'id_lang' ] ) ) > 255 ) {
					$errors[] = $this->getTranslator()->trans(
						'The title is too long.',
						[],
						'Modules.PlanatecRecomendaciones.Admin'
					);
				}
			}
		} elseif ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_SECCION ) ) {
			if ( !Validate::isInt( Tools::getValue( 'activo' ) )
				|| ( Tools::getValue( 'activo' ) != 0
					&& Tools::getValue( 'activo' ) != 1 )
			) {
				$errors[] = $this->getTranslator()->trans( 'Invalid state.', [], 'Modules.PlanatecRecomendaciones.Admin' );
			}
			
			if ( Tools::isSubmit( self::ID_RECOMENDACION_SECCION ) ) {
				if ( !Validate::isInt(
						Tools::getValue( self::ID_RECOMENDACION_SECCION )
					) && !$this->recomendacionSeccionExists( Tools::getValue( self::ID_RECOMENDACION_SECCION ) ) ) {
					$errors[] = $this->getTranslator()->trans(
						'Invalid sección ID',
						[],
						'Modules.PlanatecRecomendaciones.Admin'
					);
				}
			}
			
			$languages = Language::getLanguages( false );
			foreach ( $languages as $language ) {
				if ( Tools::strlen( Tools::getValue( 'titulo_' . $language[ 'id_lang' ] ) ) > 255 ) {
					$errors[] = $this->getTranslator()->trans(
						'The title is too long.',
						[],
						'Modules.PlanatecRecomendaciones.Admin'
					);
				}
				
				if ( Tools::getValue( 'imagen_' . $language[ 'id_lang' ] ) != null
					&& !Validate::isFileName( Tools::getValue( 'imagen_' . $language[ 'id_lang' ] ) ) ) {
					$errors[] = $this->getTranslator()->trans(
						'Invalid filename.',
						[],
						'Modules.PlanatecRecomendaciones.Admin'
					);
				}
				
				if ( Tools::getValue( 'image_old_' . $language[ 'id_lang' ] ) != null
					&& !Validate::isFileName( Tools::getValue( 'image_old_' . $language[ 'id_lang' ] ) ) ) {
					$errors[] = $this->getTranslator()->trans(
						'Invalid filename.',
						[],
						'Modules.PlanatecRecomendaciones.Admin'
					);
				}
			}
			
			$id_lang_default = (int) Configuration::get( 'PS_LANG_DEFAULT' );
			if ( !Tools::isSubmit( 'has_picture' )
				&& ( !isset( $_FILES[ 'imagen_' . $id_lang_default ] )
					|| empty( $_FILES[ 'imagen_' . $id_lang_default ][ 'tmp_name' ] ) ) ) {
				$errors[] = $this->getTranslator()->trans(
					'The image is not set.',
					[],
					'Modules.PlanatecRecomendaciones.Admin'
				);
			}
			if ( Tools::getValue( 'image_old_' . $id_lang_default )
				&& !Validate::isFileName( Tools::getValue( 'image_old_' . $id_lang_default ) ) ) {
				$errors[] = $this->getTranslator()->trans(
					'The image is not set.',
					[],
					'Modules.PlanatecRecomendaciones.Admin'
				);
			}
		} elseif ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_APARTADO ) ) {
			if ( !Validate::isInt( Tools::getValue( 'activo' ) )
				|| ( Tools::getValue( 'activo' ) != 0
					&& Tools::getValue( 'activo' ) != 1 )
			) {
				$errors[] = $this->getTranslator()->trans( 'Invalid state.', [], 'Modules.PlanatecRecomendaciones.Admin' );
			}
			
			if ( Tools::isSubmit( self::ID_RECOMENDACION_APARTADO ) ) {
				if ( !Validate::isInt(
						Tools::getValue( self::ID_RECOMENDACION_APARTADO )
					) && !$this->recomendacionApartadoExists( Tools::getValue( self::ID_RECOMENDACION_APARTADO ) ) ) {
					$errors[] = $this->getTranslator()->trans(
						'Invalid apartado ID',
						[],
						'Modules.PlanatecRecomendaciones.Admin'
					);
				}
			}
			
			$languages = Language::getLanguages( false );
			foreach ( $languages as $language ) {
				if ( Tools::strlen( Tools::getValue( 'titulo_' . $language[ 'id_lang' ] ) ) > 255 ) {
					$errors[] = $this->getTranslator()->trans(
						'The title is too long.',
						[],
						'Modules.PlanatecRecomendaciones.Admin'
					);
				}
				
				if ( Tools::isEmpty( Tools::getValue( 'contenido_' . $language[ 'id_lang' ] ) ) ) {
					$errors[] = $this->getTranslator()->trans(
						'El contenido está vacío.',
						[],
						'Modules.PlanatecRecomendaciones.Admin'
					);
				}
			}
		}
		
		if ( count( $errors ) ) {
			$this->_html .= $this->displayError( implode( '<br />', $errors ) );
			
			return false;
		}
		
		return true;
	}
	
	protected function _postProcess() {
		$errors = [];
		
		if ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_PAGINA_CHANGE_STATUS )
			&& Tools::isSubmit( self::ID_RECOMENDACION_PAGINA )
		) {
			$recomendacionPagina = new Ps_RecomendacionesPagina( (int) Tools::getValue( self::ID_RECOMENDACION_PAGINA ) );
			
			if ( $recomendacionPagina->activo == 0 ) {
				$recomendacionPagina->activo = 1;
			} else {
				$recomendacionPagina->activo = 0;
			}
			
			$res = $recomendacionPagina->update();
			$this->clearCache();
			$this->_html .= ( $res ? $this->displayConfirmation(
				$this->getTranslator()->trans( 'Configuration updated', [], 'Admin.Notifications.Success' )
			) : $this->displayError(
				$this->getTranslator()->trans(
					'The configuration could not be updated.',
					[],
					'Modules.PlanatecRecomendaciones.Admin'
				)
			) );
		} elseif ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_PAGINA ) ) {
			if ( Tools::getValue( self::ID_RECOMENDACION_PAGINA ) ) {
				$recomendacionPagina = new Ps_RecomendacionesPagina(
					(int) Tools::getValue( self::ID_RECOMENDACION_PAGINA )
				);
				if ( !Validate::isLoadedObject( $recomendacionPagina ) ) {
					$this->_html .= $this->displayError(
						$this->getTranslator()->trans(
							'Invalid Recomendación Página ID',
							[],
							'Modules.PlanatecRecomendaciones.Admin'
						)
					);
					
					return false;
				}
			} else {
				$recomendacionPagina           = new Ps_RecomendacionesPagina();
				$recomendacionPagina->posicion = (int) $this->getSiguientePosicionRecomendacionPagina();
			}
			$recomendacionPagina->activo = (int) Tools::getValue( 'activo' );
			
			$languages = Language::getLanguages( false );
			
			foreach ( $languages as $language ) {
				$recomendacionPagina->titulo[ $language[ 'id_lang' ] ] = Tools::getValue(
					'titulo_' . $language[ 'id_lang' ]
				);
			}
			
			if ( !$errors ) {
				if ( !Tools::getValue( self::ID_RECOMENDACION_PAGINA ) ) {
					if ( !$recomendacionPagina->add() ) {
						$errors[] = $this->displayError(
							$this->getTranslator()->trans(
								'La página de recomendaciones no puede añadirse.',
								[],
								'Module.PlanatecRecomendaciones.Admin'
							)
						);
					}
				} elseif ( !$recomendacionPagina->update() ) {
					$errors[] = $this->displayError(
						$this->getTranslator()->trans(
							'La página de recomendaciones no puede actualizarse.',
							[],
							'Module.PlanatecRecomendaciones.Admin'
						)
					);
				}
				$this->clearCache();
			}
		} elseif ( Tools::isSubmit( self::DELETE_RECOMENDACION_PAGINA ) ) {
			$recomendacionPagina = new Ps_RecomendacionesPagina(
				(int) Tools::getValue( self::ID_RECOMENDACION_PAGINA )
			);
			$res                 = $recomendacionPagina->delete();
			$this->clearCache();
			if ( !$res ) {
				$this->_html .= $this->displayError( 'Could not delete.' );
			} else {
				Tools::redirectAdmin(
					$this->context->link->getAdminLink(
						'AdminModules',
						true
					) . '&conf=1&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name
				);
			}
		} elseif ( Tools::isSubmit( self::SUBMIT_SECCION_CHANGE_STATUS )
			&& Tools::isSubmit( self::ID_RECOMENDACION_SECCION )
		) {
			$recomendacionSeccion = new Ps_RecomendacionesSeccion(
				(int) Tools::getValue( self::ID_RECOMENDACION_SECCION )
			);
			
			if ( $recomendacionSeccion->activo == 0 ) {
				$recomendacionSeccion->activo = 1;
			} else {
				$recomendacionSeccion->activo = 0;
			}
			
			$res = $recomendacionSeccion->update();
			$this->clearCache();
			$this->_html .= ( $res ? $this->displayConfirmation(
				$this->getTranslator()->trans( 'Configuration updated', [], 'Admin.Notifications.Success' )
			) : $this->displayError(
				$this->getTranslator()->trans(
					'The configuration could not be updated.',
					[],
					'Modules.PlanatecRecomendaciones.Admin'
				)
			) );
		} elseif ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_SECCION ) ) {
			if ( Tools::getValue( self::ID_RECOMENDACION_SECCION ) ) {
				$recomendacionSeccion = new Ps_RecomendacionesSeccion(
					(int) Tools::getValue( self::ID_RECOMENDACION_SECCION )
				);
				if ( !Validate::isLoadedObject( $recomendacionSeccion ) ) {
					$this->_html .= $this->displayError(
						$this->getTranslator()->trans(
							'Invalid Recomendación Sección ID',
							[],
							'Modules.PlanatecRecomendaciones.Admin'
						)
					);
					
					return false;
				}
			} else {
				$recomendacionSeccion           = new Ps_RecomendacionesSeccion();
				$recomendacionSeccion->posicion = (int) $this->getSiguientePosicionRecomendacionSeccion();
			}
			$recomendacionSeccion->activo = (int) Tools::getValue( 'activo' );
			
			$languages = Language::getLanguages( false );
			
			foreach ( $languages as $language ) {
				if ( !empty( (int) Tools::getValue( 'id_planatec_recomendaciones_paginas' ) ) ) {
					$recomendacionSeccion->id_planatec_recomendaciones_paginas[ $language[ 'id_lang' ] ] = (int) Tools::getValue(
						'id_planatec_recomendaciones_paginas'
					);
				}
				
				$recomendacionSeccion->titulo[ $language[ 'id_lang' ] ] = Tools::getValue(
					'titulo_' . $language[ 'id_lang' ]
				);
				
				$type = Tools::strtolower(
					Tools::substr( strrchr( $_FILES[ 'imagen_' . $language[ 'id_lang' ] ][ 'name' ], '.' ), 1 )
				);
				if ( !empty( $type ) ) {
					$imagesize = @getimagesize( $_FILES[ 'imagen_' . $language[ 'id_lang' ] ][ 'tmp_name' ] );
					if ( isset( $_FILES[ 'imagen_' . $language[ 'id_lang' ] ] ) &&
						isset( $_FILES[ 'imagen_' . $language[ 'id_lang' ] ][ 'tmp_name' ] ) &&
						!empty( $_FILES[ 'imagen_' . $language[ 'id_lang' ] ][ 'tmp_name' ] ) &&
						!empty( $imagesize ) &&
						in_array(
							Tools::strtolower( Tools::substr( strrchr( $imagesize[ 'mime' ], '/' ), 1 ) ),
							[
								'jpg',
								'gif',
								'jpeg',
								'png',
							]
						) &&
						in_array( $type, [ 'jpg', 'gif', 'jpeg', 'png' ] )
					) {
						$temp_name = tempnam( _PS_TMP_IMG_DIR_, 'PS' );
						$salt      = sha1( microtime() );
						if ( $error = ImageManager::validateUpload( $_FILES[ 'imagen_' . $language[ 'id_lang' ] ] ) ) {
							$errors[] = $error;
						} elseif ( !$temp_name || !move_uploaded_file(
								$_FILES[ 'imagen_' . $language[ 'id_lang' ] ][ 'tmp_name' ],
								$temp_name
							) ) {
							return false;
						} elseif ( !ImageManager::resize(
							$temp_name,
							__DIR__ . '/images/' . $salt . '_' . $_FILES[ 'imagen_' . $language[ 'id_lang' ] ][ 'name' ],
							null,
							null,
							$type
						) ) {
							$errors[] = $this->displayError(
								$this->getTranslator()->trans(
									'An error occurred during the image upload process.',
									[],
									'Admin.Notifications.Error'
								)
							);
						}
						if ( file_exists( $temp_name ) ) {
							@unlink( $temp_name );
						}
						$recomendacionSeccion->imagen[ $language[ 'id_lang' ] ] = $salt . '_' . $_FILES[ 'imagen_' . $language[ 'id_lang' ] ][ 'name' ];
					} elseif ( Tools::getValue( 'image_old_' . $language[ 'id_lang' ] ) != '' ) {
						$recomendacionSeccion->imagen[ $language[ 'id_lang' ] ] = Tools::getValue(
							'image_old_' . $language[ 'id_lang' ]
						);
					}
				}
			}
			
			if ( !$errors ) {
				if ( !Tools::getValue( self::ID_RECOMENDACION_SECCION ) ) {
					if ( !$recomendacionSeccion->add() ) {
						$errors[] = $this->displayError(
							$this->getTranslator()->trans(
								'La sección de recomendaciones no puede añadirse.',
								[],
								'Modules.PlanatecRecomendaciones.Admin'
							)
						);
					}
				} elseif ( !$recomendacionSeccion->update() ) {
					$errors[] = $this->displayError(
						$this->getTranslator()->trans(
							'La sección de recomendaciones no puede actualizarse.',
							[],
							'Modules.PlanatecRecomendaciones.Admin'
						)
					);
				}
				$this->clearCache();
			}
		} elseif ( Tools::isSubmit( self::DELETE_RECOMENDACION_SECCION ) ) {
			$recomendacionSeccion = new Ps_RecomendacionesSeccion(
				(int) Tools::getValue( self::ID_RECOMENDACION_SECCION )
			);
			$res                  = $recomendacionSeccion->delete();
			$this->clearCache();
			if ( !$res ) {
				$this->_html .= $this->displayError( 'Could not delete.' );
			} else {
				Tools::redirectAdmin(
					$this->context->link->getAdminLink(
						'AdminModules',
						true
					) . '&configure=planatec_recomendaciones&idRecomendacionPagina=' . Tools::getValue(
						'idPaginaRelacionada'
					)
				);
			}
		} elseif ( Tools::isSubmit( self::SUBMIT_APARTADO_CHANGE_STATUS )
			&& Tools::isSubmit( self::ID_RECOMENDACION_APARTADO )
		) {
			$recomendacionApartado = new Ps_RecomendacionesApartado(
				(int) Tools::getValue( self::ID_RECOMENDACION_APARTADO )
			);
			
			if ( $recomendacionApartado->activo == 0 ) {
				$recomendacionApartado->activo = 1;
			} else {
				$recomendacionApartado->activo = 0;
			}
			
			$res = $recomendacionApartado->update();
			$this->clearCache();
			$this->_html .= ( $res ? $this->displayConfirmation(
				$this->getTranslator()->trans( 'Configuration updated', [], 'Admin.Notifications.Success' )
			) : $this->displayError(
				$this->getTranslator()->trans(
					'The configuration could not be updated.',
					[],
					'Modules.PlanatecRecomendaciones.Admin'
				)
			) );
		} elseif ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_APARTADO ) ) {
			if ( Tools::getValue( self::ID_RECOMENDACION_APARTADO ) ) {
				$recomendacionApartado = new Ps_RecomendacionesApartado(
					(int) Tools::getValue( self::ID_RECOMENDACION_APARTADO )
				);
				if ( !Validate::isLoadedObject( $recomendacionApartado ) ) {
					$this->_html .= $this->displayError(
						$this->getTranslator()->trans(
							'Invalid Recomendación Apartado ID',
							[],
							'Modules.PlanatecRecomendaciones.Admin'
						)
					);
					
					return false;
				}
			} else {
				$recomendacionApartado           = new Ps_RecomendacionesApartado();
				$recomendacionApartado->posicion = (int) $this->getSiguientePosicionRecomendacionApartado();
			}
			$recomendacionApartado->activo = (int) Tools::getValue( 'activo' );
			
			$languages = Language::getLanguages( false );
			
			foreach ( $languages as $language ) {
				if ( !empty( (int) Tools::getValue( 'id_planatec_recomendaciones_secciones' ) ) ) {
					$recomendacionApartado->id_planatec_recomendaciones_secciones[ $language[ 'id_lang' ] ] = (int) Tools::getValue(
						'id_planatec_recomendaciones_secciones'
					);
				}
				
				$recomendacionApartado->titulo[ $language[ 'id_lang' ] ] = Tools::getValue(
					'titulo_' . $language[ 'id_lang' ]
				);
				
				$recomendacionApartado->contenido[ $language[ 'id_lang' ] ] = Tools::getValue(
					'contenido_' . $language[ 'id_lang' ]
				);
			}
			
			if ( !$errors ) {
				if ( !Tools::getValue( self::ID_RECOMENDACION_APARTADO ) ) {
					if ( !$recomendacionApartado->add() ) {
						$errors[] = $this->displayError(
							$this->getTranslator()->trans(
								'El apartado de la sección de recomendaciones no puede añadirse.',
								[],
								'Modules.PlanatecRecomendaciones.Admin'
							)
						);
					}
				} elseif ( !$recomendacionApartado->update() ) {
					$errors[] = $this->displayError(
						$this->getTranslator()->trans(
							'El apartado de la sección de recomendaciones no puede actualizarse.',
							[],
							'Modules.PlanatecRecomendaciones.Admin'
						)
					);
				}
				$this->clearCache();
			}
		} elseif ( Tools::isSubmit( self::DELETE_RECOMENDACION_APARTADO ) ) {
			$recomendacionApartado = new Ps_RecomendacionesApartado(
				(int) Tools::getValue( self::ID_RECOMENDACION_APARTADO )
			);
			$res                   = $recomendacionApartado->delete();
			$this->clearCache();
			if ( !$res ) {
				$this->_html .= $this->displayError( 'Could not delete.' );
			} else {
				Tools::redirectAdmin(
					$this->context->link->getAdminLink(
						'AdminModules',
						true
					) . '&configure=planatec_recomendaciones&idRecomendacionSeccion=' . Tools::getValue(
						'idSeccionRelacionada'
					)
				);
			}
		}
		
		if ( count( $errors ) ) {
			$this->_html .= $this->displayError( implode( '<br />', $errors ) );
		} elseif ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_PAGINA )
			&& Tools::getValue( self::ID_RECOMENDACION_PAGINA )
		) {
			Tools::redirectAdmin(
				$this->context->link->getAdminLink(
					'AdminModules',
					true
				) . '&conf=4&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name
			);
		} elseif ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_PAGINA ) ) {
			Tools::redirectAdmin(
				$this->context->link->getAdminLink(
					'AdminModules',
					true
				) . '&conf=3&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name
			);
		} elseif ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_SECCION )
			&& Tools::getValue( self::ID_RECOMENDACION_SECCION )
		) {
			Tools::redirectAdmin(
				$this->context->link->getAdminLink(
					'AdminModules',
					true
				) . '&configure=planatec_recomendaciones&idRecomendacionPagina=' . Tools::getValue(
					'id_planatec_recomendaciones_paginas'
				)
			);
		} elseif ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_SECCION ) ) {
			Tools::redirectAdmin(
				$this->context->link->getAdminLink(
					'AdminModules',
					true
				) . '&configure=planatec_recomendaciones&idRecomendacionPagina=' . Tools::getValue(
					'id_planatec_recomendaciones_paginas'
				)
			);
		} elseif ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_APARTADO )
			&& Tools::getValue( self::ID_RECOMENDACION_APARTADO )
		) {
			Tools::redirectAdmin(
				$this->context->link->getAdminLink(
					'AdminModules',
					true
				) . '&configure=planatec_recomendaciones&idRecomendacionSeccion=' . Tools::getValue(
					'id_planatec_recomendaciones_secciones'
				)
			);
		} elseif ( Tools::isSubmit( self::SUBMIT_RECOMENDACION_APARTADO ) ) {
			Tools::redirectAdmin(
				$this->context->link->getAdminLink(
					'AdminModules',
					true
				) . '&configure=planatec_recomendaciones&idRecomendacionSeccion=' . Tools::getValue(
					'id_planatec_recomendaciones_secciones'
				)
			);
		}
	}
	
	public function renderWidget( $hookName = null, array $configuration = [] ) {
		if ( !$this->isCached( $this->templateFile, $this->getCacheId() ) ) {
			$this->smarty->assign( $this->getWidgetVariables( $hookName, $configuration ) );
		}
	}
	
	public function getWidgetVariables( $hookName = null, array $configuration = [] ) {
		$recomendacionesPaginas = $this->getRecomendacionesPaginas( true );
		
		$recomendacionesSecciones = $this->getRecomendacionesSecciones( true );
		if ( is_array( $recomendacionesSecciones ) ) {
			foreach ( $recomendacionesSecciones as &$recomendacionSeccion ) {
				$recomendacionSeccion[ 'sizes' ] = @getimagesize(
					( __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $recomendacionSeccion[ 'imagen' ] )
				);
				if ( isset( $recomendacionSeccion[ 'sizes' ][ 3 ] ) && $recomendacionSeccion[ 'sizes' ][ 3 ] ) {
					$recomendacionSeccion[ 'size' ] = $recomendacionSeccion[ 'sizes' ][ 3 ];
				}
			}
		}
		
		$recomendacionesApartados = $this->getRecomendacionesApartados( true );
		
		return [
			'recomendacionesPaginas'   => [
				'recomendacionesPaginas' => $recomendacionesPaginas
			],
			'recomendacionesSecciones' => [
				'recomendacionesSecciones' => $recomendacionesSecciones
			],
			'recomendacionesApartados' => [
				'recomendacionesApartados' => $recomendacionesApartados
			]
		];
	}
	
	public function getSiguientePosicionRecomendacionPagina() {
		$row = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->getRow(
			'SELECT MAX(rp.`posicion`) AS `siguiente_posicion`
			FROM `' . _DB_PREFIX_ . 'planatec_recomendaciones_paginas` rp'
		);
		
		return ++$row[ 'siguiente_posicion' ];
	}
	
	public function getSiguientePosicionRecomendacionSeccion() {
		$row = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->getRow(
			'SELECT MAX(rs.`posicion`) AS `siguiente_posicion`
			FROM `' . _DB_PREFIX_ . 'planatec_recomendaciones_secciones` rs'
		);
		
		return ++$row[ 'siguiente_posicion' ];
	}
	
	public function getSiguientePosicionRecomendacionApartado() {
		$row = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->getRow(
			'SELECT MAX(ra.`posicion`) AS `siguiente_posicion`
			FROM `' . _DB_PREFIX_ . 'planatec_recomendaciones_apartados` ra'
		);
		
		return ++$row[ 'siguiente_posicion' ];
	}
	
	public function renderPaginasList() {
		$recomendacionesPaginas = $this->getRecomendacionesPaginas();
		foreach ( $recomendacionesPaginas as $key => $recomendacionPagina ) {
			$recomendacionesPaginas[ $key ][ 'status' ] = $this->displayPaginaStatus(
				$recomendacionPagina[ 'id_recomendacion_pagina' ],
				$recomendacionPagina[ 'activo' ]
			);
		}
		
		$this->context->smarty->assign(
			[
				'link'                   => $this->context->link,
				'recomendacionesPaginas' => $recomendacionesPaginas
			]
		);
		
		return $this->display( __FILE__, 'list_recomendaciones_paginas.tpl' );
	}
	
	public function renderSeccionesList( $idRecomendacionPagina ) {
		$recomendacionesSecciones = $this->getRecomendacionesSeccionesByPagina( $idRecomendacionPagina );
		foreach ( $recomendacionesSecciones as $key => $recomendacionSeccion ) {
			$recomendacionesSecciones[ $key ][ 'status' ] = $this->displaySeccionStatus(
				$recomendacionSeccion[ 'id_recomendacion_seccion' ],
				$recomendacionSeccion[ 'activo' ]
			);
		}
		
		$this->context->smarty->assign(
			[
				'link'                     => $this->context->link,
				'idRecomendacionPagina'    => $idRecomendacionPagina,
				'recomendacionesSecciones' => $recomendacionesSecciones,
				'image_baseurl'            => $this->_path . 'images/'
			]
		);
		
		return $this->display( __FILE__, 'list_recomendaciones_secciones.tpl' );
	}
	
	public function renderApartadosList( $idRecomendacionSeccion ) {
		$recomendacionesApartados = $this->getRecomendacionesApartadosBySeccion( $idRecomendacionSeccion );
		foreach ( $recomendacionesApartados as $key => $recomendacionApartado ) {
			$recomendacionesApartados[ $key ][ 'status' ] = $this->displayApartadoStatus(
				$recomendacionApartado[ 'id_recomendacion_apartado' ],
				$recomendacionApartado[ 'activo' ]
			);
		}
		
		$this->context->smarty->assign(
			[
				'link'                     => $this->context->link,
				'idRecomendacionSeccion'   => $idRecomendacionSeccion,
				'recomendacionesApartados' => $recomendacionesApartados
			]
		);
		
		return $this->display( __FILE__, 'list_recomendaciones_apartados.tpl' );
	}
	
	public function displayPaginaStatus( $id_recomendacion_pagina, $activo ) {
		$title = ( (int) $activo == 0 ? $this->getTranslator()->trans(
			'Disabled',
			[],
			'Admin.Global'
		) : $this->getTranslator()->trans(
			'Enabled',
			[],
			'Admin.Global'
		) );
		$icon  = ( (int) $activo == 0 ? 'icon-remove' : 'icon-check' );
		$class = ( (int) $activo == 0 ? 'btn-danger' : 'btn-success' );
		$html  = '<a class="btn ' . $class . '" href="' . AdminController::$currentIndex .
			'&configure=' . $this->name .
			'&token=' . Tools::getAdminTokenLite( 'AdminModules' ) .
			'&' . self::SUBMIT_RECOMENDACION_PAGINA_CHANGE_STATUS . '&idRecomendacionPagina=' . (int) $id_recomendacion_pagina . '" title="' . $title . '"><i class="' . $icon . '"></i> ' . $title . '</a>';
		
		return $html;
	}
	
	public function displaySeccionStatus( $id_recomendacion_seccion, $activo ) {
		$title = ( (int) $activo == 0 ? $this->getTranslator()->trans(
			'Disabled',
			[],
			'Admin.Global'
		) : $this->getTranslator()->trans(
			'Enabled',
			[],
			'Admin.Global'
		) );
		$icon  = ( (int) $activo == 0 ? 'icon-remove' : 'icon-check' );
		$class = ( (int) $activo == 0 ? 'btn-danger' : 'btn-success' );
		$html  = '<a class="btn ' . $class . '" href="' . AdminController::$currentIndex .
			'&configure=' . $this->name .
			'&token=' . Tools::getAdminTokenLite( 'AdminModules' ) .
			'&' . self::SUBMIT_SECCION_CHANGE_STATUS . '&idRecomendacionSeccion=' . (int) $id_recomendacion_seccion . '" title="' . $title . '"><i class="' . $icon . '"></i> ' . $title . '</a>';
		
		return $html;
	}
	
	public function displayApartadoStatus( $id_recomendacion_apartado, $activo ) {
		$title = ( (int) $activo == 0 ? $this->getTranslator()->trans(
			'Disabled',
			[],
			'Admin.Global'
		) : $this->getTranslator()->trans(
			'Enabled',
			[],
			'Admin.Global'
		) );
		$icon  = ( (int) $activo == 0 ? 'icon-remove' : 'icon-check' );
		$class = ( (int) $activo == 0 ? 'btn-danger' : 'btn-success' );
		$html  = '<a class="btn ' . $class . '" href="' . AdminController::$currentIndex .
			'&configure=' . $this->name .
			'&token=' . Tools::getAdminTokenLite( 'AdminModules' ) .
			'&' . self::SUBMIT_APARTADO_CHANGE_STATUS . '&idRecomendacionApartado=' . (int) $id_recomendacion_apartado . '" title="' . $title . '"><i class="' . $icon . '"></i> ' . $title . '</a>';
		
		return $html;
	}
	
	public function renderPaginasAddForm() {
		$fields_form = [
			'form' => [
				'legend' => [
					'title' => $this->getTranslator()->trans(
						'Contenido de la página de recomendaciones',
						[],
						'Modules.PlanatecRecomendaciones.Admin'
					),
					'icon'  => 'icon-cogs'
				],
				'input'  => [
					[
						'type'     => 'text',
						'label'    => $this->getTranslator()->trans( 'Título', [], 'Modules.PlanatecRecomendaciones.Admin' ),
						'name'     => 'titulo',
						'lang'     => true,
						'required' => true
					],
					[
						'type'    => 'switch',
						'label'   => $this->getTranslator()->trans( 'Enabled', [], 'Admin.Global' ),
						'name'    => 'activo',
						'is_bool' => true,
						'values'  => [
							[
								'id'    => 'active_on',
								'value' => 1,
								'label' => $this->getTranslator()->trans( 'Yes', [], 'Admin.Global' ),
							],
							[
								'id'    => 'active_off',
								'value' => 0,
								'label' => $this->getTranslator()->trans( 'No', [], 'Admin.Global' ),
							],
						],
					]
				],
				'submit' => [
					'title' => $this->getTranslator()->trans( 'Save', [], 'Admin.Actions' )
				]
			]
		];
		
		if ( Tools::isSubmit( self::ID_RECOMENDACION_PAGINA )
			&& $this->recomendacionPaginaExists( (int) Tools::getValue( self::ID_RECOMENDACION_PAGINA ) )
		) {
			$recomendacionPagina = new Ps_RecomendacionesPagina(
				(int) Tools::getValue( self::ID_RECOMENDACION_PAGINA )
			);
			
			$fields_form[ 'form' ][ 'input' ][] = [ 'type' => 'hidden', 'name' => self::ID_RECOMENDACION_PAGINA ];
		}
		
		$helper                           = new HelperForm();
		$helper->show_toolbar             = false;
		$helper->table                    = $this->table;
		$lang                             = new Language( (int) Configuration::get( 'PS_LANG_DEFAULT' ) );
		$helper->default_form_language    = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get( 'PS_BO_ALLOW_EMPLOYEE_FORM_LANG' ) ? Configuration::get(
			'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
		) : 0;
		$helper->module                   = $this;
		$helper->identifier               = $this->identifier;
		$helper->submit_action            = self::SUBMIT_RECOMENDACION_PAGINA;
		$helper->currentIndex             = $this->context->link->getAdminLink(
				'AdminModules',
				false
			) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
		$helper->token                    = Tools::getAdminTokenLite( 'AdminModules' );
		$language                         = new Language( (int) Configuration::get( 'PS_LANG_DEFAULT' ) );
		$helper->tpl_vars                 = [
			'base_url'     => $this->context->shop->getBaseURL(),
			'language'     => [
				'id_lang'  => $language->id,
				'iso_code' => $language->iso_code,
			],
			'fields_value' => $this->getAddPaginasFieldsValues(),
			'languages'    => $this->context->controller->getLanguages(),
			'id_language'  => $this->context->language->id
		];
		
		$helper->override_folder = '/';
		
		return $helper->generateForm( [ $fields_form ] );
	}
	
	public function renderSeccionesAddForm( $idPaginaRelacionada ) {
		$fields_form = [
			'form' => [
				'legend' => [
					'title' => $this->getTranslator()->trans(
						'Contenido de la página de secciones',
						[],
						'Modules.PlanatecRecomendaciones.Admin'
					),
					'icon'  => 'icon-cogs'
				],
				'input'  => [
					[
						'type' => 'hidden',
						'name' => 'id_planatec_recomendaciones_paginas'
					],
					[
						'type'     => 'text',
						'label'    => $this->getTranslator()->trans( 'Título', [], 'Modules.PlanatecRecomendaciones.Admin' ),
						'name'     => 'titulo',
						'lang'     => true,
						'required' => true
					],
					[
						'type'     => 'file_lang',
						'label'    => $this->getTranslator()->trans( 'Image', [], 'Admin.Global' ),
						'name'     => 'imagen',
						'required' => true,
						'lang'     => true,
						'desc'     => $this->getTranslator()->trans(
							'Maximum image size: %s.',
							[ ini_get( 'upload_max_filesize' ) ],
							'Admin.Global'
						)
					],
					[
						'type'    => 'switch',
						'label'   => $this->getTranslator()->trans( 'Enabled', [], 'Admin.Global' ),
						'name'    => 'activo',
						'is_bool' => true,
						'values'  => [
							[
								'id'    => 'active_on',
								'value' => 1,
								'label' => $this->getTranslator()->trans( 'Yes', [], 'Admin.Global' ),
							],
							[
								'id'    => 'active_off',
								'value' => 0,
								'label' => $this->getTranslator()->trans( 'No', [], 'Admin.Global' ),
							],
						],
					]
				],
				'submit' => [
					'title' => $this->getTranslator()->trans( 'Save', [], 'Admin.Actions' )
				]
			]
		];
		
		if ( Tools::isSubmit( self::ID_RECOMENDACION_SECCION )
			&& $this->recomendacionSeccionExists( (int) Tools::getValue( self::ID_RECOMENDACION_SECCION ) )
		) {
			$recomendacionSeccion = new Ps_RecomendacionesSeccion(
				(int) Tools::getValue( self::ID_RECOMENDACION_SECCION )
			);
			
			$fields_form[ 'form' ][ 'input' ][] = [ 'type' => 'hidden', 'name' => self::ID_RECOMENDACION_SECCION ];
			$fields_form[ 'form' ][ 'images' ]  = $recomendacionSeccion->imagen;
			
			$has_picture = true;
			
			foreach ( Language::getLanguages( false ) as $lang ) {
				if ( !isset( $recomendacionSeccion->imagen[ $lang[ 'id_lang' ] ] ) ) {
					$has_picture &= false;
				}
			}
			
			if ( $has_picture ) {
				$fields_form[ 'form' ][ 'input' ][] = [ 'type' => 'hidden', 'name' => 'has_picture' ];
			}
		}
		
		$helper                           = new HelperForm();
		$helper->show_toolbar             = false;
		$helper->table                    = $this->table;
		$lang                             = new Language( (int) Configuration::get( 'PS_LANG_DEFAULT' ) );
		$helper->default_form_language    = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get( 'PS_BO_ALLOW_EMPLOYEE_FORM_LANG' ) ? Configuration::get(
			'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
		) : 0;
		$helper->module                   = $this;
		$helper->identifier               = $this->identifier;
		$helper->submit_action            = self::SUBMIT_RECOMENDACION_SECCION;
		$helper->currentIndex             = $this->context->link->getAdminLink(
				'AdminModules',
				false
			) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
		$helper->token                    = Tools::getAdminTokenLite( 'AdminModules' );
		$language                         = new Language( (int) Configuration::get( 'PS_LANG_DEFAULT' ) );
		$helper->tpl_vars                 = [
			'base_url'      => $this->context->shop->getBaseURL(),
			'language'      => [
				'id_lang'  => $language->id,
				'iso_code' => $language->iso_code,
			],
			'fields_value'  => $this->getAddSeccionesFieldsValues( $idPaginaRelacionada ),
			'languages'     => $this->context->controller->getLanguages(),
			'id_language'   => $this->context->language->id,
			'image_baseurl' => $this->_path . 'images/'
		];
		
		$helper->override_folder = '/';
		
		return $helper->generateForm( [ $fields_form ] );
	}
	
	public function renderApartadosAddForm( $idSeccionRelacionada ) {
		$fields_form = [
			'form' => [
				'tinymce' => true,
				'legend'  => [
					'title' => $this->getTranslator()->trans(
						'Contenido de la página de apartados',
						[],
						'Modules.PlanatecRecomendaciones.Admin'
					),
					'icon'  => 'icon-cogs'
				],
				'input'   => [
					[
						'type' => 'hidden',
						'name' => 'id_planatec_recomendaciones_secciones'
					],
					[
						'type'  => 'text',
						'label' => $this->getTranslator()->trans( 'Título', [], 'Modules.PlanatecRecomendaciones.Admin' ),
						'name'  => 'titulo',
						'lang'  => true
					],
					[
						'type'         => 'textarea',
						'label'        => $this->getTranslator()->trans(
							'Contenido',
							[],
							'Modules.PlanatecRecomendaciones.Admin'
						),
						'name'         => 'contenido',
						'lang'         => true,
						'cols'         => 40,
						'rows'         => 10,
						'autoload_rte' => true,
						'required'     => true
					],
					[
						'type'    => 'switch',
						'label'   => $this->getTranslator()->trans( 'Enabled', [], 'Admin.Global' ),
						'name'    => 'activo',
						'is_bool' => true,
						'values'  => [
							[
								'id'    => 'active_on',
								'value' => 1,
								'label' => $this->getTranslator()->trans( 'Yes', [], 'Admin.Global' ),
							],
							[
								'id'    => 'active_off',
								'value' => 0,
								'label' => $this->getTranslator()->trans( 'No', [], 'Admin.Global' ),
							],
						],
					]
				],
				'submit'  => [
					'title' => $this->getTranslator()->trans( 'Save', [], 'Admin.Actions' )
				]
			]
		];
		
		if ( Tools::isSubmit( self::ID_RECOMENDACION_APARTADO )
			&& $this->recomendacionApartadoExists( (int) Tools::getValue( self::ID_RECOMENDACION_APARTADO ) )
		) {
			$recomendacionApartado = new Ps_RecomendacionesApartado(
				(int) Tools::getValue( self::ID_RECOMENDACION_APARTADO )
			);
			
			$fields_form[ 'form' ][ 'input' ][] = [ 'type' => 'hidden', 'name' => self::ID_RECOMENDACION_APARTADO ];
		}
		
		$helper                           = new HelperForm();
		$helper->show_toolbar             = false;
		$helper->table                    = $this->table;
		$lang                             = new Language( (int) Configuration::get( 'PS_LANG_DEFAULT' ) );
		$helper->default_form_language    = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get( 'PS_BO_ALLOW_EMPLOYEE_FORM_LANG' ) ? Configuration::get(
			'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
		) : 0;
		$helper->module                   = $this;
		$helper->identifier               = $this->identifier;
		$helper->submit_action            = self::SUBMIT_RECOMENDACION_APARTADO;
		$helper->currentIndex             = $this->context->link->getAdminLink(
				'AdminModules',
				false
			) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
		$helper->token                    = Tools::getAdminTokenLite( 'AdminModules' );
		$language                         = new Language( (int) Configuration::get( 'PS_LANG_DEFAULT' ) );
		$helper->tpl_vars                 = [
			'base_url'     => $this->context->shop->getBaseURL(),
			'language'     => [
				'id_lang'  => $language->id,
				'iso_code' => $language->iso_code,
			],
			'fields_value' => $this->getAddApartadosFieldsValues( $idSeccionRelacionada ),
			'languages'    => $this->context->controller->getLanguages(),
			'id_language'  => $this->context->language->id
		];
		
		$helper->override_folder = '/';
		
		return $helper->generateForm( [ $fields_form ] );
	}
	
	public function getAddPaginasFieldsValues() {
		$fields = [];
		
		if ( Tools::isSubmit( self::ID_RECOMENDACION_PAGINA )
			&& $this->recomendacionPaginaExists( (int) Tools::getValue( self::ID_RECOMENDACION_PAGINA ) )
		) {
			$recomendacionPagina = new Ps_RecomendacionesPagina( (int) Tools::getValue( self::ID_RECOMENDACION_PAGINA ) );
			
			$fields[ self::ID_RECOMENDACION_PAGINA ] = (int) Tools::getValue(
				self::ID_RECOMENDACION_PAGINA,
				$recomendacionPagina->id
			);
		} else {
			$recomendacionPagina = new Ps_RecomendacionesPagina();
		}
		
		$fields[ 'activo' ] = Tools::getValue( 'activo', $recomendacionPagina->activo );
		
		$languages = Language::getLanguages( false );
		
		foreach ( $languages as $lang ) {
			$fields[ 'titulo' ][ $lang[ 'id_lang' ] ] = Tools::getValue(
				'titulo_' . (int) $lang[ 'id_lang' ],
				isset( $recomendacionPagina->titulo[ $lang[ 'id_lang' ] ] ) ? $recomendacionPagina->titulo[ $lang[ 'id_lang' ] ] : ''
			);
		}
		
		return $fields;
	}
	
	public function getAddSeccionesFieldsValues( $idPaginaRelacionada ) {
		$fields = [];
		
		if ( Tools::isSubmit( self::ID_RECOMENDACION_SECCION )
			&& $this->recomendacionSeccionExists( (int) Tools::getValue( self::ID_RECOMENDACION_SECCION ) )
		) {
			$recomendacionSeccion = new Ps_RecomendacionesSeccion(
				(int) Tools::getValue( self::ID_RECOMENDACION_SECCION )
			);
			
			$fields[ self::ID_RECOMENDACION_SECCION ] = (int) Tools::getValue(
				self::ID_RECOMENDACION_SECCION,
				$recomendacionSeccion->id
			);
		} else {
			$recomendacionSeccion = new Ps_RecomendacionesSeccion();
		}
		
		$fields[ 'activo' ]                              = Tools::getValue(
			'activo',
			$recomendacionSeccion->activo
		);
		$fields[ 'has_picture' ]                         = true;
		$fields[ 'id_planatec_recomendaciones_paginas' ] = $idPaginaRelacionada;
		
		$languages = Language::getLanguages( false );
		
		foreach ( $languages as $lang ) {
			$fields[ 'titulo' ][ $lang[ 'id_lang' ] ] = Tools::getValue(
				'titulo_' . (int) $lang[ 'id_lang' ],
				isset( $recomendacionSeccion->titulo[ $lang[ 'id_lang' ] ] ) ? $recomendacionSeccion->titulo[ $lang[ 'id_lang' ] ] : ''
			);
			
			$fields[ 'imagen' ][ $lang[ 'id_lang' ] ] = Tools::getValue( 'imagen_' . (int) $lang[ 'id_lang' ] );
		}
		
		return $fields;
	}
	
	public function getAddApartadosFieldsValues( $idSeccionRelacionada ) {
		$fields = [];
		
		if ( Tools::isSubmit( self::ID_RECOMENDACION_APARTADO )
			&& $this->recomendacionApartadoExists( (int) Tools::getValue( self::ID_RECOMENDACION_APARTADO ) )
		) {
			$recomendacionApartado = new Ps_RecomendacionesApartado(
				(int) Tools::getValue( self::ID_RECOMENDACION_APARTADO )
			);
			
			$fields[ self::ID_RECOMENDACION_APARTADO ] = (int) Tools::getValue(
				self::ID_RECOMENDACION_APARTADO,
				$recomendacionApartado->id
			);
		} else {
			$recomendacionApartado = new Ps_RecomendacionesApartado();
		}
		
		$fields[ 'activo' ]                                = Tools::getValue(
			'activo',
			$recomendacionApartado->activo
		);
		$fields[ 'id_planatec_recomendaciones_secciones' ] = $idSeccionRelacionada;
		
		$languages = Language::getLanguages( false );
		
		foreach ( $languages as $lang ) {
			$fields[ 'titulo' ][ $lang[ 'id_lang' ] ] = Tools::getValue(
				'titulo_' . (int) $lang[ 'id_lang' ],
				isset( $recomendacionApartado->titulo[ $lang[ 'id_lang' ] ] ) ? $recomendacionApartado->titulo[ $lang[ 'id_lang' ] ] : ''
			);
			
			$fields[ 'contenido' ][ $lang[ 'id_lang' ] ] = Tools::getValue(
				'contenido_' . (int) $lang[ 'id_lang' ],
				isset( $recomendacionApartado->contenido[ $lang[ 'id_lang' ] ] ) ? $recomendacionApartado->contenido[ $lang[ 'id_lang' ] ] : ''
			);
		}
		
		return $fields;
	}
	
	public function clearCache() {
		$this->_clearCache( $this->templateFile );
	}
}
