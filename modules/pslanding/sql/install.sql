CREATE TABLE IF NOT EXISTS `ps_pslanding` (
  `id_pslanding` INT(11) NOT NULL AUTO_INCREMENT,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `template` VARCHAR(64) NOT NULL DEFAULT 'landing-default',
  `id_feature_value_collection` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `hero_media` VARCHAR(255) NULL,
  `hero_media_mobile` VARCHAR (255) NULL,
  `hero2_media` VARCHAR(255) NULL,
  `hero2_product` INT(10) NULL,
  `block2_image` VARCHAR(255) NULL,
  `block3_image` VARCHAR(255) NULL,
  `block4_image` VARCHAR(255) NULL,
  `block5_image` VARCHAR(255) NULL,
  `block6_image` VARCHAR(255) NULL,
  `block7_image` VARCHAR(255) NULL,
  `date_add` DATETIME NULL,
  `date_upd` DATETIME NULL,
  PRIMARY KEY (`id_pslanding`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ps_pslanding_lang` (
  `id_pslanding` INT(11) NOT NULL,
  `id_lang` INT(11) NOT NULL,
  `id_shop` INT(11) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `external_url` VARCHAR(255) NULL,
  `hero_title` VARCHAR(255) NULL,
  `hero_subtitle` TEXT NULL,
  `hero2_button` VARCHAR(255) NULL,
  `hero2_title` TEXT NULL,
  `block2_title` VARCHAR(255) NULL,
  `block2_text` TEXT NULL,
  `block3_title` VARCHAR(255) NULL,
  `block3_text` TEXT NULL,
  `block4_title` VARCHAR(255) NULL,
  `block4_text` TEXT NULL,
  `block5_title` VARCHAR(255) NULL,
  `block5_text` TEXT NULL,
  `block6_title` VARCHAR(255) NULL,
  `block6_text` TEXT NULL,
  `block7_title` VARCHAR(255) NULL,
  `block7_text` TEXT NULL,
  `products_title` VARCHAR(255) NULL,
  `products_subtitle` TEXT NULL,
  PRIMARY KEY (`id_pslanding`,`id_lang`,`id_shop`),
  KEY `idx_slug_lang_shop` (`slug`, `id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ps_pslanding_characteristic` (
  `id_pslanding_characteristic` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pslanding` INT(10) UNSIGNED NOT NULL,
  `position` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_pslanding_characteristic`),
  KEY `idx_pslanding` (`id_pslanding`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ps_pslanding_characteristic_lang` (
  `id_pslanding_characteristic` INT(10) UNSIGNED NOT NULL,
  `id_lang` INT(10) UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `text` TEXT NOT NULL,
  PRIMARY KEY (`id_pslanding_characteristic`, `id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ps_pslanding_slide` (
  `id_pslanding_slide` INT(11) NOT NULL AUTO_INCREMENT,
  `id_pslanding` INT(11) NOT NULL,
  `position` INT(11) NOT NULL DEFAULT 1,
  `id_product` INT(11) NULL,
  `id_category` INT(11) NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_pslanding_slide`),
  KEY `idx_pslanding` (`id_pslanding`, `position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ps_pslanding_slide_lang` (
  `id_pslanding_slide` INT UNSIGNED NOT NULL,
  `id_lang` INT UNSIGNED NOT NULL,
  `image` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_pslanding_slide`, `id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `ps_pslanding_slide`
  ADD COLUMN `slot` VARCHAR(64) NOT NULL DEFAULT 'carousel_1' AFTER `id_pslanding`;

CREATE INDEX `idx_pslanding_slide_slot`
  ON `ps_pslanding_slide` (`id_pslanding`, `slot`, `position`);
