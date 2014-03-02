ALTER TABLE  `ip_page` ADD  `redirectUrl` VARCHAR( 255 ) NULL DEFAULT NULL AFTER  `description` ,
ADD  `isDisabled` BOOLEAN NOT NULL DEFAULT FALSE AFTER  `redirectUrl`,
ADD  `isSecured` BOOLEAN NOT NULL DEFAULT FALSE AFTER  `isDeleted`
