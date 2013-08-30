
SET foreign_key_checks = 0;


ALTER TABLE `#__newsletter_newsletters` ADD COLUMN `state` INT(11) DEFAULT 1 NOT NULL;

ALTER TABLE `#__newsletter_template_styles` ADD COLUMN `state` INT(11) DEFAULT 1 NOT NULL;

ALTER TABLE `#__newsletter_automailings` ADD COLUMN `state` INT(11) DEFAULT 1  NOT NULL;

SET foreign_key_checks = 1;
