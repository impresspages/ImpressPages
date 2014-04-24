-- UPDATE `ip_user` SET `name` = ?, `pass` = MD5(?) limit 1;
INSERT INTO `ip_storage` (`plugin`, `key`, `value`) VALUES
('Config',	'Config.en.websiteTitle',	'"TestSite"'),
('Config',	'Config.en.websiteEmail',	'"test@test.com"'),
('Config',	'Config.multilingual',	'1'),
('Config',	'Config.automaticCron',	'1'),
('Config',	'Config.cronPassword',	'"testCronPassword"');
