# Version 1.0.4b1;
# Migration(upgrade).Uses only if UPDATE proccess executes!;
# Prev version 1.0.3b2;

SET foreign_key_checks = 0;



# Fixes for version 1.0.2b;

# Firstly fixes will be applied from script _fixVersion1_0_2b;

ALTER TABLE #__newsletter_sub_history MODIFY COLUMN `newsletter_id` BIGINT(20);
ALTER TABLE #__newsletter_sub_history MODIFY COLUMN `subscriber_id` BIGINT(20);
ALTER TABLE #__newsletter_sub_history MODIFY COLUMN `list_id`       BIGINT(20);

ALTER TABLE #__newsletter_sub_history ADD FOREIGN KEY (newsletter_id) REFERENCES #__newsletter_newsletters (newsletter_id) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE #__newsletter_sub_history ADD FOREIGN KEY (subscriber_id) REFERENCES #__newsletter_subscribers (subscriber_id) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE #__newsletter_sub_history ADD FOREIGN KEY (list_id) REFERENCES #__newsletter_lists (list_id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE #__newsletter_sub_list ADD FOREIGN KEY (list_id) REFERENCES #__newsletter_lists (list_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE #__newsletter_sub_list ADD FOREIGN KEY (subscriber_id) REFERENCES #__newsletter_subscribers (subscriber_id) ON DELETE CASCADE ON UPDATE CASCADE;
# End of fixes;

# Fixes for 1.0.3;
CREATE TABLE IF NOT EXISTS `#__newsletter_mailbox_profiles` (
  `mailbox_profile_id` INT(11) NOT NULL AUTO_INCREMENT,
  `mailbox_profile_name` VARCHAR(255) DEFAULT NULL,
  `mailbox_server` VARCHAR(255) DEFAULT NULL,
  `mailbox_server_type` VARCHAR(255) DEFAULT NULL,
  `mailbox_port` INT(11) DEFAULT NULL,
  `is_ssl` ENUM('1','0') DEFAULT '0',
  `username` VARCHAR(255) DEFAULT NULL,
  `password` VARCHAR(255) DEFAULT NULL,
  `data` LONGBLOB,

  PRIMARY KEY (`mailbox_profile_id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

# Other fixes cannot be applied here.;
# See script _fixVersion1_0_3b;

# End of fixes;



ALTER TABLE `#__newsletter_mailbox_profiles` MODIFY COLUMN  `data` LONGBLOB;
ALTER TABLE `#__newsletter_smtp_profiles` ADD COLUMN  `params` TEXT;
ALTER TABLE `#__newsletter_smtp_profiles` ADD COLUMN  `is_joomla` SMALLINT;
ALTER TABLE `#__newsletter_newsletters` ADD COLUMN  `category` INT(11);

CREATE INDEX email_idx   ON #__newsletter_subscribers(email);
CREATE INDEX user_id_idx ON #__newsletter_subscribers(user_id);

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

ALTER TABLE #__newsletter_queue MODIFY COLUMN `newsletter_id` BIGINT(20);
DELETE FROM #__newsletter_queue WHERE newsletter_id NOT IN (SELECT newsletter_id FROM #__newsletter_newsletters);
CREATE INDEX newsletter_ids_idxfk ON #__newsletter_queue(newsletter_id);
ALTER TABLE #__newsletter_queue ADD FOREIGN KEY (newsletter_id) REFERENCES #__newsletter_newsletters (newsletter_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE #__newsletter_queue MODIFY COLUMN `subscriber_id` BIGINT(20);
DELETE FROM #__newsletter_queue WHERE subscriber_id NOT IN (SELECT subscriber_id FROM #__newsletter_subscribers);
CREATE INDEX subscriber_ids_idxfk ON #__newsletter_queue(subscriber_id);
ALTER TABLE #__newsletter_queue ADD FOREIGN KEY (subscriber_id) REFERENCES #__newsletter_subscribers (subscriber_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE #__newsletter_sub_history MODIFY COLUMN `newsletter_id` BIGINT(20);
ALTER TABLE #__newsletter_sub_history MODIFY COLUMN `subscriber_id` BIGINT(20);
ALTER TABLE #__newsletter_sub_history MODIFY COLUMN `list_id`       BIGINT(20);



SET foreign_key_checks = 1;
