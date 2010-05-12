CREATE TABLE IF NOT EXISTS `ip_cms_mod_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `language_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `verification_code` varchar(32) NOT NULL,
  `new_email` varchar(255) DEFAULT NULL,
  `warned_on` timestamp NULL DEFAULT NULL,
  `valid_until` timestamp NULL DEFAULT NULL COMMENT 'required for maintenance. Real date should be calculated in real time by last_login field.',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `ip_cms_mod_user`
--

INSERT INTO `ip_cms_mod_user` (`id`, `login`, `language_id`, `email`, `password`, `verified`, `created_on`, `last_login`, `verification_code`, `new_email`, `warned_on`, `valid_until`) VALUES
(14, 'maskas', 384, 'mangirdas@apro.lt', '2a3b728546073fa4c6c09c2010002a78', 1, '2009-06-16 22:24:07', '2009-06-16 00:00:00', '8f7c57dfef30df7811a1337816c282ed', NULL, NULL, NULL);
