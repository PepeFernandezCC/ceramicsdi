
CREATE TABLE IF NOT EXISTS `ps_inspirationcards` (
  id_inspiration INT AUTO_INCREMENT PRIMARY KEY,
  active TINYINT(1) DEFAULT 1,
  `image` VARCHAR(255) NULL,
  date_add DATETIME,
  date_upd DATETIME
);

CREATE TABLE IF NOT EXISTS `ps_inspirationcards_lang` (
  id_inspiration INT,
  id_lang INT,
  name VARCHAR(255),
  slug VARCHAR(255),
  PRIMARY KEY(id_inspiration, id_lang)
);

CREATE TABLE IF NOT EXISTS `ps_inspirationcards_category` (
  id_inspiration INT,
  id_category INT,
  PRIMARY KEY(id_inspiration, id_category)
);

CREATE TABLE IF NOT EXISTS `ps_inspirationcards_feature` (
  `id_inspiration_feature` INT AUTO_INCREMENT PRIMARY KEY,
  `id_inspiration` INT NOT NULL,
  `id_feature` INT NOT NULL,
  `id_feature_value` INT NULL,
  `custom_value` VARCHAR(255) NULL,
  `position` INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS `ps_inspirationcards_product` (
  `id_inspiration` INT NOT NULL,
  `id_product` INT NOT NULL,
  `position` INT NOT NULL DEFAULT 0,
  `product_type` VARCHAR(10) NOT NULL DEFAULT 'suelo',
  PRIMARY KEY (`id_inspiration`, `id_product`)
);
