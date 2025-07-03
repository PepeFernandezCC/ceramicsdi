<?php

/**
 * @author Julio Colás
 */
class Ps_RecomendacionesSeccion extends ObjectModel {
	
	public $titulo;
	public $imagen;
	public $id_planatec_recomendaciones_paginas;
	
	public $activo;
	public $posicion;
	
	public static $definition = [
		'table'     => 'planatec_recomendaciones_secciones',
		'primary'   => 'id_planatec_recomendaciones_secciones',
		'multilang' => true,
		'fields'    => [
			'activo'                              => [
				'type'     => self::TYPE_BOOL,
				'validate' => 'isBool',
				'required' => true
			],
			'posicion'                            => [
				'type'     => self::TYPE_INT,
				'validate' => 'isunsignedInt',
				'required' => true
			],
			
			// Campos del idioma
			'id_planatec_recomendaciones_paginas' => [
				'type'     => self::TYPE_INT,
				'lang'     => true,
				'validate' => 'isunsignedInt',
				'required' => true
			],
			'titulo'                              => [
				'type'     => self::TYPE_STRING,
				'lang'     => true,
				'validate' => 'isCleanHtml',
				'size'     => 255
			],
			'imagen'                              => [
				'type'     => self::TYPE_STRING,
				'lang'     => true,
				'validate' => 'isCleanHtml',
				'size'     => 255
			]
		]
	];
	
	public function add( $auto_date = true, $null_values = false ) {
		return parent::add( $auto_date, $null_values );
	}
	
	public function delete() {
		$res = true;
		
		$images = $this->imagen;
		foreach ( $images as $image ) {
			if ( $image && file_exists( __DIR__ . '/images/' . $image ) ) {
				$res &= @unlink( __DIR__ . '/images/' . $image );
			}
		}
		
		$res &= $this->reOrderPositions();
		
		$res &= parent::delete();
		
		return $res;
	}
	
	public function reOrderPositions() {
		$id_recomendacionesSeccion = $this->id;
		
		$max = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT MAX(rs.`posicion`) as posicion
			FROM `' . _DB_PREFIX_ . 'planatec_recomendaciones_secciones` rs'
		);
		
		if ( (int) $max == (int) $id_recomendacionesSeccion ) {
			return true;
		}
		
		$rows = Db::getInstance( (bool) _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT rs.`posicion` as posicion, rs.`id_planatec_recomendaciones_secciones` as id_recomendacion_seccion
			FROM `' . _DB_PREFIX_ . 'planatec_recomendaciones_secciones` rs
			WHERE rs.`posicion` > ' . (int) $this->posicion
		);
		
		foreach ( $rows as $row ) {
			$current_recomendacionSeccion = new Ps_RecomendacionesSeccion( $row[ 'id_recomendacion_seccion' ] );
			--$current_recomendacionSeccion->posicion;
			$current_recomendacionSeccion->update();
			unset( $current_recomendacionSeccion );
		}
		
		return true;
	}
}