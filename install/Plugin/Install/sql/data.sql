


INSERT INTO `ip_cms_page` (`id`, `row_number`, `parent`, `button_title`, `visible`, `page_title`, `keywords`, `description`, `url`, `dynamic_modules`, `last_modified`, `created_on`, `cached_html`, `cached_text`, `type`, `redirect_url`) VALUES
(1, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2009-07-19 07:24:20', NULL, NULL, 'default', NULL),
(3, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2009-07-19 07:24:32', NULL, NULL, 'default', NULL),
(4, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2009-07-19 07:24:32', NULL, NULL, 'default', NULL),
(66, 0, 3, 'Page1', 1, 'Page1', '', '', 'page1', '', '2012-01-21 22:00:00', '2009-08-08 21:00:00', '<div id="ipBlock-main" class="ipBlock ipbEmpty">\n</div>\n', ' ', 'default', ''),
(67, 1, 3, 'Page2', 2, 'Page2', '', '', 'page2', '', '2012-01-21 22:00:00', '2009-08-08 21:00:00', '<div id="ipBlock-main" class="ipBlock ipbEmpty">\n</div>\n', ' ', 'default', ''),
(68, 2, 3, 'Page3', 3, 'Page3', '', '', 'page3', '', '2012-01-21 22:00:00', '2009-08-08 21:00:00', '<div id="ipBlock-main" class="ipBlock ipbEmpty">\n</div>\n', ' ', 'default', ''),
(69, 3, 3, 'Page4', 4, 'Page4', '', '', 'page4', '', '2012-01-21 22:00:00', '2009-08-08 21:00:00', '<div id="ipBlock-main" class="ipBlock ipbEmpty">\n</div>\n', ' ', 'default', ''),
(70, 4, 3, 'Page5', 5, 'Page5', '', '', 'page5', '', '2012-01-21 22:00:00', '2009-08-08 21:00:00', '<div id="ipBlock-main" class="ipBlock ipbEmpty">\n</div>\n', ' ', 'default', ''),
(71, 0, 67, 'Subpage', 1, 'Subpage example', '', '', 'subpage', 'a:0:{}', '2012-01-21 22:00:00', '2009-08-08 21:00:00', '<div id="ipBlock-main" class="ipBlock ipbEmpty">\n</div>\n', ' ', 'default', ''),
(64, 0, 1, 'Home', 1, 'Home', '', '', 'home', '', '2012-01-21 22:00:00', '2009-08-08 21:00:00', '<div id="ipBlock-main" class="ipBlock">\n<div  class="ipWidget ipWidget-Title ipLayout-default">\n<h1 class="ipwTitle">This is the main content area</h1>\n</div>\n<div  class="ipWidget ipWidget-Text ipLayout-default">\n<p>Drag any widget here and play with it. You can drag same widget to the sidebar too.</p></div>\n</div>\n', '  \n\nTHIS IS MAIN CONTENT AREA\n\n	Drag any widget here and play with it. You can drag same widget to\nthe sidebar too. ', 'default', ''),
(65, 1, 1, 'Lorem ipsum', 1, 'Lorem ipsum', '', '', 'lorem-ipsum', '', '2012-01-21 22:00:00', '2009-08-08 21:00:00', '<div id="ipBlock-main" class="ipBlock ipbEmpty">\n</div>\n', ' ', 'default', ''),
(72, 0, 4, 'Example page', 1, 'Example page', '', '', 'examplepage', '', '2012-01-21 22:00:00', '2009-08-08 21:00:00', '<div id="ipBlock-main" class="ipBlock ipbEmpty">\n</div>\n', ' ', 'default', ''),
(73, 0, 4, 'Example page2', 2, 'Example page', '', '', 'examplepage2', '', '2012-01-21 22:00:00', '2009-08-08 21:00:00', '<div id="ipBlock-main" class="ipBlock ipbEmpty">\n</div>\n', ' ', 'default', '');

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
(442, 336, 0, 'inline_management', 1, 'Inline Management', 0, '1.00', 1),
(443, 323, 0, 'wizard', 1, 'Wizard', 0, '1.00', 1);

-- Dumping data for table--

INSERT INTO `ip_cms_page_layout` (`module_name`, `page_id`, `layout`) VALUES
('Content', 64, 'home.php');

-- Dumping data for table--

INSERT INTO `ip_cms_storage` (`plugin`, `key`, `value`) VALUES
('Ip', 'version', '"4.0"'),
('Ip', 'theme', '"Blank"'),
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

INSERT INTO `ip_cms_zone` (`id`, `row_number`, `name`, `template`, `translation`, `associated_module`) VALUES
(105, 4, 'menu1', 'main.php', 'Menu1', 'Content'),
(106, 5, 'menu2', 'main.php', 'Menu2', 'Content'),
(110, 6, 'menu3', 'main.php', 'Menu3', 'Content');

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



