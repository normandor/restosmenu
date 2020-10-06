# ************************************************************
# Sequel Pro SQL dump
# Version 5416
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 192.168.42.20 (MySQL 5.6.33-0ubuntu0.14.04.1)
# Database: restos
# Generation Time: 2020-10-06 04:27:23 +0000
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
	(18,'combo','asdfa','asdfas','',1,1,123,1,5),
	(6,'basico','Entradas','',NULL,1,1,NULL,0,3),
	(13,'basico','Entradas','','',1,2,NULL,NULL,3),
	(12,'none','Sin asignar','',NULL,0,1,NULL,NULL,4),
	(16,'combo','combo1','asdf','',1,2,1,1,4),
	(19,'text','restaurant_name','','',1,1,NULL,NULL,1),
	(20,'image','restaurant_logo','','images/logos/1/a0dac555c8eca364afc513311aa629b1.png',0,1,NULL,NULL,2),
	(21,'text','restaurant_name','','',1,23,NULL,NULL,1),
	(22,'image','restaurant_logo','','',0,23,NULL,NULL,2),
	(23,'basico','Entradas','','',1,23,NULL,NULL,3),
	(24,'text','restaurant_name','','',1,2,NULL,NULL,1),
	(25,'image','restaurant_logo','',NULL,0,2,NULL,NULL,2),
	(26,'basico','Entradas','','',1,24,NULL,NULL,3);

/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table combo_dish
# ------------------------------------------------------------

CREATE TABLE `combo_dish` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `combo_id` int(250) NOT NULL,
  `dish_id` int(1) NOT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `order_show` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



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
	(2,'euro','?',1);

/*!40000 ALTER TABLE `currency` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dish
# ------------------------------------------------------------

CREATE TABLE `dish` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `description` varchar(250) DEFAULT NULL,
  `imageUrl` varchar(250) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `category_id` int(5) NOT NULL,
  `enabled` int(1) NOT NULL,
  `restaurant_id` int(5) DEFAULT NULL,
  `order_show` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `fk_currency_id` (`currency_id`),
  KEY `fk_category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `dish` WRITE;
/*!40000 ALTER TABLE `dish` DISABLE KEYS */;

INSERT INTO `dish` (`id`, `name`, `description`, `imageUrl`, `price`, `currency_id`, `category_id`, `enabled`, `restaurant_id`, `order_show`)
VALUES
	(19,'Plato visible',NULL,NULL,1,1,13,1,2,0),
	(20,'Plato no visible',NULL,NULL,2,1,13,0,2,0),
	(13,'Quesadillas','Nuestra especialidad',NULL,2.2,1,5,1,1,0),
	(21,'plato3','asdf',NULL,3,1,13,1,2,0),
	(24,'Milanesa a la napolitana','Nuestra especialidad','images/dishes/1/a39ae7cd23e798d18538679b6532cd7d.png',11,1,6,1,1,1),
	(25,'Tortilla de papas','Riquisima',NULL,33,1,6,1,1,2);

/*!40000 ALTER TABLE `dish` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table image
# ------------------------------------------------------------

CREATE TABLE `image` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `url` varchar(250) NOT NULL DEFAULT '',
  `witdh` varchar(15) NOT NULL DEFAULT '',
  `height` varchar(15) NOT NULL DEFAULT '',
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
	(4,'Gestion',0,0,80,'','fa fa-book'),
	(5,'Celular',1,2,10,'view_menu_mobile','fa fa-book'),
	(7,'Platos',1,3,30,'show_dishes','fa fa-book'),
	(6,'Categorias',1,3,20,'show_categories','fa fa-book'),
	(3,'Menus',0,0,20,'','fa fa-book'),
	(40,'Texto e imágenes',0,12,25,'show_page_settings','fa fa-book'),
	(8,'Restaurant',0,0,0,'show_restaurant','fa fa-area-chart'),
	(9,'Promociones',0,0,30,'','fa fa-book'),
	(10,'Combos',1,9,20,'show_combos','fa fa-book'),
	(11,'Platos',1,9,30,'show_dishes_combos','fa fa-book'),
	(12,'Apariencia',0,0,45,'','fa fa-book'),
	(41,'Orden',0,12,25,'show_page_order','fa fa-book'),
	(43,'PC',1,2,20,'view_menu_desktop','fa fa-book'),
	(44,'Personal',1,4,80,'user_list','fa fa-user'),
	(45,'Restaurants',1,4,80,'restaurant_list','fa fa-user');

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
  `country` varchar(3),
  `city_region` varchar(150),
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
  `qr_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `restaurant` WRITE;
/*!40000 ALTER TABLE `restaurant` DISABLE KEYS */;

INSERT INTO `restaurant` (`id`, `name`, `enabled`, `selected`, `slug`, `qr_url`)
VALUES
	(1,'Demo Restaurant',1,1,'don-pepe','images/qr/qr-don-pepe.png'),
	(2,'Demo Restaurant 2',1,1,'don-pepe-2','images/qr/qr-don-pepe.png'),
	(24,'restob',1,1,'resto-b',''),
	(23,'restoa',1,1,'resto-a','');

/*!40000 ALTER TABLE `restaurant` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table settings_image
# ------------------------------------------------------------

CREATE TABLE `settings_image` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `key` varchar(250) NOT NULL DEFAULT '',
  `name` varchar(250) NOT NULL DEFAULT '',
  `property` varchar(250) NOT NULL DEFAULT '',
  `value` varchar(250) NOT NULL DEFAULT '',
  `value_mobile` varchar(250) DEFAULT NULL,
  `restaurant_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `settings_image` WRITE;
/*!40000 ALTER TABLE `settings_image` DISABLE KEYS */;

INSERT INTO `settings_image` (`id`, `key`, `name`, `property`, `value`, `value_mobile`, `restaurant_id`)
VALUES
	(1,'restaurant_logo','Logo restaurant','visible','false',NULL,1),
	(12,'restaurant_logo','Logo restaurant','width','30%','30%',1),
	(13,'dish','Platos','visible','true',NULL,1),
	(14,'dish','Platos','width','50%','70%',1),
	(70,'dish','Platos','width','30%','30%',23),
	(69,'dish','Platos','visible','true',NULL,23),
	(68,'restaurant_logo','Logo restaurant','width','30%','30%',23),
	(67,'restaurant_logo','Logo restaurant','visible','false',NULL,23),
	(71,'restaurant_logo','Logo restaurant','visible','false',NULL,24),
	(72,'restaurant_logo','Logo restaurant','width','30%','30%',24),
	(73,'dish','Platos','visible','true',NULL,24),
	(74,'dish','Platos','width','30%','30%',24);

/*!40000 ALTER TABLE `settings_image` ENABLE KEYS */;
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
	(17,'menu_restaurant_title','Titulo restaurant','font-family','\"Arial Black\", Gadget, sans-serif',2),
	(2,'menu_restaurant_title','Titulo restaurant','font-size','30px',1),
	(3,'menu_restaurant_title','Titulo restaurant','color','#b86d05',1),
	(4,'menu_restaurant_title','Titulo restaurant','background-color','',1),
	(5,'menu_body','Cuerpo de la carta','font-family','',1),
	(6,'menu_body','Cuerpo de la carta','font-size','',1),
	(7,'menu_body','Cuerpo de la carta','color','#b86d05',1),
	(8,'menu_body','Cuerpo de la carta','background-color','#e4d7af',1),
	(9,'menu_category','Categorias','font-family','Arial, Helvetica, sans-serif',1),
	(10,'menu_category','Categorias','font-size','30px',1),
	(11,'menu_category','Categorias','color','#b86d05',1),
	(12,'menu_category','Categorias','background-color','',1),
	(13,'menu_promo_title','Promociones','font-family','\"Lucida Sans Unicode\", \"Lucida Grande\", sans-serif',1),
	(14,'menu_promo_title','Promociones','font-size','30px',1),
	(15,'menu_promo_title','Promociones','color','#b86d05',1),
	(16,'menu_promo_title','Promociones','background-color','',1),
	(18,'menu_restaurant_title','Titulo restaurant','font-size','30px',2),
	(19,'menu_restaurant_title','Titulo restaurant','color','#6f4444',2),
	(20,'menu_restaurant_title','Titulo restaurant','background-color','',2),
	(21,'menu_body','Cuerpo de la carta','font-family','',2),
	(22,'menu_body','Cuerpo de la carta','font-size','',2),
	(23,'menu_body','Cuerpo de la carta','color','#6f4444',2),
	(24,'menu_body','Cuerpo de la carta','background-color','#e4d7af',2),
	(25,'menu_category','Categorias','font-family','\"Comic Sans MS\", cursive, sans-serif',2),
	(26,'menu_category','Categorias','font-size','30px',2),
	(27,'menu_category','Categorias','color','#804f0a',2),
	(28,'menu_category','Categorias','background-color','',2),
	(29,'menu_promo_title','Promociones','font-family','\"Lucida Sans Unicode\", \"Lucida Grande\", sans-serif',2),
	(30,'menu_promo_title','Promociones','font-size','30px',2),
	(31,'menu_promo_title','Promociones','color','#804f0a',2),
	(32,'menu_promo_title','Promociones','background-color','',2),
	(33,'menu_dish','Platos','font-family','\"Lucida Sans Unicode\", \"Lucida Grande\", sans-serif',1),
	(34,'menu_dish','Platos','font-size','30px',1),
	(35,'menu_dish','Platos','color','#b86d05',1),
	(36,'menu_dish','Platos','background-color','',1),
	(71,'menu_promo_title','Promociones','font-size','26px',23),
	(70,'menu_promo_title','Promociones','font-family','\"Arial Black\", Gadget, sans-serif',23),
	(69,'menu_category','Categorias','background-color','',23),
	(68,'menu_category','Categorias','color','#b86d05',23),
	(67,'menu_category','Categorias','font-size','26px',23),
	(66,'menu_category','Categorias','font-family','\"Arial Black\", Gadget, sans-serif',23),
	(65,'menu_body','Cuerpo de la carta','background-color','#e4d7af',23),
	(64,'menu_body','Cuerpo de la carta','color','#b86d05',23),
	(63,'menu_body','Cuerpo de la carta','font-size','',23),
	(62,'menu_body','Cuerpo de la carta','font-family','',23),
	(61,'menu_restaurant_title','Titulo restaurant','background-color','',23),
	(60,'menu_restaurant_title','Titulo restaurant','color','#b86d05',23),
	(59,'menu_restaurant_title','Titulo restaurant','font-size','30px',23),
	(58,'menu_restaurant_title','Titulo restaurant','font-family','\"Arial Black\", Gadget, sans-serif',23),
	(72,'menu_promo_title','Promociones','color','#b86d05',23),
	(73,'menu_promo_title','Promociones','background-color','',23),
	(74,'menu_dish','Platos','font-family','\"Arial Black\", Gadget, sans-serif',23),
	(75,'menu_dish','Platos','font-size','22px',23),
	(76,'menu_dish','Platos','color','#b86d05',23),
	(77,'menu_dish','Platos','background-color','',23),
	(78,'menu_restaurant_title','Titulo restaurant','font-family','\"Arial Black\", Gadget, sans-serif',24),
	(79,'menu_restaurant_title','Titulo restaurant','font-size','30px',24),
	(80,'menu_restaurant_title','Titulo restaurant','color','#b86d05',24),
	(81,'menu_restaurant_title','Titulo restaurant','background-color','',24),
	(82,'menu_body','Cuerpo de la carta','font-family','',24),
	(83,'menu_body','Cuerpo de la carta','font-size','',24),
	(84,'menu_body','Cuerpo de la carta','color','#b86d05',24),
	(85,'menu_body','Cuerpo de la carta','background-color','#e4d7af',24),
	(86,'menu_category','Categorias','font-family','\"Arial Black\", Gadget, sans-serif',24),
	(87,'menu_category','Categorias','font-size','26px',24),
	(88,'menu_category','Categorias','color','#b86d05',24),
	(89,'menu_category','Categorias','background-color','',24),
	(90,'menu_promo_title','Promociones','font-family','\"Arial Black\", Gadget, sans-serif',24),
	(91,'menu_promo_title','Promociones','font-size','26px',24),
	(92,'menu_promo_title','Promociones','color','#b86d05',24),
	(93,'menu_promo_title','Promociones','background-color','',24),
	(94,'menu_dish','Platos','font-family','\"Arial Black\", Gadget, sans-serif',24),
	(95,'menu_dish','Platos','font-size','22px',24),
	(96,'menu_dish','Platos','color','#b86d05',24),
	(97,'menu_dish','Platos','background-color','',24);

/*!40000 ALTER TABLE `settings_page` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table settings_page_preview
# ------------------------------------------------------------

CREATE TABLE `settings_page_preview` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `key` varchar(250) NOT NULL DEFAULT '',
  `name` varchar(250) NOT NULL DEFAULT '',
  `property` varchar(250) NOT NULL DEFAULT '',
  `value` varchar(250) NOT NULL DEFAULT '',
  `restaurant_id` int(11) NOT NULL,
  `is_synced` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `settings_page_preview` WRITE;
/*!40000 ALTER TABLE `settings_page_preview` DISABLE KEYS */;

INSERT INTO `settings_page_preview` (`id`, `key`, `name`, `property`, `value`, `restaurant_id`, `is_synced`)
VALUES
	(1,'menu_restaurant_title','Titulo restaurant','font-family','\"Arial Black\", Gadget, sans-serif',1,1),
	(17,'menu_restaurant_title','Titulo restaurant','font-family','\"Arial Black\", Gadget, sans-serif',2,1),
	(2,'menu_restaurant_title','Titulo restaurant','font-size','30px',1,1),
	(3,'menu_restaurant_title','Titulo restaurant','color','#b86d05',1,1),
	(4,'menu_restaurant_title','Titulo restaurant','background-color','',1,1),
	(5,'menu_body','Cuerpo de la carta','font-family','',1,1),
	(6,'menu_body','Cuerpo de la carta','font-size','22px',1,0),
	(7,'menu_body','Cuerpo de la carta','color','#b86d05',1,1),
	(8,'menu_body','Cuerpo de la carta','background-color','#e4d7af',1,1),
	(9,'menu_category','Categorias','font-family','Arial, Helvetica, sans-serif',1,1),
	(10,'menu_category','Categorias','font-size','30px',1,1),
	(11,'menu_category','Categorias','color','#b86d05',1,1),
	(12,'menu_category','Categorias','background-color','',1,1),
	(13,'menu_promo_title','Promociones','font-family','\"Lucida Sans Unicode\", \"Lucida Grande\", sans-serif',1,1),
	(14,'menu_promo_title','Promociones','font-size','30px',1,1),
	(15,'menu_promo_title','Promociones','color','#b86d05',1,1),
	(16,'menu_promo_title','Promociones','background-color','',1,1),
	(18,'menu_restaurant_title','Titulo restaurant','font-size','30px',2,1),
	(19,'menu_restaurant_title','Titulo restaurant','color','#6f4444',2,1),
	(20,'menu_restaurant_title','Titulo restaurant','background-color','',2,1),
	(21,'menu_body','Cuerpo de la carta','font-family','',2,1),
	(22,'menu_body','Cuerpo de la carta','font-size','',2,1),
	(23,'menu_body','Cuerpo de la carta','color','#6f4444',2,1),
	(24,'menu_body','Cuerpo de la carta','background-color','#e4d7af',2,1),
	(25,'menu_category','Categorias','font-family','\"Comic Sans MS\", cursive, sans-serif',2,1),
	(26,'menu_category','Categorias','font-size','30px',2,1),
	(27,'menu_category','Categorias','color','#804f0a',2,1),
	(28,'menu_category','Categorias','background-color','',2,1),
	(29,'menu_promo_title','Promociones','font-family','\"Lucida Sans Unicode\", \"Lucida Grande\", sans-serif',2,1),
	(30,'menu_promo_title','Promociones','font-size','30px',2,1),
	(31,'menu_promo_title','Promociones','color','#804f0a',2,1),
	(32,'menu_promo_title','Promociones','background-color','',2,1),
	(33,'menu_dish','Platos','font-family','\"Lucida Sans Unicode\", \"Lucida Grande\", sans-serif',1,1),
	(34,'menu_dish','Platos','font-size','30px',1,1),
	(35,'menu_dish','Platos','color','#b86d05',1,1),
	(36,'menu_dish','Platos','background-color','',1,1),
	(37,'menu_restaurant_title','Titulo restaurant','font-family','\"Arial Black\", Gadget, sans-serif',23,1),
	(38,'menu_restaurant_title','Titulo restaurant','font-size','30px',23,1),
	(39,'menu_restaurant_title','Titulo restaurant','color','#b86d05',23,1),
	(40,'menu_restaurant_title','Titulo restaurant','background-color','',23,1),
	(41,'menu_body','Cuerpo de la carta','font-family','',23,1),
	(42,'menu_body','Cuerpo de la carta','font-size','',23,1),
	(43,'menu_body','Cuerpo de la carta','color','#b86d05',23,1),
	(44,'menu_body','Cuerpo de la carta','background-color','#e4d7af',23,1),
	(45,'menu_category','Categorias','font-family','\"Arial Black\", Gadget, sans-serif',23,1),
	(46,'menu_category','Categorias','font-size','26px',23,1),
	(47,'menu_category','Categorias','color','#b86d05',23,1),
	(48,'menu_category','Categorias','background-color','',23,1),
	(49,'menu_promo_title','Promociones','font-family','\"Arial Black\", Gadget, sans-serif',23,1),
	(50,'menu_promo_title','Promociones','font-size','26px',23,1),
	(51,'menu_promo_title','Promociones','color','#b86d05',23,1),
	(52,'menu_promo_title','Promociones','background-color','',23,1),
	(53,'menu_dish','Platos','font-family','\"Arial Black\", Gadget, sans-serif',23,1),
	(54,'menu_dish','Platos','font-size','22px',23,1),
	(55,'menu_dish','Platos','color','#b86d05',23,1),
	(56,'menu_dish','Platos','background-color','',23,1),
	(57,'menu_restaurant_title','Titulo restaurant','font-family','\"Arial Black\", Gadget, sans-serif',24,1),
	(58,'menu_restaurant_title','Titulo restaurant','font-size','30px',24,1),
	(59,'menu_restaurant_title','Titulo restaurant','color','#b86d05',24,1),
	(60,'menu_restaurant_title','Titulo restaurant','background-color','',24,1),
	(61,'menu_body','Cuerpo de la carta','font-family','',24,1),
	(62,'menu_body','Cuerpo de la carta','font-size','',24,1),
	(63,'menu_body','Cuerpo de la carta','color','#b86d05',24,1),
	(64,'menu_body','Cuerpo de la carta','background-color','#e4d7af',24,1),
	(65,'menu_category','Categorias','font-family','\"Arial Black\", Gadget, sans-serif',24,1),
	(66,'menu_category','Categorias','font-size','26px',24,1),
	(67,'menu_category','Categorias','color','#b86d05',24,1),
	(68,'menu_category','Categorias','background-color','',24,1),
	(69,'menu_promo_title','Promociones','font-family','\"Arial Black\", Gadget, sans-serif',24,1),
	(70,'menu_promo_title','Promociones','font-size','26px',24,1),
	(71,'menu_promo_title','Promociones','color','#b86d05',24,1),
	(72,'menu_promo_title','Promociones','background-color','',24,1),
	(73,'menu_dish','Platos','font-family','\"Arial Black\", Gadget, sans-serif',24,1),
	(74,'menu_dish','Platos','font-size','22px',24,1),
	(75,'menu_dish','Platos','color','#b86d05',24,1),
	(76,'menu_dish','Platos','background-color','',24,1);

/*!40000 ALTER TABLE `settings_page_preview` ENABLE KEYS */;
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
	(276,'4e688855-4a5b-457e-8077-b3d63b60f0d5','d18a2d8d-7ca6-4fbb-9ada-4024f0c82319','admin','Mr','Admin','admin@example.com','[\"ROLE_ADMIN\", \"ROLE_API\", \"ROLE_MANAGER\"]','$2y$13$jIdNI8xFyD5v3Xvaxh9yb.9pQ6JgpsmCv4MAORto9PfsVFWA55VEK',1,'images/avatars/db8e1af0cb3aca1ae2d0018624204529.png',1,0,1),
	(277,NULL,NULL,'a','a','a','a','[\"ROLE_ADMIN\", \"ROLE_API\"]','$2y$13$L57znYpZMAcMZ/3Fmd936O/yjv.sKNw.pwxGEUg0k393ZLnDecOKK',0,'/images/avatars/avatar_gen.png',0,0,2);

/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
