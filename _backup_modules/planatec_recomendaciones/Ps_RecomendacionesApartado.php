<?php

/**
 * @author Julio Colás
 */
class Ps_RecomendacionesApartado extends ObjectModel {
	
	public $titulo;
	public $contenido;
	public $id_planatec_recomendaciones_secciones;
	
	public $activo;
	public $posicion;
	
	public static $definition = [
		'table'     => 'planatec_recomendaciones_apartados',
		'primary'   => 'id_planatec_recomendaciones_apartados',
		'multilang' => true,
		'fields'    => [
			'activo'                                => [
				'type'     => self::TYPE_BOOL,
				'validate' => 'isBool',
				'required' => true
			],
			'posicion'                              => [
				'type'     => self::TYPE_INT,
				'validate' => 'isunsignedInt',
				'required' => true
			],
			
			// Campos del idioma
			'id_planatec_recomendaciones_secciones' => [
				'type'     => self::TYPE_INT,
				'lang'     => true,
				'validate' => 'isunsignedInt',
				'required' => true
			],
			'titulo'                                => [
				'type'     => self::TYPE_STRING,
				'lang'     => true,
				'validate' => 'isCleanHtml',
				'size'     => 255
			],
			'contenido'                             => [
				'type'     => self::TYPE_HTML,
				'lang'     => true,
				'validate' => 'isCleanHtml'
			]
		]
	];
	
	public function add( $auto_date = true, $null_values = false ) {
		return parent::add( $auto_date, $null_values );
	}
	
	public function delete() {
		$res = $this->reOrderPositions();
		
		$res &= parent::delete();
		
		return $res;
	}
	
	public function reOrderPositions() {
		$id_recomendacionesApartado = $this->id;
		
		$max = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT MAX(ra.`posicion`) as posicion
			FROM `' . _DB_PREFIX_ . 'planatec_recomendaciones_apartados` ra'
		);
		
		if ( (int) $max == (int) $id_recomendacionesApartado ) {
			return true;
		}
		
		$rows = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT ra.`posicion` as posicion, ra.`id_planatec_recomendaciones_apartados` as id_recomendacion_apartado
			FROM `' . _DB_PREFIX_ . 'planatec_recomendaciones_apartados` ra
			WHERE ra.`posicion` > ' . (int) $this->posicion
		);
		
		foreach ( $rows as $row ) {
			$current_recomendacionApartado = new Ps_RecomendacionesApartado( $row[ 'id_recomendacion_apartado' ] );
			--$current_recomendacionApartado->posicion;
			$current_recomendacionApartado->update();
			unset( $current_recomendacionApartado );
		}
		
		return true;
	}
}