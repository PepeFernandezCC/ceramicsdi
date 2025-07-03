<?php

/**
 * @author Julio Colás
 */
class Ps_RecomendacionesPagina extends ObjectModel {
	
	public $titulo;
	
	public $activo;
	public $posicion;
	
	public static $definition = [
		'table'     => 'planatec_recomendaciones_paginas',
		'primary'   => 'id_planatec_recomendaciones_paginas',
		'multilang' => true,
		'fields'    => [
			'activo'   => [ 'type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true ],
			'posicion' => [ 'type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true ],
			
			// Campos del idioma
			'titulo'   => [ 'type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255 ]
		]
	];
	
	public function add( $autodate = true, $null_values = false ) {
		return parent::add( $autodate, $null_values );
	}
	
	public function delete() {
		$res = $this->reOrderPositions();
		
		$res &= parent::delete();
		
		return $res;
	}
	
	public function reOrderPositions() {
		$id_recomendacionesPagina = $this->id;
		
		$max = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT MAX(rp.`posicion`) as posicion
			FROM `' . _DB_PREFIX_ . 'planatec_recomendaciones_paginas` rp'
		);
		
		if ( (int) $max == (int) $id_recomendacionesPagina ) {
			return true;
		}
		
		$rows = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT rp.`posicion` as posicion, rp.`id_planatec_recomendaciones_paginas` as id_recomendacion_pagina
			FROM `' . _DB_PREFIX_ . 'planatec_recomendaciones_paginas` rp
			WHERE rp.`posicion` > ' . (int) $this->posicion
		);
		
		foreach ( $rows as $row ) {
			$current_recomendacionPagina = new Ps_RecomendacionesPagina( $row[ 'id_recomendacion_pagina' ] );
			--$current_recomendacionPagina->posicion;
			$current_recomendacionPagina->update();
			unset( $current_recomendacionPagina );
		}
		
		return true;
	}
}