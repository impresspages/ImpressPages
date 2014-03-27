
ALTER DATABASE  `[[[[database]]]]` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;



DROP TABLE IF EXISTS `ip_page`;

CREATE TABLE `ip_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `languageCode` varchar(6) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `urlPath` varchar(140) DEFAULT NULL,
  `parentId` int(11) DEFAULT NULL,
  `pageOrder` double NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `metaTitle` mediumtext,
  `keywords` mediumtext,
  `description` mediumtext,
  `type` varchar(255) NOT NULL DEFAULT 'default',
  `alias` varchar(255) DEFAULT NULL,
  `redirectUrl` varchar(255) DEFAULT NULL,
  `isVisible` tinyint(1) NOT NULL DEFAULT '0',
  `isDisabled` tinyint(1) NOT NULL DEFAULT '0',
  `isSecured` tinyint(1) NOT NULL DEFAULT '0',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `isBlank` BOOLEAN NOT NULL DEFAULT FALSE COMMENT  'Open page in new window',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedAt` timestamp NULL DEFAULT NULL,
  `deletedAt` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `url` (`urlPath`, `languageCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_page_storage`;

CREATE TABLE `ip_page_storage` (
  `pageId` int(10) unsigned NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `pageKey` (`pageId`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ip_permission`;

CREATE TABLE `ip_permission` (
  `administratorId` int(11) NOT NULL DEFAULT '0',
  `permission` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`administratorId`,`permission`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ip_language`;

CREATE TABLE `ip_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `abbreviation` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `languageOrder` double NOT NULL DEFAULT '0',
  `isVisible` int(1) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL DEFAULT '',
  `code` varchar(255) NOT NULL,
  `textDirection` varchar(10) NOT NULL DEFAULT 'ltr',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_log`;

CREATE TABLE `ip_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `level` varchar(255) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `context` mediumtext,
  PRIMARY KEY (`id`),
  KEY `time` (`time`),
  KEY `message` (`message`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;



DROP TABLE IF EXISTS `ip_email_queue`;

CREATE TABLE `ip_email_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` mediumtext NOT NULL,
  `to` varchar(255) NOT NULL,
  `toName` varchar(255) DEFAULT NULL,
  `from` varchar(255) NOT NULL,
  `fromName` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `immediate` tinyint(1) NOT NULL DEFAULT '0',
  `html` tinyint(1) NOT NULL,
  `send` timestamp NULL DEFAULT NULL,
  `lock` varchar(32) DEFAULT NULL,
  `lockedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `files` mediumtext,
  `fileNames` mediumtext,
  `fileMimeTypes` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;



DROP TABLE IF EXISTS `ip_repository_file`;

CREATE TABLE `ip_repository_file` (
  `fileId` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `plugin` varchar(255) NOT NULL,
  `instanceId` int(11) NOT NULL COMMENT 'Unique identificator. Tells in which part of the module the file is used. Teoretically there could be two identical records. The same module binds the same file to the same instance. For example: gallery widget adds the same photo twice.',
  `createdAt` int(11) NOT NULL COMMENT 'Time, when this module started to use this resource.',
  PRIMARY KEY (`fileId`),
  KEY `filename` (`filename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='File usage table. Track which modules uses which files';



DROP TABLE IF EXISTS `ip_repository_reflection`;

CREATE TABLE `ip_repository_reflection` (
  `reflectionId` int(11) NOT NULL AUTO_INCREMENT,
  `transformFingerprint` char(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL COMMENT 'unique cropping options key',
  `original` varchar(255) NOT NULL,
  `reflection` varchar(255) NOT NULL COMMENT 'Cropped version of original file.',
  `createdAt` int(11) NOT NULL,
  PRIMARY KEY (`reflectionId`),
  KEY `transformFingerprint` (`transformFingerprint`,`original`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Cropped versions of original image file';

DROP TABLE IF EXISTS `ip_widget`;

CREATE TABLE IF NOT EXISTS `ip_widget` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `skin` varchar(25) NOT NULL,
  `data` text NOT NULL,
  `revisionId` int(11) NOT NULL,
  `languageId` int(11) NOT NULL,
  `blockName` varchar(25) NOT NULL,
  `position` double NOT NULL,
  `isVisible` tinyint(1) NOT NULL DEFAULT '1',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `createdAt` int(11) NOT NULL COMMENT 'unix timestamp',
  `updatedAt` int(11) NOT NULL,
  `deletedAt` int(11) DEFAULT NULL COMMENT 'unix timestamp',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ip_theme_storage`;

CREATE TABLE `ip_theme_storage` (
  `theme` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  UNIQUE KEY `themeKey` (`theme`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_widget_order`;

CREATE TABLE `ip_widget_order` (
  `widgetName` varchar(255) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `widgetName` (`widgetName`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_inline_value_global`;

CREATE TABLE `ip_inline_value_global` (
  `plugin` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`plugin`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_inline_value_language`;

CREATE TABLE `ip_inline_value_language` (
  `plugin` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `languageId` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`plugin`,`key`,`languageId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_inline_value_page`;

CREATE TABLE `ip_inline_value_page` (
  `plugin` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `pageId` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`plugin`,`key`,`pageId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_plugin`;

CREATE TABLE `ip_plugin` (
  `title` varchar(100) NOT NULL,
  `name` varchar(30) NOT NULL,
  `version` decimal(10,2) NOT NULL,
  `isActive` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_revision`;

CREATE TABLE `ip_revision` (
  `revisionId` int(11) NOT NULL AUTO_INCREMENT,
  `pageId` int(11) NOT NULL DEFAULT '0',
  `isPublished` tinyint(1) NOT NULL DEFAULT '0',
  `createdAt` int(11) NOT NULL,
  PRIMARY KEY (`revisionId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_storage`;

CREATE TABLE `ip_storage` (
    `plugin` varchar(40) NOT NULL,
    `key` varchar(100) NOT NULL,
    `value` text NOT NULL,
    UNIQUE KEY `pluginkey` (`plugin`,`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_administrator`;

CREATE TABLE `ip_administrator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL DEFAULT '',
  `hash` text NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `resetSecret` varchar(32) DEFAULT NULL,
  `resetTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

