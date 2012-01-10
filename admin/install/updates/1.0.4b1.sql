# Version 1.0.3b;
# Migration(upgrade).Uses only if UPDATE proccess executes!;
# Prev version 1.0.3a;

SET foreign_key_checks = 0;

ALTER TABLE `#__newsletter_mailbox_profiles` MODIFY COLUMN  `data` LONGBLOB;

CREATE TABLE `#__newsletter_automailings` (
  `automailing_id` INT(11) NOT NULL AUTO_INCREMENT,
  `automailing_name` VARCHAR(255) DEFAULT NULL,
  `automailing_type` ENUM('scheduled','eventbased') DEFAULT NULL,
  `automailing_event` ENUM('date','subscription') DEFAULT NULL,
  `automailing_state` INT(11) DEFAULT NULL,
  `params` TEXT,

  PRIMARY KEY (`automailing_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `#__newsletter_automailing_items` (
  `series_id` INT(11) NOT NULL AUTO_INCREMENT,
  `automailing_id` INT(11) DEFAULT NULL,
  `newsletter_id` BIGINT(11) DEFAULT NULL,
  `time_start` TIMESTAMP NULL DEFAULT NULL,
  `time_offset` INT(11) DEFAULT NULL,
  `parent_id` INT(11) DEFAULT '0',
  `status` INT(11),
  `sent` INT(11),
  `params` TEXT,

  PRIMARY KEY (`series_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `#__newsletter_threads` (
  `thread_id` INT(11) NOT NULL AUTO_INCREMENT,
  `parent_id` INT(11) DEFAULT NULL,
  `type` ENUM ('send', 'automail', 'read') NOT NULL,
  `subtype` VARCHAR (255),
  `resource` VARCHAR (255) NOT NULL COMMENT "The target point of a process. email for 'send' and 'read'",
  `params` TEXT,

  PRIMARY KEY (`thread_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `#__newsletter_automailing_targets` (
  `am_target_id` INT(11) NOT NULL AUTO_INCREMENT,
  `automailing_id` INT(11) DEFAULT NULL,
  `target_id` INT(11) DEFAULT NULL,
  `target_type` VARCHAR (255) DEFAULT NULL,

  PRIMARY KEY (`am_target_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE INDEX automailing_ids_idxfk ON #__newsletter_automailing_targets(automailing_id);
ALTER TABLE #__newsletter_automailing_targets ADD FOREIGN KEY (automailing_id) REFERENCES #__newsletter_automailings (automailing_id) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE INDEX automailing_ids_idxfk ON #__newsletter_automailing_items(automailing_id);
ALTER TABLE #__newsletter_automailing_items ADD FOREIGN KEY (automailing_id) REFERENCES #__newsletter_automailings (automailing_id) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE INDEX newsletter_ids_idxfk ON #__newsletter_automailing_items(newsletter_id);
ALTER TABLE #__newsletter_automailing_items ADD FOREIGN KEY (newsletter_id) REFERENCES #__newsletter_newsletters (newsletter_id) ON DELETE CASCADE ON UPDATE CASCADE;

SET foreign_key_checks = 1;
