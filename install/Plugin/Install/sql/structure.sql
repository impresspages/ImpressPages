
ALTER DATABASE  `[[[[database]]]]` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;



DROP TABLE IF EXISTS `ip_cms_page`;

CREATE TABLE IF NOT EXISTS `ip_cms_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `languageCode` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `pageOrder` double NOT NULL DEFAULT '0',
  `parentId` int(11) DEFAULT NULL,
  `navigationTitle` varchar(255) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `pageTitle` mediumtext,
  `keywords` mediumtext,
  `description` mediumtext,
  `lastModified` timestamp NULL DEFAULT NULL,
  `createdOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` varchar(255) NOT NULL DEFAULT 'default',
  `alias` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_cms_pageStorage`;

CREATE TABLE IF NOT EXISTS `ip_cms_pageStorage` (
  `pageId` int(10) unsigned NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `pageKey` (`pageId`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_cms_language`;

CREATE TABLE IF NOT EXISTS `ip_cms_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `d_short` varchar(255) NOT NULL DEFAULT '',
  `d_long` varchar(255) NOT NULL DEFAULT '',
  `row_number` double NOT NULL DEFAULT '0',
  `visible` int(1) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL DEFAULT '',
  `code` varchar(255) NOT NULL,
  `text_direction` varchar(10) NOT NULL DEFAULT 'ltr',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_cms_log`;

CREATE TABLE IF NOT EXISTS `ip_cms_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `level` varchar(255) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `context` mediumtext,
  PRIMARY KEY (`id`),
  KEY `time` (`time`),
  KEY `message` (`message`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;



DROP TABLE IF EXISTS `ip_cms_module`;

CREATE TABLE IF NOT EXISTS `ip_cms_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `row_number` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `translation` varchar(255) NOT NULL,
  `managed` tinyint(1) NOT NULL DEFAULT '1',
  `version` decimal(10,2) NOT NULL,
  `core` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;



DROP TABLE IF EXISTS `ip_cms_email_queue`;

CREATE TABLE IF NOT EXISTS `ip_cms_email_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` mediumtext NOT NULL,
  `to` varchar(255) NOT NULL,
  `to_name` varchar(255) DEFAULT NULL,
  `from` varchar(255) NOT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `immediate` tinyint(1) NOT NULL DEFAULT '0',
  `html` tinyint(1) NOT NULL,
  `send` timestamp NULL DEFAULT NULL,
  `lock` varchar(32) DEFAULT NULL,
  `locked_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `files` mediumtext,
  `file_names` mediumtext,
  `file_mime_types` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;



DROP TABLE IF EXISTS `ip_cms_repository_file`;

CREATE TABLE IF NOT EXISTS `ip_cms_repository_file` (
  `fileId` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL COMMENT 'Module group and module key which uses file resource. Eg. standard/content_management',
  `instanceId` int(11) NOT NULL COMMENT 'Unique identificator. Tells in which part of the module the file is used. Teoretically there could be two identical records. The same module binds the same file to the same instance. For example: gallery widget adds the same photo twice.',
  `date` int(11) NOT NULL COMMENT 'Date, when this module started to use this resource.',
  PRIMARY KEY (`fileId`),
  KEY `filename` (`filename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='File usage table. Track which modules uses which files';



DROP TABLE IF EXISTS `ip_cms_repository_reflection`;

CREATE TABLE IF NOT EXISTS `ip_cms_repository_reflection` (
  `reflectionId` int(11) NOT NULL AUTO_INCREMENT,
  `transformFingerprint` char(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL COMMENT 'unique cropping options key',
  `original` varchar(255) NOT NULL,
  `reflection` varchar(255) NOT NULL COMMENT 'Cropped version of original file.',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`reflectionId`),
  KEY `transformFingerprint` (`transformFingerprint`,`original`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Cropped versions of original image file';



DROP TABLE IF EXISTS `ip_cms_widget`;

CREATE TABLE IF NOT EXISTS `ip_cms_widget` (
  `widgetId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '0',
  `layout` varchar(25) NOT NULL,
  `data` text NOT NULL,
  `created` int(11) NOT NULL,
  `recreated` int(11) DEFAULT NULL COMMENT 'when last time the images were cropped freshly :)',
  PRIMARY KEY (`widgetId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_cms_widget_instance`;

CREATE TABLE IF NOT EXISTS `ip_cms_widget_instance` (
  `instanceId` int(11) NOT NULL AUTO_INCREMENT,
  `revisionId` int(11) NOT NULL,
  `widgetId` int(11) NOT NULL,
  `position` double NOT NULL,
  `blockName` varchar(25) NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `created` int(11) NOT NULL COMMENT 'unix timestamp',
  `deleted` int(11) DEFAULT NULL COMMENT 'unix timestamp',
  PRIMARY KEY (`instanceId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_cms_themeStorage`;

CREATE TABLE IF NOT EXISTS `ip_cms_themeStorage` (
  `theme` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `themeKey` (`theme`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_cms_widgetOrder`;

CREATE TABLE IF NOT EXISTS `ip_cms_widgetOrder` (
  `widgetName` varchar(255) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `widgetName` (`widgetName`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_cms_inlineValueGlobal`;

CREATE TABLE IF NOT EXISTS `ip_cms_inlineValueGlobal` (
  `plugin` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`plugin`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_cms_inlineValueForLanguage`;

CREATE TABLE IF NOT EXISTS `ip_cms_inlineValueForLanguage` (
  `plugin` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `languageId` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`plugin`,`key`,`languageId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_cms_inlineValueForPage`;

CREATE TABLE IF NOT EXISTS `ip_cms_inlineValueForPage` (
  `module` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `languageId` int(11) NOT NULL,
  `pageId` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`module`,`key`,`languageId`,`pageId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_cms_plugin`;

CREATE TABLE IF NOT EXISTS `ip_cms_plugin` (
  `title` varchar(100) NOT NULL,
  `name` varchar(30) NOT NULL,
  `version` decimal(10,2) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_cms_revision`;

CREATE TABLE IF NOT EXISTS `ip_cms_revision` (
  `revisionId` int(11) NOT NULL AUTO_INCREMENT,
  `pageId` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`revisionId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_cms_storage`;

CREATE TABLE IF NOT EXISTS `ip_cms_storage` (
    `plugin` varchar(40) NOT NULL,
    `key` varchar(100) NOT NULL,
    `value` text NOT NULL,
    UNIQUE KEY `pluginkey` (`plugin`,`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ip_cms_administrator`;

CREATE TABLE IF NOT EXISTS `ip_cms_administrator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL DEFAULT '',
  `hash` text NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `resetSecret` varchar(32) DEFAULT NULL,
  `resetTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

