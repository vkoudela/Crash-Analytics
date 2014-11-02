# ************************************************************
# Sequel Pro SQL dump
# Version 4135
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: mysql (MySQL 5.5.38-0ubuntu0.12.04.1)
# Database: crash_analytics
# Generation Time: 2014-11-02 15:21:30 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table brand
# ------------------------------------------------------------

DROP TABLE IF EXISTS `brand`;

CREATE TABLE `brand` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `total` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `brand_name` (`name`),
  KEY `total` (`total`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table country
# ------------------------------------------------------------

DROP TABLE IF EXISTS `country`;

CREATE TABLE `country` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `tld` char(2) NOT NULL,
  `country` varchar(80) NOT NULL,
  `iso3` char(3) DEFAULT NULL,
  `numcode` char(3) DEFAULT NULL,
  `phone_code` varchar(3) DEFAULT NULL,
  `total` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tld` (`tld`),
  KEY `total` (`total`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `country` WRITE;
/*!40000 ALTER TABLE `country` DISABLE KEYS */;

INSERT INTO `country` (`id`, `tld`, `country`, `iso3`, `numcode`, `phone_code`, `total`)
VALUES
	(1,'af','Afghanistan','AFG','004',NULL,0),
	(2,'al','Albania','ALB','008',NULL,572),
	(3,'dz','Algeria','DZA','012',NULL,6),
	(4,'as','American Samoa','ASM','016',NULL,0),
	(5,'ao','Angola','AGO','024',NULL,253),
	(6,'ai','Anguilla','AIA','660',NULL,0),
	(7,'aq','Antarctica','ATA','010',NULL,0),
	(8,'ag','Antigua and Barbuda','ATG','028',NULL,26),
	(9,'ar','Argentina','ARG','032',NULL,34026),
	(10,'am','Armenia','ARM','051',NULL,1422),
	(11,'aw','Aruba','ABW','533',NULL,65),
	(12,'au','Australia','AUS','036',NULL,15012),
	(13,'at','Austria','AUT','040','43',4962),
	(14,'az','Azerbaijan','AZE','031',NULL,111),
	(15,'bs','Bahamas','BHS','044',NULL,0),
	(16,'bh','Bahrain','BHR','048',NULL,0),
	(17,'bd','Bangladesh','BGD','050',NULL,5477),
	(18,'bb','Barbados','BRB','052',NULL,0),
	(19,'by','Belarus','BLR','112',NULL,586),
	(20,'be','Belgium','BEL','056',NULL,7039),
	(21,'bz','Belize','BLZ','084',NULL,0),
	(22,'bj','Benin','BEN','204',NULL,0),
	(23,'bm','Bermuda','BMU','060',NULL,0),
	(24,'bt','Bhutan','BTN','064',NULL,58),
	(25,'bo','Bolivia','BOL','068',NULL,6548),
	(26,'ba','Bosnia and Herzegovina','BIH','070','387',376),
	(27,'bw','Botswana','BWA','072',NULL,131),
	(28,'bv','Bouvet Island','BVT','074',NULL,0),
	(29,'br','Brazil','BRA','076',NULL,1011785),
	(30,'io','British Indian Ocean Territory','IOT','086',NULL,0),
	(31,'bn','Brunei Darussalam','BRN','096',NULL,3111),
	(32,'bg','Bulgaria','BGR','100',NULL,2042),
	(33,'bf','Burkina Faso','BFA','854',NULL,0),
	(34,'bi','Burundi','BDI','108',NULL,0),
	(35,'kh','Cambodia','KHM','116',NULL,651),
	(36,'cm','Cameroon','CMR','120',NULL,1533),
	(37,'ca','Canada','CAN','124',NULL,8268),
	(38,'cv','Cape Verde','CPV','132',NULL,64),
	(39,'ky','Cayman Islands','CYM','136',NULL,0),
	(40,'cf','Central African Republic','CAF','140',NULL,0),
	(41,'td','Chad','TCD','148',NULL,0),
	(42,'cl','Chile','CHL','152',NULL,8940),
	(43,'cn','China','CHN','156',NULL,899),
	(44,'cx','Christmas Island','CXR','162',NULL,0),
	(45,'cc','Cocos (Keeling) Islands','CCK','166',NULL,52),
	(46,'co','Colombia','COL','170',NULL,12617),
	(47,'km','Comoros','COM','174',NULL,0),
	(48,'cg','Congo','COG','178',NULL,0),
	(49,'cd','Congo, the Democratic Republic of the','COD','180',NULL,0),
	(50,'ck','Cook Islands','COK','184',NULL,0),
	(51,'cr','Costa Rica','CRI','188',NULL,201),
	(52,'ci','Cote D\'Ivoire','CIV','384',NULL,3568),
	(53,'hr','Croatia','HRV','191','385',9042),
	(54,'cu','Cuba','CUB','192',NULL,0),
	(55,'cy','Cyprus','CYP','196',NULL,301),
	(56,'cz','Czech Republic','CZE','203',NULL,8979),
	(57,'dk','Denmark','DNK','208',NULL,1456),
	(58,'dj','Djibouti','DJI','262',NULL,0),
	(59,'dm','Dominica','DMA','212',NULL,0),
	(60,'do','Dominican Republic','DOM','214',NULL,1211),
	(61,'ec','Ecuador','ECU','218',NULL,2141),
	(62,'eg','Egypt','EGY','818',NULL,5004),
	(63,'sv','El Salvador','SLV','222',NULL,33),
	(64,'gq','Equatorial Guinea','GNQ','226',NULL,0),
	(65,'er','Eritrea','ERI','232',NULL,0),
	(66,'ee','Estonia','EST','233',NULL,1456),
	(67,'et','Ethiopia','ETH','231',NULL,0),
	(68,'fk','Falkland Islands (Malvinas)','FLK','238',NULL,0),
	(69,'fo','Faroe Islands','FRO','234',NULL,0),
	(70,'fj','Fiji','FJI','242',NULL,170),
	(71,'fi','Finland','FIN','246',NULL,3367),
	(72,'fr','France','FRA','250',NULL,11773),
	(73,'gf','French Guiana','GUF','254',NULL,0),
	(74,'pf','French Polynesia','PYF','258',NULL,0),
	(75,'tf','French Southern Territories','ATF','260',NULL,0),
	(76,'ga','Gabon','GAB','266',NULL,0),
	(77,'gm','Gambia','GMB','270',NULL,0),
	(78,'ge','Georgia','GEO','268',NULL,429),
	(79,'de','Germany','DEU','276','41',62772),
	(80,'gh','Ghana','GHA','288',NULL,751),
	(81,'gi','Gibraltar','GIB','292',NULL,0),
	(82,'gr','Greece','GRC','300',NULL,10276),
	(83,'gl','Greenland','GRL','304',NULL,0),
	(84,'gd','Grenada','GRD','308',NULL,0),
	(85,'gp','Guadeloupe','GLP','312',NULL,0),
	(86,'gu','Guam','GUM','316',NULL,0),
	(87,'gt','Guatemala','GTM','320',NULL,2326),
	(88,'gn','Guinea','GIN','324',NULL,0),
	(89,'gw','Guinea-Bissau','GNB','624',NULL,0),
	(90,'gy','Guyana','GUY','328',NULL,64),
	(91,'ht','Haiti','HTI','332',NULL,31),
	(92,'hm','Heard Island and Mcdonald Islands','HMD','334',NULL,0),
	(93,'va','Holy See (Vatican City State)','VAT','336',NULL,0),
	(94,'hn','Honduras','HND','340',NULL,307),
	(95,'hk','Hong Kong','HKG','344',NULL,1296),
	(96,'hu','Hungary','HUN','348',NULL,8811),
	(97,'is','Iceland','ISL','352',NULL,392),
	(98,'in','India','IND','356',NULL,59863),
	(99,'id','Indonesia','IDN','360',NULL,31974),
	(100,'ir','Iran, Islamic Republic of','IRN','364',NULL,1250),
	(101,'iq','Iraq','IRQ','368',NULL,0),
	(102,'ie','Ireland','IRL','372',NULL,897),
	(103,'il','Israel','ISR','376',NULL,13878),
	(104,'it','Italy','ITA','380',NULL,40154),
	(105,'jm','Jamaica','JAM','388',NULL,0),
	(106,'jp','Japan','JPN','392',NULL,11052),
	(107,'jo','Jordan','JOR','400',NULL,3872),
	(108,'kz','Kazakhstan','KAZ','398',NULL,6253),
	(109,'ke','Kenya','KEN','404',NULL,695),
	(110,'ki','Kiribati','KIR','296',NULL,0),
	(111,'kp','Korea, Democratic People\'s Republic of','PRK','408',NULL,0),
	(112,'kr','Korea, Republic of','KOR','410',NULL,0),
	(113,'kw','Kuwait','KWT','414',NULL,0),
	(114,'kg','Kyrgyzstan','KGZ','417',NULL,493),
	(115,'la','Lao People\'s Democratic Republic','LAO','418',NULL,1193),
	(116,'lv','Latvia','LVA','428',NULL,842),
	(117,'lb','Lebanon','LBN','422',NULL,1252),
	(118,'ls','Lesotho','LSO','426',NULL,93),
	(119,'lr','Liberia','LBR','430',NULL,0),
	(120,'ly','Libyan Arab Jamahiriya','LBY','434',NULL,368),
	(121,'li','Liechtenstein','LIE','438',NULL,0),
	(122,'lt','Lithuania','LTU','440',NULL,1609),
	(123,'lu','Luxembourg','LUX','442',NULL,106),
	(124,'mo','Macao','MAC','446',NULL,0),
	(125,'mk','Macedonia, the Former Yugoslav Republic of','MKD','807',NULL,153),
	(126,'mg','Madagascar','MDG','450',NULL,244),
	(127,'mw','Malawi','MWI','454',NULL,2),
	(128,'my','Malaysia','MYS','458',NULL,1076232),
	(129,'mv','Maldives','MDV','462',NULL,102),
	(130,'ml','Mali','MLI','466',NULL,0),
	(131,'mt','Malta','MLT','470',NULL,0),
	(132,'mh','Marshall Islands','MHL','584',NULL,0),
	(133,'mq','Martinique','MTQ','474',NULL,0),
	(134,'mr','Mauritania','MRT','478',NULL,0),
	(135,'mu','Mauritius','MUS','480',NULL,129),
	(136,'yt','Mayotte','MYT','175',NULL,0),
	(137,'mx','Mexico','MEX','484',NULL,59506),
	(138,'fm','Micronesia, Federated States of','FSM','583',NULL,0),
	(139,'md','Moldova, Republic of','MDA','498',NULL,544),
	(140,'mc','Monaco','MCO','492',NULL,0),
	(141,'mn','Mongolia','MNG','496',NULL,4),
	(142,'me','Montenegro','MNE','499',NULL,85),
	(143,'ms','Montserrat','MSR','500',NULL,0),
	(144,'ma','Morocco','MAR','504',NULL,1572),
	(145,'mz','Mozambique','MOZ','508',NULL,301),
	(146,'mm','Myanmar','MMR','104',NULL,1047),
	(147,'na','Namibia','NAM','516',NULL,870),
	(148,'nr','Nauru','NRU','520',NULL,0),
	(149,'np','Nepal','NPL','524',NULL,1455),
	(150,'nl','Netherlands','NLD','528',NULL,15351),
	(151,'an','Netherlands Antilles','ANT','530',NULL,0),
	(152,'nc','New Caledonia','NCL','540',NULL,10),
	(153,'nz','New Zealand','NZL','554',NULL,5307),
	(154,'ni','Nicaragua','NIC','558',NULL,1482),
	(155,'ne','Niger','NER','562',NULL,0),
	(156,'ng','Nigeria','NGA','566',NULL,0),
	(157,'nu','Niue','NIU','570',NULL,0),
	(158,'nf','Norfolk Island','NFK','574',NULL,0),
	(159,'mp','Northern Mariana Islands','MNP','580',NULL,0),
	(160,'no','Norway','NOR','578',NULL,2161),
	(161,'om','Oman','OMN','512',NULL,103),
	(162,'pk','Pakistan','PAK','586',NULL,3593),
	(163,'pw','Palau','PLW','585',NULL,0),
	(164,'ps','Palestinian Territory, Occupied','PSE','275',NULL,1),
	(165,'pa','Panama','PAN','591',NULL,0),
	(166,'pg','Papua New Guinea','PNG','598',NULL,15),
	(167,'py','Paraguay','PRY','600',NULL,3077),
	(168,'pe','Peru','PER','604',NULL,979),
	(169,'ph','Philippines','PHL','608',NULL,982),
	(170,'pn','Pitcairn','PCN','612',NULL,0),
	(171,'pl','Poland','POL','616',NULL,8161),
	(172,'pt','Portugal','PRT','620',NULL,6221),
	(173,'pr','Puerto Rico','PRI','630',NULL,0),
	(174,'qa','Qatar','QAT','634',NULL,0),
	(175,'re','Reunion','REU','638',NULL,0),
	(176,'ro','Romania','ROM','642',NULL,3108),
	(177,'ru','Russian Federation','RUS','643',NULL,37000),
	(178,'rw','Rwanda','RWA','646',NULL,1),
	(179,'sh','Saint Helena','SHN','654',NULL,0),
	(180,'kn','Saint Kitts and Nevis','KNA','659',NULL,0),
	(181,'lc','Saint Lucia','LCA','662',NULL,0),
	(182,'pm','Saint Pierre and Miquelon','SPM','666',NULL,0),
	(183,'vc','Saint Vincent and the Grenadines','VCT','670',NULL,0),
	(184,'ws','Samoa','WSM','882',NULL,24),
	(185,'sm','San Marino','SMR','674',NULL,0),
	(186,'st','Sao Tome and Principe','STP','678',NULL,0),
	(187,'sa','Saudi Arabia','SAU','682',NULL,1512),
	(188,'sn','Senegal','SEN','686',NULL,0),
	(189,'rs','Serbia','SRB','688',NULL,5844),
	(190,'sc','Seychelles','SYC','690',NULL,0),
	(191,'sl','Sierra Leone','SLE','694',NULL,0),
	(192,'sg','Singapore','SGP','702',NULL,18404),
	(193,'sk','Slovakia','SVK','703',NULL,3199),
	(194,'si','Slovenia','SVN','705',NULL,289),
	(195,'sb','Solomon Islands','SLB','090',NULL,0),
	(196,'so','Somalia','SOM','706',NULL,0),
	(197,'za','South Africa','ZAF','710',NULL,4908538),
	(198,'gs','South Georgia and the South Sandwich Islands','SGS','239',NULL,0),
	(199,'es','Spain','ESP','724',NULL,10103),
	(200,'lk','Sri Lanka','LKA','144',NULL,74),
	(201,'sd','Sudan','SDN','736',NULL,0),
	(202,'sr','Suriname','SUR','740',NULL,0),
	(203,'sj','Svalbard and Jan Mayen','SJM','744',NULL,0),
	(204,'sz','Swaziland','SWZ','748',NULL,0),
	(205,'se','Sweden','SWE','752',NULL,4034),
	(206,'ch','Switzerland','CHE','756',NULL,24911),
	(207,'sy','Syrian Arab Republic','SYR','760',NULL,0),
	(208,'tw','Taiwan, Province of China','TWN','158',NULL,594),
	(209,'tj','Tajikistan','TJK','762',NULL,17),
	(210,'tz','Tanzania, United Republic of','TZA','834',NULL,387),
	(211,'th','Thailand','THA','764',NULL,71854),
	(212,'tl','Timor-Leste','TLS','626',NULL,0),
	(213,'tg','Togo','TGO','768',NULL,39),
	(214,'tk','Tokelau','TKL','772',NULL,1),
	(215,'to','Tonga','TON','776',NULL,0),
	(216,'tt','Trinidad and Tobago','TTO','780',NULL,615),
	(217,'tn','Tunisia','TUN','788',NULL,0),
	(218,'tr','Turkey','TUR','792',NULL,5059),
	(219,'tm','Turkmenistan','TKM','795',NULL,7),
	(220,'tc','Turks and Caicos Islands','TCA','796',NULL,63),
	(221,'tv','Tuvalu','TUV','798',NULL,395),
	(222,'ug','Uganda','UGA','800',NULL,169),
	(223,'ua','Ukraine','UKR','804',NULL,4516),
	(224,'ae','United Arab Emirates','ARE','784',NULL,1829),
	(225,'gb','United Kingdom','GBR','826',NULL,0),
	(226,'us','United States','USA','840','1',366),
	(227,'um','United States Minor Outlying Islands','UMI','581',NULL,0),
	(228,'uy','Uruguay','URY','858',NULL,2239),
	(229,'uz','Uzbekistan','UZB','860',NULL,802),
	(230,'vu','Vanuatu','VUT','548',NULL,0),
	(231,'ve','Venezuela','VEN','862',NULL,2317),
	(232,'vn','Viet Nam','VNM','704',NULL,5660),
	(233,'vg','Virgin Islands, British','VGB','092',NULL,0),
	(234,'vi','Virgin Islands, U.s.','VIR','850',NULL,14),
	(235,'wf','Wallis and Futuna','WLF','876',NULL,0),
	(236,'eh','Western Sahara','ESH','732',NULL,0),
	(237,'ye','Yemen','YEM','887',NULL,4622),
	(238,'zm','Zambia','ZMB','894',NULL,160),
	(239,'zw','Zimbabwe','ZWE','716',NULL,10776);

/*!40000 ALTER TABLE `country` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table crash_archive
# ------------------------------------------------------------

DROP TABLE IF EXISTS `crash_archive`;

CREATE TABLE `crash_archive` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `package_id` bigint(20) unsigned DEFAULT NULL,
  `package_version_id` bigint(20) unsigned DEFAULT NULL,
  `brand_id` bigint(20) DEFAULT NULL,
  `model_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `os` enum('Android','iOS','Windows') NOT NULL DEFAULT 'Android',
  `os_version_id` bigint(20) unsigned DEFAULT NULL,
  `total_mem_size` bigint(20) DEFAULT NULL,
  `available_mem_size` bigint(20) DEFAULT NULL,
  `user_comment` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `user_email` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `user_app_start_date` datetime DEFAULT NULL,
  `user_crash_date` datetime DEFAULT NULL,
  `user_app_lifetime` bigint(20) DEFAULT NULL,
  `stack_trace_id` bigint(20) DEFAULT NULL,
  `country_id` bigint(20) unsigned DEFAULT NULL,
  `provider_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`),
  KEY `package_id` (`package_id`),
  KEY `package_version_id` (`package_version_id`),
  KEY `brand_id` (`brand_id`),
  KEY `model_id` (`model_id`),
  KEY `os` (`os`),
  KEY `os_version_id` (`os_version_id`),
  KEY `country_id` (`country_id`),
  KEY `provider_id` (`provider_id`),
  KEY `stack_trace_id` (`stack_trace_id`),
  KEY `product_id` (`product_id`),
  KEY `stacks_per_package` (`package_id`,`stack_trace_id`),
  KEY `stack_per_brand` (`brand_id`,`stack_trace_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table crash_archive_meta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `crash_archive_meta`;

CREATE TABLE `crash_archive_meta` (
  `crash_id` bigint(20) unsigned NOT NULL,
  `name` enum('report_id','environment','build','settings_global','settings_system','settings_secure','device_features','shared_preferences','initial_configuration','crash_configuration','dumpsys_meminfo','display','stack_trace','logcat','tktal_mem_size','@evice_features','installation_id','file_path','dropbox','is_silent','custom_data') NOT NULL DEFAULT 'report_id',
  `value` longtext,
  PRIMARY KEY (`crash_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table crash_archive_meta_unknown
# ------------------------------------------------------------

DROP TABLE IF EXISTS `crash_archive_meta_unknown`;

CREATE TABLE `crash_archive_meta_unknown` (
  `report_id` bigint(20) NOT NULL,
  `meta_name` varchar(255) NOT NULL DEFAULT '',
  `meta_value` longtext,
  PRIMARY KEY (`report_id`,`meta_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='If crash report received unknown meta name, it will be written in this table';



# Dump of table crash_submit
# ------------------------------------------------------------

DROP TABLE IF EXISTS `crash_submit`;

CREATE TABLE `crash_submit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `package_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `app_version_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `app_version_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `brand` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_model` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stack_trace` longtext COLLATE utf8_unicode_ci,
  `os` enum('Android','iOS','Windows') COLLATE utf8_unicode_ci DEFAULT NULL,
  `android_version` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_path` longtext COLLATE utf8_unicode_ci,
  `total_mem_size` int(11) DEFAULT NULL,
  `available_mem_size` int(11) DEFAULT NULL,
  `user_comment` longtext COLLATE utf8_unicode_ci,
  `user_app_start_date` datetime DEFAULT NULL,
  `user_crash_date` datetime DEFAULT NULL,
  `report_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `installation_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `user_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `country` char(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`),
  KEY `package` (`package_name`),
  KEY `package_version` (`app_version_name`),
  KEY `brand` (`brand`),
  KEY `phone_model` (`phone_model`),
  KEY `product` (`product`),
  KEY `os_version` (`android_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table crash_submit_meta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `crash_submit_meta`;

CREATE TABLE `crash_submit_meta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `submit_id` bigint(20) unsigned NOT NULL,
  `meta_name` varchar(40) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `meta_value` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fast_key` (`submit_id`,`meta_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table email_trigger
# ------------------------------------------------------------

DROP TABLE IF EXISTS `email_trigger`;

CREATE TABLE `email_trigger` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `package` varchar(255) DEFAULT NULL,
  `package_version` varchar(255) DEFAULT NULL,
  `os_version` varchar(255) DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `product` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `to_emails` mediumtext NOT NULL,
  `last_email` datetime DEFAULT NULL,
  `email_delay_minutes` int(10) unsigned NOT NULL DEFAULT '60',
  `state` enum('waiting','sending') NOT NULL DEFAULT 'waiting',
  `created_by` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `package` (`package`),
  KEY `package_version` (`package_version`),
  KEY `os_version` (`os_version`),
  KEY `brand` (`brand`),
  KEY `model` (`model`),
  KEY `product` (`product`),
  KEY `country` (`country`),
  KEY `last_email` (`last_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table package
# ------------------------------------------------------------

DROP TABLE IF EXISTS `package`;

CREATE TABLE `package` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `total` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `package_name` (`name`),
  KEY `total` (`total`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table package_version
# ------------------------------------------------------------

DROP TABLE IF EXISTS `package_version`;

CREATE TABLE `package_version` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` bigint(20) unsigned NOT NULL,
  `value` varchar(255) CHARACTER SET latin1 NOT NULL,
  `total` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `package_id` (`package_id`,`value`),
  KEY `total` (`total`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table phone_model
# ------------------------------------------------------------

DROP TABLE IF EXISTS `phone_model`;

CREATE TABLE `phone_model` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL,
  `name` varchar(80) NOT NULL DEFAULT '',
  `total` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`,`name`),
  KEY `total` (`total`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table product
# ------------------------------------------------------------

DROP TABLE IF EXISTS `product`;

CREATE TABLE `product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `total` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `brand` (`brand_id`,`name`),
  KEY `total` (`total`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table provider
# ------------------------------------------------------------

DROP TABLE IF EXISTS `provider`;

CREATE TABLE `provider` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `total` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `total` (`total`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table stack_trace
# ------------------------------------------------------------

DROP TABLE IF EXISTS `stack_trace`;

CREATE TABLE `stack_trace` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hash` varchar(32) CHARACTER SET latin1 NOT NULL,
  `summary` mediumtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `total` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `total` (`total`),
  KEY `created_at` (`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table status
# ------------------------------------------------------------

DROP TABLE IF EXISTS `status`;

CREATE TABLE `status` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT '',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `pass` char(32) CHARACTER SET latin1 NOT NULL DEFAULT '' COMMENT 'MD5',
  `first_name` varchar(80) DEFAULT NULL,
  `last_name` varchar(160) DEFAULT NULL,
  `account_type` enum('admin','normal') NOT NULL DEFAULT 'normal',
  `timezone` varchar(80) CHARACTER SET latin1 NOT NULL DEFAULT 'Europe/London',
  `last_login` datetime DEFAULT NULL,
  `last_login_ip` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;

INSERT INTO `user` (`id`, `username`, `pass`, `first_name`, `last_name`, `account_type`, `timezone`, `last_login`, `last_login_ip`)
VALUES
	(1,'admin@admin.com','21232f297a57a5a743894a0e4a801fc3','Administrator',NULL,'admin','Europe/London',NULL,NULL);

/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table version
# ------------------------------------------------------------

DROP TABLE IF EXISTS `version`;

CREATE TABLE `version` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `os` enum('Android','iOS','Windows') CHARACTER SET latin1 NOT NULL DEFAULT 'Android',
  `name` varchar(80) CHARACTER SET latin1 NOT NULL,
  `total` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `os` (`os`,`name`),
  KEY `total` (`total`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='OS versions';




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
