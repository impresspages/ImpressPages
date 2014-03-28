-- UPDATE `ip_user` SET `name` = ?, `pass` = MD5(?) limit 1;
INSERT INTO `ip_storage` (`plugin`, `key`, `value`) VALUES
('Config',	'Config.websiteTitle',	'"TestSite"'),
('Config',	'Config.multilingual',	'1'),
('Config',	'Config.websiteEmail',	'"test@test.com"'),
('Config',	'Config.automaticCron',	'1'),
('Config',	'Config.cronPassword',	'"testCronPassword"');
