CREATE TABLE IF NOT EXISTS `ps_ccincidencias_log` (
  `id_ccincidencias_log` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip` VARCHAR(45) NOT NULL,
  `sent` TINYINT(1) NOT NULL DEFAULT 0,
  `date_add` DATETIME NOT NULL,
  PRIMARY KEY (`id_ccincidencias_log`),
  KEY `ip_date` (`ip`, `date_add`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ps_ccincidencias_tipo` (
  `id_ccincidencias_tipo` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(32) NOT NULL,
  `email` VARCHAR(150) NULL,
  `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `position` INT UNSIGNED NOT NULL DEFAULT 0,
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_ccincidencias_tipo`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ps_ccincidencias_tipo_lang` (
  `id_ccincidencias_tipo` INT UNSIGNED NOT NULL,
  `id_lang` INT UNSIGNED NOT NULL,
  `descripcion` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_ccincidencias_tipo`, `id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
