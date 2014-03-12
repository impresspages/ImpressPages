
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
('Ip', 'version', '"4.0.0"'),
('Ip', 'dbVersion', '2'),
('Ip', 'theme', '"Air"'),
('Ip', 'cachedBaseUrl', ''),
('Ip', 'lastSystemMessageSent', ''),
('Ip', 'lastSystemMessageShown', ''),
('Ip', 'themeChanged', '0'),
('Ip', 'cacheVersion', '1');


INSERT INTO `ip_plugin` (`title`, `name`, `version`, `isActive`) VALUES
('Application', 'Application', 1.00, 1);
