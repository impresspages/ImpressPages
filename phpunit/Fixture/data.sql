-- UPDATE `ip_cms_user` SET `name` = ?, `pass` = MD5(?) limit 1;
INSERT INTO `ip_cms_storage` (`plugin`, `key`, `value`) VALUES
('Config',	'Config.websiteTitle',	'"TestSite"'),
('Config',	'Config.multilingual',	'1'),
('Config',	'Config.websiteEmail',	'"test@test.com"'),
('Config',	'Config.automaticCron',	'1'),
('Config',	'Config.keepOldRevision',	'3'),
('Config',	'Config.cronPassword',	'"testCronPassword"');
