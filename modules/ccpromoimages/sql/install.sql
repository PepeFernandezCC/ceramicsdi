CREATE TABLE IF NOT EXISTS `PREFIX_ccpromoimages` (
  `id_imagen` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_producto` INT UNSIGNED NOT NULL,
  `tipo` ENUM('vertical','horizontal') NOT NULL,
  `url_imagen` VARCHAR(255) NOT NULL,
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_imagen`),
  UNIQUE KEY `uniq_producto_tipo` (`id_producto`, `tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
