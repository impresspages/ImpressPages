INSERT INTO `ip_cms_page` (`id`, `url`, `languageCode`, `slug`, `pageOrder`, `parentId`, `navigationTitle`, `isVisible`, `pageTitle`, `keywords`, `description`, `updatedAt`, `createdAt`, `type`, `alias`) VALUES
(1,	NULL,	'en',	NULL,	0,	0,	'Menu1',	1,	'Menu1',	NULL,	NULL,	NULL,	'2014-02-07 17:13:11',	'default',	'menu1'),
(3,	'',	'en',	'',	0,	0,	'Home',	1,	'Home',	NULL,	NULL,	'2014-02-07 00:00:00',	'2014-02-07 17:13:21',	'default',	'menu2'),
(4,	NULL,	'en',	NULL,	0,	0,	'Menu3',	1,	'Menu3',	NULL,	NULL,	NULL,	'2014-02-07 17:13:25',	'default',	'menu3'),
(66,	'page1',	'en',	'page1',	0,	3,	'Page1',	1,	'Page1',	'',	'',	'2014-02-07 00:00:00',	'2014-02-07 16:34:55',	'default',	NULL),
(67,	'page2',	'en',	'page2',	1,	3,	'Page2',	1,	'Page2',	'',	'',	'2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(68,	'page3',	'en',	'page3',	2,	3,	'Page3',	1,	'Page3',	'',	'',	'2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(69,	'page4',	'en',	'page4',	3,	3,	'Page4',	1,	'Page4',	'',	'',	'2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(70,	'page5',	'en',	'page5',	3.5,	3,	'Page5',	1,	'Page5',	'',	'',	'2012-01-21 22:00:00',	'2014-02-12 12:02:18',	'default',	NULL),
(71,	'subpage',	'en',	'page2/subpage',	0,	67,	'Subpage',	1,	'Subpage example',	'',	'',	'2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(64,	'home',	'en',	'home',	0,	1,	'Home',	1,	'Home',	'',	'',	'2014-01-27 00:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(65,	'lorem-ipsumy',	'en',	'lorem-ipsumy',	1,	1,	'Lorem ipsum',	1,	'Lorem ipsum',	'',	'',	'2012-01-21 00:00:00',	'2014-02-12 12:02:00',	'default',	NULL),
(72,	'examplepage',	'en',	'examplepage',	0,	4,	'Example page',	1,	'Example page',	'',	'',	'2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(73,	'examplepage2',	'en',	'examplepage2',	0,	4,	'Example page2',	1,	'Example page',	'',	'',	'2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL);



INSERT INTO `ip_cms_pageStorage` (`pageId`, `key`, `value`) VALUES
(64, 'layout', '"home.php"');


INSERT INTO `ip_cms_widgetOrder` (`widgetName`, `priority`) VALUES
('Title',   10),
('Text',    20),
('Divider', 30),
('Image',   40),
('Gallery', 50),
('File',    60),
('Html',    70),
('Faq',     80);



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
