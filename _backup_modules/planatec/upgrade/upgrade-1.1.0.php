<?php

if ( !defined( '_PS_VERSION_' ) )
	exit;

function upgrade_module_1_1_0( $object ) {
	$sqls = array();
	
	// Planatec Categorías Contenido Extra
	$sqlCreateTable = '
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra_lang` (
			`id_planatec_categorias_contenido_extra` int(10) unsigned NOT NULL,
			`id_lang` int(10) unsigned NOT NULL,
			`content` text,
			`button_text` varchar(255),
			PRIMARY KEY (`id_planatec_categorias_contenido_extra`, `id_lang`)
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
	';
	Db::getInstance()->execute( $sqlCreateTable );
	
	$rows = Db::getInstance()->executeS(
		'
		SELECT * FROM `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra`
		'
	);
	
	if ( $rows ) {
		foreach ( $rows as $row ) {
			$res = Db::getInstance()->execute(
				'
					SELECT * FROM `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra_lang`
					WHERE `id_planatec_categorias_contenido_extra` = ' . $row[ 'id_planatec_categorias_contenido_extra' ] . '
					'
			);
			
			if ( $res && Db::getInstance()->numRows() === 0 ) {
				$sqls[] = '
					INSERT INTO `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra_lang` (`content`, `button_text`, `id_planatec_categorias_contenido_extra`, `id_lang`)
					VALUES ("'
					. str_replace( '"', '\"', $row[ 'content' ] )
					. '", "' . str_replace( '"', '\"', $row[ 'button_text' ] )
					. '", ' . $row[ 'id_planatec_categorias_contenido_extra' ]
					. ', 1' . ')
				';
			} else {
				$sqls[] = '
					UPDATE `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra_lang`
					SET `content` = "' . $row[ 'content' ] . '",
					`button_text` = "' . $row[ 'button_text' ] . '",
					WHERE `id_planatec_categorias_contenido_extra` = ' . $row[ 'id_planatec_categorias_contenido_extra' ] . '
				';
			}
		}
	}
	
	// Planatec Home Section
	$sqlCreateTable = '
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'planatec_home_section_lang` (
			`id_planatec_home_section` int(10) unsigned NOT NULL,
			`id_lang` int(10) unsigned NOT NULL,
			`image_url` varchar(255) NOT NULL,
			`content` text,
			`button_url` varchar(255),
			PRIMARY KEY (`id_planatec_home_section`, `id_lang`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
	';
	Db::getInstance()->execute( $sqlCreateTable );
	
	$rows = Db::getInstance()->executeS(
		'
		SELECT * FROM `' . _DB_PREFIX_ . 'planatec_home_section`
		'
	);
	
	if ( $rows ) {
		foreach ( $rows as $row ) {
			$res = Db::getInstance()->execute(
				'
					SELECT * FROM `' . _DB_PREFIX_ . 'planatec_home_section_lang`
					WHERE `id_planatec_home_section` = ' . $row[ 'id_planatec_home_section' ] . '
				'
			);
			
			if ( $res && Db::getInstance()->numRows() === 0 ) {
				$sqls[] = '
					INSERT INTO `' . _DB_PREFIX_ . 'planatec_home_section_lang` (`content`, `button_url`, `id_planatec_home_section`, `id_lang`)
					VALUES ("'
					. str_replace( '"', '\"', $row[ 'content' ] )
					. '", "' . str_replace( '"', '\"', $row[ 'button_url' ] )
					. '", ' . $row[ 'id_planatec_home_section' ]
					. ', 1' . ')
				';
			} else {
				$sqls[] = '
					UPDATE `' . _DB_PREFIX_ . 'planatec_home_section_lang`
					SET `content` = "' . $row[ 'content' ] . '",
					`button_url` = "' . $row[ 'button_text' ] . '",
					WHERE `id_planatec_home_section` = ' . $row[ 'id_planatec_home_section' ] . '
				';
			}
		}
	}
	
	// Ejecución de los SQL
	foreach ( $sqls as $sql ) {
		Db::getInstance()->execute( $sql );
	}
	
	return true;
}