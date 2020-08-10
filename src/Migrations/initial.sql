# ************************************************************
# Sequel Pro SQL dump
# Version 5416
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 192.168.42.20 (MySQL 5.6.33-0ubuntu0.14.04.1)
# Database: restos
# Generation Time: 2020-08-10 08:55:20 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table category
# ------------------------------------------------------------

CREATE TABLE `category` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `category_type` varchar(50) DEFAULT NULL,
  `name` varchar(250) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `image_url` varchar(255) DEFAULT NULL,
  `enabled` int(1) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `price` float DEFAULT NULL,
  `currency_id` int(5) DEFAULT NULL,
  `order_show` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;

INSERT INTO `category` (`id`, `category_type`, `name`, `description`, `image_url`, `enabled`, `restaurant_id`, `price`, `currency_id`, `order_show`)
VALUES
	(5,'combo','Promoción en Quesadillas','2 quesadillas por el precio de 1',NULL,0,1,2.2,1,3),
	(6,'basico','Entradas','',NULL,1,1,NULL,0,1);

/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table combo_dish
# ------------------------------------------------------------

CREATE TABLE `combo_dish` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `combo_id` int(250) NOT NULL,
  `dish_id` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `combo_dish` WRITE;
/*!40000 ALTER TABLE `combo_dish` DISABLE KEYS */;

INSERT INTO `combo_dish` (`id`, `combo_id`, `dish_id`)
VALUES
	(17,11,17),
	(15,5,13),
	(14,5,13),
	(18,11,15);

/*!40000 ALTER TABLE `combo_dish` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table currency
# ------------------------------------------------------------

CREATE TABLE `currency` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `symbol` varchar(5) DEFAULT NULL,
  `enabled` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `currency` WRITE;
/*!40000 ALTER TABLE `currency` DISABLE KEYS */;

INSERT INTO `currency` (`id`, `name`, `symbol`, `enabled`)
VALUES
	(1,'peso','$',1),
	(2,'euro','€',1);

/*!40000 ALTER TABLE `currency` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dish
# ------------------------------------------------------------

CREATE TABLE `dish` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `description` varchar(250) DEFAULT NULL,
  `image` varchar(250) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `category_id` int(5) NOT NULL,
  `enabled` int(1) NOT NULL,
  `restaurant_id` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `fk_currency_id` (`currency_id`),
  KEY `fk_category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `dish` WRITE;
/*!40000 ALTER TABLE `dish` DISABLE KEYS */;

INSERT INTO `dish` (`id`, `name`, `description`, `image`, `price`, `currency_id`, `category_id`, `enabled`, `restaurant_id`)
VALUES
	(16,'Milanesa a la napolitana','Nuestra especialidad',NULL,3,1,6,1,1),
	(13,'Quesadillas','Nuestra especialidad',NULL,2.2,1,5,1,1);

/*!40000 ALTER TABLE `dish` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table image
# ------------------------------------------------------------

CREATE TABLE `image` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `section_id` int(3) NOT NULL,
  `image_url` varchar(250) NOT NULL DEFAULT '',
  `enabled` int(1) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table menu
# ------------------------------------------------------------

CREATE TABLE `menu` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `level` int(2) NOT NULL,
  `parent` int(5) NOT NULL,
  `position` int(2) NOT NULL,
  `path` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;

INSERT INTO `menu` (`id`, `name`, `level`, `parent`, `position`, `path`, `icon`)
VALUES
	(1,'Inicio',0,0,0,'dashboard','fa fa-area-chart'),
	(2,'Vista previa',0,0,10,'','fa fa-book'),
	(4,'Personal',0,0,80,'user_list','fa fa-user'),
	(5,'Ver carta',1,2,10,'view_menu_1','fa fa-book'),
	(7,'Platos',1,3,30,'show_dishes','fa fa-book'),
	(6,'Categorias',1,3,20,'show_categories','fa fa-book'),
	(3,'Menus',0,0,20,'','fa fa-book'),
	(40,'Colores y fuentes',0,12,25,'show_page_settings','fa fa-book'),
	(8,'Restaurant',0,0,0,'show_restaurant','fa fa-area-chart'),
	(9,'Promociones',0,0,30,'','fa fa-book'),
	(10,'Combos',1,9,20,'show_combos','fa fa-book'),
	(11,'Platos',1,9,30,'show_dishes_combos','fa fa-book'),
	(12,'Apariencia',0,0,25,'','fa fa-book'),
	(41,'Orden',0,12,25,'show_page_order','fa fa-book');

/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table migration_versions
# ------------------------------------------------------------

CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table page_visits
# ------------------------------------------------------------

CREATE TABLE `page_visits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) NOT NULL,
  `datetime` datetime DEFAULT CURRENT_TIMESTAMP,
  `country` varchar(3) NOT NULL,
  `city_region` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `page_visits` WRITE;
/*!40000 ALTER TABLE `page_visits` DISABLE KEYS */;

INSERT INTO `page_visits` (`id`, `ip`, `datetime`, `country`, `city_region`)
VALUES
	(4,'1.1.1.1','2020-08-07 17:34:38','ARG','');

/*!40000 ALTER TABLE `page_visits` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table restaurant
# ------------------------------------------------------------

CREATE TABLE `restaurant` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `enabled` int(1) NOT NULL,
  `selected` int(1) NOT NULL DEFAULT '0',
  `slug` varchar(255) NOT NULL DEFAULT '',
  `logo_url` varchar(255) DEFAULT NULL,
  `qr_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `restaurant` WRITE;
/*!40000 ALTER TABLE `restaurant` DISABLE KEYS */;

INSERT INTO `restaurant` (`id`, `name`, `enabled`, `selected`, `slug`, `logo_url`, `qr_url`)
VALUES
	(1,'Demo Restaurant',1,1,'don-pepe','images/logos/logo-restaurant.png','images/qr/qr-don-pepe.png');

/*!40000 ALTER TABLE `restaurant` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table settings_page
# ------------------------------------------------------------

CREATE TABLE `settings_page` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `key` varchar(250) NOT NULL DEFAULT '',
  `name` varchar(250) NOT NULL DEFAULT '',
  `property` varchar(250) NOT NULL DEFAULT '',
  `value` varchar(250) NOT NULL DEFAULT '',
  `restaurant_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `settings_page` WRITE;
/*!40000 ALTER TABLE `settings_page` DISABLE KEYS */;

INSERT INTO `settings_page` (`id`, `key`, `name`, `property`, `value`, `restaurant_id`)
VALUES
	(1,'menu_restaurant_title','Titulo restaurant','font-family','\"Arial Black\", Gadget, sans-serif',1),
	(2,'menu_restaurant_title','Titulo restaurant','font-size','30px',1),
	(3,'menu_restaurant_title','Titulo restaurant','color','#6f4444',1),
	(4,'menu_restaurant_title','Titulo restaurant','background-color','',1),
	(5,'menu_body','Cuerpo de la carta','font-family','',1),
	(6,'menu_body','Cuerpo de la carta','font-size','',1),
	(7,'menu_body','Cuerpo de la carta','color','#6f4444',1),
	(8,'menu_body','Cuerpo de la carta','background-color','#e4d7af',1),
	(9,'menu_category','Categorias','font-family','\"Comic Sans MS\", cursive, sans-serif',1),
	(10,'menu_category','Categorias','font-size','30px',1),
	(11,'menu_category','Categorias','color','#804f0a',1),
	(12,'menu_category','Categorias','background-color','',1),
	(13,'menu_promo_title','Promociones','font-family','\"Lucida Sans Unicode\", \"Lucida Grande\", sans-serif',1),
	(14,'menu_promo_title','Promociones','font-size','30px',1),
	(15,'menu_promo_title','Promociones','color','#804f0a',1),
	(16,'menu_promo_title','Promociones','background-color','',1);

/*!40000 ALTER TABLE `settings_page` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user
# ------------------------------------------------------------

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logout_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verified` int(1) NOT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_panel` int(1) NOT NULL,
  `deleted` int(1) NOT NULL,
  `restaurant_id` int(5) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;

INSERT INTO `user` (`id`, `auth_token`, `logout_token`, `username`, `firstname`, `lastname`, `email`, `roles`, `password`, `verified`, `avatar_path`, `access_panel`, `deleted`, `restaurant_id`)
VALUES
	(276,'4e688855-4a5b-457e-8077-b3d63b60f0d5','d18a2d8d-7ca6-4fbb-9ada-4024f0c82319','admin','Mr','Admin','admin@example.com','[\"ROLE_ADMIN\", \"ROLE_API\"]','$2y$13$xJqAUvOiz/5wcv4rDGg0D.MPEaMzWTy7mmZ5sBNx.Bf2VHvkKUwwa',1,'/images/avatars/avatar_gen.png',1,0,1);

/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
