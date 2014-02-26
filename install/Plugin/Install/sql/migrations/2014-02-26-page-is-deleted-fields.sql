ALTER TABLE `ip_page`
CHANGE `languageCode` `languageCode` varchar(8) COLLATE 'latin1_general_ci' NULL AFTER `id`,
CHANGE `isVisible` `isPublished` tinyint(1) NOT NULL DEFAULT '0' AFTER `deletedAt`,
ADD `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
ADD `deletedAt` timestamp NULL AFTER `updatedAt`,
ADD INDEX `url` (`languageCode`, `urlPath`);
