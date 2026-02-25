CREATE TABLE IF NOT EXISTS `ps_product_review` (
  `id_review` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_product` INT UNSIGNED NOT NULL,
  `id_customer` INT UNSIGNED NOT NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL,
  `comment` TEXT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 0,
  `date_add` DATETIME NOT NULL,
  PRIMARY KEY (`id_review`),
  KEY `idx_product` (`id_product`),
  KEY `idx_customer` (`id_customer`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ps_product_review_image` (
  `id_image` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_review` INT UNSIGNED NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `date_add` DATETIME NOT NULL,
  PRIMARY KEY (`id_image`),
  KEY `idx_review` (`id_review`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ps_product_review_email_log` (
  `id_email_log` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_order` INT UNSIGNED NOT NULL,
  `id_product` INT UNSIGNED NOT NULL,
  `date_add` DATETIME NOT NULL,
  PRIMARY KEY (`id_email_log`),
  UNIQUE KEY `uniq_order_product` (`id_order`,`id_product`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;