
INSERT INTO `ip_cms_page` (`id`, `url`, `languageCode`, `slug`, `pageOrder`, `parentId`, `navigationTitle`, `visible`, `pageTitle`, `keywords`, `description`, `lastModified`, `createdOn`, `type`, `alias`) VALUES
(1,	NULL,	'en',	NULL,	0,	0,	'Menu1',	1,	'Menu1',	NULL,	NULL,	NULL,	'2014-02-07 17:13:11',	'default',	'menu1'),
(3,	'',	'en',	'',	0,	0,	'Home',	1,	'Home',	NULL,	NULL,	'2014-02-07 00:00:00',	'2014-02-07 17:13:21',	'default',	'menu2'),
(4,	NULL,	'en',	NULL,	0,	0,	'Menu3',	1,	'Menu3',	NULL,	NULL,	NULL,	'2014-02-07 17:13:25',	'default',	'menu3'),
(66,	'page1',	'en',	'page1',	0,	3,	'Page1',	1,	'Page1',	'',	'',	'2014-02-07 00:00:00',	'2014-02-07 16:34:55',	'default',	NULL),
(67,	'page2',	'en',	'page2',	1,	3,	'Page2',	2,	'Page2',	'',	'',	'2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(68,	'page3',	'en',	'page3',	2,	3,	'Page3',	3,	'Page3',	'',	'',	'2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(69,	'page4',	'en',	'page4',	3,	3,	'Page4',	4,	'Page4',	'',	'',	'2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(70,	'page5',	'en',	'page5',	3.5,	3,	'Page5',	5,	'Page5',	'',	'',	'2012-01-21 22:00:00',	'2014-02-12 12:02:18',	'default',	NULL),
(71,	'subpage',	'en',	'page2/subpage',	0,	67,	'Subpage',	1,	'Subpage example',	'',	'',	'2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(64,	'home',	'en',	'home',	0,	1,	'Home',	1,	'Home',	'',	'',	'2014-01-27 00:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(65,	'lorem-ipsumy',	'en',	'lorem-ipsumy',	1,	1,	'Lorem ipsum',	1,	'Lorem ipsum',	'',	'',	'2012-01-21 00:00:00',	'2014-02-12 12:02:00',	'default',	NULL),
(72,	'examplepage',	'en',	'examplepage',	0,	4,	'Example page',	1,	'Example page',	'',	'',	'2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL),
(73,	'examplepage2',	'en',	'examplepage2',	0,	4,	'Example page2',	2,	'Example page',	'',	'',	'2012-01-21 22:00:00',	'2009-08-08 22:00:00',	'default',	NULL);

INSERT INTO `ip_cms_pageStorage` (`pageId`, `key`, `value`) VALUES
(64, 'layout', 'home.php');

-- Dumping data for table--

INSERT INTO `ip_cms_m_developer_widget_sort` (`sortId`, `widgetName`, `priority`, `deleted`) VALUES
(2, 'Image', 60, 0),
(3, 'Gallery', 70, 0),
(5, 'Text', 30, 0),
(7, 'Title', 20, 0),
(8, 'File', 90, 0),
(10, 'Separator', 40, 0),
(9, 'Html', 120, 0),
(14, 'Faq', 140, 0);

-- Dumping data for table--

INSERT INTO `ip_cms_language` (`id`, `d_short`, `d_long`, `row_number`, `visible`, `url`, `code`) VALUES
(344, 'EN', 'English', 2, 1, 'en', 'en');

-- Dumping data for table--

INSERT INTO `ip_cms_module` (`id`, `group_id`, `row_number`, `name`, `admin`, `translation`, `managed`, `version`, `core`) VALUES
(323, 336, 7, 'std_mod', 1, 'Std. mod.', 0, '1.00', 1),
(352, 336, 4, 'modules_configuration', 1, 'Modules config', 1, '1.00', 1),
(326, 323, 0, 'administrators', 1, 'Administrators', 1, '1.00', 1),
(327, 324, 1, 'content_management', 1, 'Content management', 1, '1.00', 1),
(328, 336, 1, 'zones', 1, 'Zones', 1, '1.00', 1),
(329, 324, 6, 'languages', 1, 'Languages', 1, '1.00', 1),
(330, 336, 5, 'widgets', 1, 'Widgets', 1, '1.00', 1),
(332, 324, 5, 'configuration', 0, 'Configuration', 1, '1.00', 1),
(333, 324, 4, 'seo', 1, 'SEO', 1, '1.00', 1),
(348, 323, 3, 'log', 1, 'Log', 1, '1.00', 1),
(353, 323, 0, 'email_queue', 1, 'E-mail queue', 1, '1.00', 1),
(356, 323, 0, 'search', 0, 'Search', 0, '1.00', 1),
(358, 323, 0, 'sitemap', 1, 'Sitemap', 0, '1.00', 1),
(361, 336, 8, 'config_exp_imp', 1, 'Modules exp/imp', 1, '1.00', 1),
(436, 336, 9, 'localization', 1, 'Localization', 1, '1.00', 1),
(424, 323, 4, 'system', 1, 'System', 1, '1.00', 1),
(435, 324, 7, 'breadcrumb', 1, 'Breadcrumb', 0, '1.00', 1),
(437, 337, 1, 'user', 1, 'User', 1, '1.00', 1),
(438, 336, 11, 'upload', 0, 'Upload', 0, '1.00', 1),
(439, 323, 12, 'repository', 1, 'Repository', 0, '1.00', 1),
(441, 336, 14, 'form', 0, 'Form', 0, '1.00', 1),
(442, 336, 0, 'inline_management', 1, 'Inline Management', 0, '1.00', 1);

-- Dumping data for table--

INSERT INTO `ip_cms_storage` (`plugin`, `key`, `value`) VALUES
('Ip', 'version', '"4.0"'),
('Ip', 'theme', '"Air"'),
('Ip', 'cachedBaseUrl', ''),
('Ip', 'lastSystemMessageSent', ''),
('Ip', 'lastSystemMessageShown', ''),
('Ip', 'themeChanged', '0'),
('Ip', 'cacheVersion', '1');


-- Dumping data for table--

INSERT INTO `ip_cms_user_to_mod` (`id`, `userId`, `module_id`) VALUES
(530, 8, 436),
(525, 8, 361),
(523, 8, 330),
(522, 8, 352),
(520, 8, 328),
(519, 8, 424),
(518, 8, 348),
(517, 8, 353),
(516, 8, 326),
(529, 8, 435),
(512, 8, 329),
(511, 8, 332),
(510, 8, 333),
(508, 8, 327),
(531, 8, 437),
(532, 8, 439),
(534, 8, 441),
(535, 8, 442),
(536, 8, 443);

-- Dumping data for table--

INSERT INTO `ip_cms_zone_to_language` (`id`, `description`, `keywords`, `title`, `url`, `zone_id`, `language_id`) VALUES
(620, '', '', 'Menu2', 'menu2', 106, 384),
(619, '', '', 'Menu2', 'menu2', 106, 344),
(618, '', '', 'Menu1', 'menu1', 105, 384),
(617, '', '', 'Menu1', 'menu1', 105, 344),
(629, '', '', 'Menu3', 'menu3', 110, 344),
(630, '', '', 'Menu3', 'menu3', 110, 384);

-- Dumping data for table--


INSERT INTO `ip_cms_zone_to_page` (`id`, `language_id`, `zone_id`, `element_id`) VALUES
(163, 344, 105, 1),
(165, 344, 106, 3),
(166, 344, 110, 4);



