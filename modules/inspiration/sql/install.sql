CREATE TABLE IF NOT EXISTS `PREFIX_inspiration_category` (
  `id_inspiration_category` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_category` INT UNSIGNED NOT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `date_add` DATETIME NULL,
  `date_upd` DATETIME NULL,
  PRIMARY KEY (`id_inspiration_category`),
  UNIQUE KEY `uniq_insp_cat_id_category` (`id_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_inspiration_category_product` (
  `id_inspiration` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_category` INT UNSIGNED NOT NULL,
  `id_product` INT UNSIGNED NOT NULL DEFAULT 0,
  `id_image` INT UNSIGNED DEFAULT NULL,
  `position` INT DEFAULT NULL,
  PRIMARY KEY (`id_inspiration`),
  KEY `id_category` (`id_category`),
  KEY `id_product` (`id_product`),
  KEY `id_image` (`id_image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `PREFIX_inspiration_category` (`id_category`, `active`, `date_add`, `date_upd`)
SELECT DISTINCT id_category, 1, NOW(), NOW() FROM `PREFIX_inspiration_category_product`;
