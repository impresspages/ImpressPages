
INSERT INTO `ip_language` (`abbreviation`, `title`, `languageOrder`, `isVisible`, `url`, `code`) VALUES
('EN', 'English', 2, 1, '', 'en');

INSERT INTO `ip_page`
(`id`, `languageCode`, `urlPath`,       `parentId`, `pageOrder`,  `title`,          `isVisible`, `updatedAt`,      `createdAt`,            `type`,     `alias`) VALUES
(1,    'en',           NULL,            0,          0,            'Menu1',          1,           '[[[[time]]]]',   '[[[[time]]]]',  'default',  'menu1'),
(2,    'en',           NULL,            0,          1,            'Menu2',          1,           '[[[[time]]]]',   '[[[[time]]]]',  'default',  'menu2'),
(3,    'en',           NULL,            0,          2,            'Menu3',          1,           '[[[[time]]]]',   '[[[[time]]]]',  'default',  'menu3'),
(4,    'en',           'home',          1,          0,            'Home',           1,           '[[[[time]]]]',   '[[[[time]]]]',  'default',  NULL),
(5,    'en',           'lorem-ipsumy',  1,          1,            'Lorem ipsum',    1,           '[[[[time]]]]',   '[[[[time]]]]',  'default',  NULL),
(6,    'en',           'page1',         2,          0,            'Page1',          1,           '[[[[time]]]]',   '[[[[time]]]]',  'default',  NULL),
(7,    'en',           'page2',         2,          1,            'Page2',          1,           '[[[[time]]]]',   '[[[[time]]]]',  'default',  NULL),
(8,    'en',           'example1',      3,          0,            'Example 1',      1,           '[[[[time]]]]',   '[[[[time]]]]',  'default',  NULL),
(9,    'en',           'example2',      3,          1,            'Example 2',      1,           '[[[[time]]]]',   '[[[[time]]]]',  'default',  NULL);

INSERT INTO `ip_pageStorage` (`pageId`, `key`, `value`) VALUES
(4, 'layout', '"home.php"');

INSERT INTO `ip_revision` (`revisionId`, `pageId`, `isPublished`, `createdAt`) VALUES
(1, 4, 1, '[[[[time]]]]'),
(2, 5, 1, '[[[[time]]]]');


INSERT INTO `ip_widget` (`id`, `revisionId`, `position`, `languageId`, `blockName`, `isVisible`, `isDeleted`, `name`, `skin`, `data`, `createdAt`, `updatedAt`) VALUES
(1, 1, 50, 0, 'main', 1, 0, 'Heading', 'default', '{"title":"Home page","level":"1"}', '[[[[time]]]]', '[[[[time]]]]'),
(2, 1, 90, 0, 'main', 1, 0, 'Text', 'default', '{"text":"<p><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec accumsan, tellus posuere sodales rhoncus, nulla nulla dignissim leo, ac consectetur elit mauris quis leo. Phasellus odio orci, ultricies sit amet tristique ac, varius at nisi. Vivamus eros massa, aliquet at sem ut, placerat interdum elit. Vivamus nisi augue, auctor eget malesuada vel, pulvinar eget orci. Quisque sit amet posuere augue. Aenean sodales augue non aliquam molestie. Nunc feugiat aliquam orci a aliquet. Aenean fermentum enim a luctus posuere. Mauris elementum facilisis urna, a adipiscing tortor congue vel. Nullam facilisis ultrices quam. Donec nunc orci, ullamcorper hendrerit nunc a, fringilla rhoncus est. Suspendisse laoreet posuere sapien.<\\/span>\\u00a0<\\/p>"}', '[[[[time]]]]', '[[[[time]]]]'),
(3, 2, 50, 0, 'main', 1, 0, 'Heading', 'default', '{"title":"Page content","level":"1"}', '[[[[time]]]]', '[[[[time]]]]'),
(4, 2, 90, 0, 'main', 1, 0, 'Text', 'default', '{"text":"<p><span>Aliquam erat volutpat. Donec rutrum venenatis dignissim. Duis eu neque in neque venenatis tincidunt sit amet sed velit. Pellentesque quis luctus orci. Phasellus sed aliquam risus, eu varius sapien. Etiam ac adipiscing enim, eu molestie erat. Donec sodales pulvinar lorem, ut sagittis purus suscipit nec. In id velit nec nisi porta egestas. Maecenas rutrum felis vel nunc varius, vel ornare lectus dignissim. Maecenas vitae ante dui. Maecenas sollicitudin dolor at enim porttitor, eu placerat nulla adipiscing. Morbi sed varius nisi, sed posuere risus. Ut velit urna, dignissim ac lobortis id, sollicitudin non libero. Donec arcu massa, facilisis ut sapien et, aliquet porta dui.<\\/span>\\u00a0<\\/p>"}', '[[[[time]]]]', '[[[[time]]]]');



INSERT INTO `ip_widgetOrder` (`widgetName`, `priority`) VALUES
('Heading',   10),
('Text',    20),
('Divider', 30),
('Image',   40),
('Gallery', 50),
('File',    60),
('Html',    80),
('Video',   90),
('Map',    100);


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
('Application', 'Application', 1.00, 1),
('Colorbox', 'Colorbox', 1.00, 1);
