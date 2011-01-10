
ALTER DATABASE  `[[[[database]]]]` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_content_element`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_content_element` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `row_number` int(11) NOT NULL DEFAULT '0',
  `parent` int(11) DEFAULT NULL,
  `button_title` varchar(255) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `html` mediumtext,
  `page_title` mediumtext,
  `keywords` mediumtext,
  `description` mediumtext,
  `url` varchar(255) DEFAULT NULL,
  `dynamic_modules` mediumtext,
  `last_modified` timestamp NULL DEFAULT NULL,
  `modify_track1` timestamp NULL DEFAULT NULL,
  `modify_track2` timestamp NULL DEFAULT NULL,
  `modify_track3` timestamp NULL DEFAULT NULL,
  `modify_frequency` int(11) DEFAULT NULL,
  `rss` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cached_html` mediumtext,
  `cached_text` mediumtext COMMENT 'mainly for search purposes',
  `type` varchar(255) NOT NULL DEFAULT 'default',
  `redirect_url` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_content_element_to_modules`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_content_element_to_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `row_number` int(11) NOT NULL DEFAULT '0',
  `element_id` int(11) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `module_key` varchar(255) NOT NULL DEFAULT '0',
  `group_key` varchar(255) NOT NULL,
  `module_id` int(11) NOT NULL DEFAULT '0',
  `instance_id` int(11) NOT NULL DEFAULT '0',
  `preview` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_content_module`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_content_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `row_number` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `dynamic` tinyint(1) NOT NULL DEFAULT '0',
  `translation` varchar(255) NOT NULL,
  `version` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_content_module_group`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_content_module_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `row_number` int(11) NOT NULL DEFAULT '0',
  `translation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_language`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `d_short` varchar(255) NOT NULL DEFAULT '',
  `d_long` varchar(255) NOT NULL DEFAULT '',
  `row_number` int(11) NOT NULL DEFAULT '0',
  `visible` int(1) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL DEFAULT '',
  `code` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_log`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL COMMENT 'module group and name',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(255) DEFAULT NULL,
  `value_str` mediumtext,
  `value_int` int(11) DEFAULT NULL,
  `value_float` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_misc_contact_form`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_misc_contact_form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thank_you` varchar(255) NOT NULL DEFAULT '',
  `email_to` varchar(255) NOT NULL DEFAULT '',
  `email_subject` varchar(255) NOT NULL DEFAULT '',
  `button` varchar(255) NOT NULL DEFAULT '',
  `layout` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_misc_contact_form_field`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_misc_contact_form_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `required` int(1) NOT NULL DEFAULT '0',
  `contact_form` int(11) NOT NULL DEFAULT '0',
  `values` text COMMENT 'json array',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_misc_file`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_misc_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` mediumtext NOT NULL,
  `photo` mediumtext NOT NULL,
  `layout` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_misc_html_code`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_misc_html_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` mediumtext NOT NULL,
  `layout` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_misc_rich_text`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_misc_rich_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` mediumtext NOT NULL,
  `layout` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_misc_video`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_misc_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` mediumtext NOT NULL,
  `photo` mediumtext NOT NULL,
  `layout` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_text_photos_faq`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_text_photos_faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` mediumtext NOT NULL,
  `text` mediumtext NOT NULL,
  `layout` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_text_photos_logo_gallery`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_text_photos_logo_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blank` char(1) NOT NULL DEFAULT '',
  `layout` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_text_photos_logo_gallery_logo`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_text_photos_logo_gallery_logo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` mediumtext NOT NULL,
  `logo` mediumtext NOT NULL,
  `logo_gallery` int(11) NOT NULL DEFAULT '0',
  `row_number` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_text_photos_photo`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_text_photos_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` mediumtext NOT NULL,
  `photo` mediumtext NOT NULL,
  `photo_big` mediumtext NOT NULL,
  `layout` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_text_photos_photo_gallery`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_text_photos_photo_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blank` char(1) NOT NULL DEFAULT '',
  `layout` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_text_photos_photo_gallery_photo`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_text_photos_photo_gallery_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` mediumtext NOT NULL,
  `photo` mediumtext NOT NULL,
  `photo_big` mediumtext NOT NULL,
  `photo_gallery` int(11) NOT NULL DEFAULT '0',
  `row_number` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_text_photos_separator`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_text_photos_separator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `layout` varchar(255) NOT NULL DEFAULT 'line',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_text_photos_table`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_text_photos_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` mediumtext NOT NULL,
  `layout` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_text_photos_text`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_text_photos_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` mediumtext NOT NULL,
  `layout` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_text_photos_text_photo`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_text_photos_text_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` mediumtext NOT NULL,
  `photo` mediumtext NOT NULL,
  `photo_big` mediumtext NOT NULL,
  `text` mediumtext NOT NULL,
  `layout` varchar(255) NOT NULL DEFAULT 'left',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_text_photos_text_title`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_text_photos_text_title` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` mediumtext NOT NULL,
  `text` mediumtext NOT NULL,
  `level` int(11) NOT NULL DEFAULT '1',
  `layout` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_mc_text_photos_title`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_mc_text_photos_title` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` mediumtext NOT NULL,
  `level` int(11) NOT NULL DEFAULT '1',
  `layout` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_module`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `row_number` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `translation` varchar(255) NOT NULL,
  `managed` tinyint(1) NOT NULL DEFAULT '1',
  `version` decimal(10,2) NOT NULL,
  `core` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_module_group`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_module_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `row_number` int(11) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `translation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_module_permission`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_module_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_to_module_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_m_administrator_email_queue`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_m_administrator_email_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` mediumtext NOT NULL,
  `to` varchar(255) NOT NULL,
  `to_name` varchar(255) DEFAULT NULL,
  `from` varchar(255) NOT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `immediate` tinyint(1) NOT NULL DEFAULT '0',
  `html` tinyint(1) NOT NULL,
  `send` timestamp NULL DEFAULT NULL,
  `lock` varchar(32) DEFAULT NULL,
  `locked_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `files` mediumtext,
  `file_names` mediumtext,
  `file_mime_types` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_m_administrator_rss`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_m_administrator_rss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) DEFAULT NULL,
  `zone_key` varchar(255) DEFAULT NULL,
  `element_id` int(11) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rss` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_m_community_newsletter`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_m_community_newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) DEFAULT NULL,
  `text` mediumtext,
  `language_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_m_community_newsletter_subscribers`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_m_community_newsletter_subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `verification_code` varchar(32) DEFAULT NULL,
  `language_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_m_community_user`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_m_community_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) DEFAULT NULL,
  `language_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `verification_code` varchar(32) NOT NULL,
  `new_email` varchar(255) DEFAULT NULL,
  `new_password` varchar(32) DEFAULT NULL,
  `warned_on` timestamp NULL DEFAULT NULL,
  `valid_until` timestamp NULL DEFAULT NULL COMMENT 'required for maintenance. Real date should be calculated in real time by last_login field.',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_parameter`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_parameter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `row_number` int(11) NOT NULL DEFAULT '0',
  `regexpression` varchar(100) NOT NULL DEFAULT '',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `translation` varchar(255) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_parameter_group`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_parameter_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `module_id` int(11) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `row_number` int(11) NOT NULL DEFAULT '0',
  `translation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_par_bool`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_par_bool` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` tinyint(1) DEFAULT NULL,
  `parameter_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_par_integer`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_par_integer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` int(11) DEFAULT NULL,
  `parameter_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_par_lang`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_par_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `translation` mediumtext NOT NULL,
  `language_id` int(11) DEFAULT NULL,
  `parameter_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_par_string`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_par_string` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` mediumtext NOT NULL,
  `parameter_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_user`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `pass` varchar(32) NOT NULL DEFAULT '',
  `wrong_logins` int(11) NOT NULL DEFAULT '0',
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  `e_mail` varchar(255) NOT NULL DEFAULT '',
  `row_number` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_user_to_mod`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_user_to_mod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `module_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_variables`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_variables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'module group and name',
  `value` text,
  `modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_zone`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `row_number` int(11) NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL DEFAULT '',
  `template` varchar(255) DEFAULT NULL,
  `translation` varchar(255) NOT NULL,
  `associated_group` varchar(255) DEFAULT NULL,
  `associated_module` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_zone_parameter`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_zone_parameter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` mediumtext,
  `keywords` mediumtext,
  `title` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `language_id` int(11) DEFAULT NULL,
  `translation` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Table structure

DROP TABLE IF EXISTS `ip_cms_zone_to_content`;

-- Table structure

CREATE TABLE IF NOT EXISTS `ip_cms_zone_to_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL DEFAULT '0',
  `zone_id` int(11) NOT NULL DEFAULT '0',
  `element_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='tells how mutch elements have language' ;

