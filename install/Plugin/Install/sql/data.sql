INSERT INTO `ip_cms_page`
(`id`,`languageCode`,`urlPath`,`parentId`,`pageOrder`,`title`,`isVisible`,`metaTitle`,`keywords`,`description`,`updatedAt`,             `createdAt`,            `type`,     `alias`) VALUES
(1,   'en',   NULL,	          0,  0,            'Menu1',          1,	      NULL,     NULL,       NULL,	        NULL,                   '2014-02-07 17:13:11',	'default',	'menu1'),
(3,   'en',   '',	            0,  1,	          'Home',	          1,	      NULL,     NULL,	      NULL,	        '2014-02-07 00:00:00',	'2014-02-07 17:13:21',	'default',	'menu2'),
(4,   'en',   NULL,	          0,	2,	          'Menu3',	        1,	      NULL,	    NULL,	      NULL,	        NULL,                   '2014-02-07 17:13:25',	'default',	'menu3'),
(64,  'en',	  'home',	        1,	1,	          'Home',	          1,	      NULL,	    NULL,       NULL,	        '2014-01-27 00:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(65,  'en',	  'lorem-ipsumy', 1,	2,	          'Lorem ipsum',	  1,	      NULL,     NULL,       NULL,	        '2012-01-21 00:00:00',	'2014-02-12 12:02:00',	'default',	NULL),
(66,  'en',	  'page1',	      3,	1,	          'Page1',	        1,	      NULL,	    NULL,       NULL,	        '2014-02-07 00:00:00',	'2014-02-07 16:34:55',	'default',	NULL),
(67,  'en',	  'page2',	      3,	2,	          'Page2',	        1,	      NULL,	    NULL,	      NULL,	        '2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(68,  'en',	  'page3',	      3,	3,	          'Page3',	        1,	      NULL,	    NULL,	      NULL,	        '2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(69,  'en',	  'page4',	      3,	4,            'Page4',	        1,	      NULL,	    NULL,	      NULL,	        '2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(70,  'en',	  'page5',	      3,	5,            'Page5',	        1,	      NULL,	    NULL,	      NULL,	        '2012-01-21 22:00:00',	'2014-02-12 12:02:18',	'default',	NULL),
(72,  'en',	  'examplepage',  4,	1,	          'Example page',	  1,	      NULL,	    NULL,	      NULL,	        '2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(73,  'en',	  'examplepage2', 4,	2,	          'Example page2',  1,	      NULL,	    NULL,	      NULL,	        '2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(71,  'en',	  'page2/subpage',67,	1,	    'Subpage',	      1,	            NULL,     NULL,	      NULL,	        '2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL);



INSERT INTO `ip_cms_pageStorage` (`pageId`, `key`, `value`) VALUES
(64, 'layout', '"home.php"');


INSERT INTO `ip_cms_widgetOrder` (`widgetName`, `priority`) VALUES
('Title',   10),
('Text',    20),
('Divider', 30),
('Image',   40),
('Gallery', 50),
('File',    60),
('Html',    80),
('Video',   90),
('Map',    100);



INSERT INTO `ip_cms_language` (`abbreviation`, `title`, `languageOrder`, `isVisible`, `url`, `code`) VALUES
('EN', 'English', 2, 1, 'en', 'en');



INSERT INTO `ip_cms_storage` (`plugin`, `key`, `value`) VALUES
('Ip', 'version', '"4.0"'),
('Ip', 'theme', '"Air"'),
('Ip', 'cachedBaseUrl', ''),
('Ip', 'lastSystemMessageSent', ''),
('Ip', 'lastSystemMessageShown', ''),
('Ip', 'themeChanged', '0'),
('Ip', 'cacheVersion', '1');


INSERT INTO `ip_cms_plugin` (`title`, `name`, `version`, `isActive`) VALUES
('Application', 'Application', 1.00, 1);
