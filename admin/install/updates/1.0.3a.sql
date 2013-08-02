# Version 1.0.3a1;
# Migration(upgrade).Uses only if UPDATE proccess executes!;
# Prev version 1.0.2a;


# Renamed from 1.0.3 to be applied after 1.0.3a and before 1.0.3b1;

SET foreign_key_checks = 0;

CREATE INDEX `newsletter_queue_state` ON #__newsletter_queue(`state`);

CREATE TABLE `#__newsletter_mailbox_profiles` (
  `mailbox_profile_id` INT(11) NOT NULL AUTO_INCREMENT,
  `mailbox_profile_name` VARCHAR(255) DEFAULT NULL,
  `mailbox_server` VARCHAR(255) DEFAULT NULL,
  `mailbox_server_type` VARCHAR(255) DEFAULT NULL,
  `mailbox_port` INT(11) DEFAULT NULL,
  `is_ssl` ENUM('1','0') DEFAULT '0',
  `username` VARCHAR(255) DEFAULT NULL,
  `password` VARCHAR(255) DEFAULT NULL,

  PRIMARY KEY (`mailbox_profile_id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE `#__newsletter_smtp_profiles` ADD COLUMN `mailbox_profile_id` INT(11);

ALTER TABLE `#__newsletter_smtp_profiles` MODIFY COLUMN `is_ssl` INT(11);

SET foreign_key_checks = 1;
