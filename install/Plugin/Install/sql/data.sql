
INSERT INTO `ip_page`
(`id`, `languageCode`, `urlPath`,       `parentId`, `pageOrder`,  `title`,          `isVisible`, `metaTitle`, `keywords`, `description`, `updatedAt`,             `createdAt`,            `type`,     `alias`) VALUES
(1,    'en',           NULL,            0,          0,            'Menu1',          1,           NULL,        NULL,       NULL,          NULL,                    '2014-02-07 17:13:11',  'default',  'menu1'),
(2,    'en',           NULL,            0,          1,            'Menu2',          1,           NULL,        NULL,       NULL,          '2014-02-07 00:00:00',   '2014-02-07 17:13:21',  'default',  'menu2'),
(3,    'en',           NULL,            0,          2,            'Menu3',          1,           NULL,        NULL,       NULL,          NULL,                    '2014-02-07 17:13:25',  'default',  'menu3'),
(4,    'en',           'home',          1,          0,            'Home',           1,           NULL,        NULL,       NULL,          '2014-01-27 00:00:00',   '2009-08-08 22:00:00',  'default',  NULL),
(5,    'en',           'lorem-ipsumy',  1,          1,            'Lorem ipsum',    1,           NULL,        NULL,       NULL,          '2012-01-21 00:00:00',   '2014-02-12 12:02:00',  'default',  NULL),
(6,    'en',           'page1',         2,          0,            'Page1',          1,           NULL,        NULL,       NULL,          '2014-02-07 00:00:00',   '2014-02-07 16:34:55',  'default',  NULL),
(7,    'en',           'page2',         2,          1,            'Page2',          1,           NULL,        NULL,       NULL,          '2012-01-21 22:00:00',   '2009-08-08 22:00:00',  'default',  NULL),
(8,    'en',           'example1',      3,          0,            'Example 1',      1,           NULL,        NULL,       NULL,          '2012-01-21 22:00:00',   '2009-08-08 22:00:00',  'default',  NULL),
(9,    'en',           'example2',      3,          1,            'Example 2',      1,           NULL,        NULL,       NULL,          '2012-01-21 22:00:00',   '2009-08-08 22:00:00',  'default',  NULL);



INSERT INTO `ip_pageStorage` (`pageId`, `key`, `value`) VALUES
(4, 'layout', '"home.php"');



INSERT INTO `ip_widgetOrder` (`widgetName`, `priority`) VALUES
('Title',   10),
('Text',    20),
('Divider', 30),
('Image',   40),
('Gallery', 50),
('File',    60),
('Html',    80),
('Video',   90),
('Map',    100);



INSERT INTO `ip_language` (`abbreviation`, `title`, `languageOrder`, `isVisible`, `url`, `code`) VALUES
('EN', 'English', 2, 1, 'en', 'en');



INSERT INTO `ip_storage` (`plugin`, `key`, `value`) VALUES
('Ip', 'version', '"[[[[version]]]]"'),
('Ip', 'dbVersion', '[[[[dbversion]]]]'),
('Ip', 'theme', '"Air"'),
('Ip', 'cachedBaseUrl', ''),
('Ip', 'lastSystemMessageSent', ''),
('Ip', 'lastSystemMessageShown', ''),
('Ip', 'themeChanged', '0'),
('Ip', 'cacheVersion', '1');


INSERT INTO `ip_plugin` (`title`, `name`, `version`, `isActive`) VALUES
('Application', 'Application', 1.00, 1);
