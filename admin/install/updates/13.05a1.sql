
SET foreign_key_checks = 0;


CREATE TABLE `#__newsletter_list_events` (
  `le_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `list_id` BIGINT(20) NOT NULL,
  `jgroup_id` INT(10) UNSIGNED,
  `event` VARCHAR(255),
  `action` VARCHAR(255),

  PRIMARY KEY (`le_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE INDEX `lid_jgid_idfk` ON `#__newsletter_list_events`(`list_id`, `jgroup_id`);

ALTER TABLE `#__newsletter_list_events` ADD FOREIGN KEY (`list_id`) REFERENCES `#__newsletter_lists`(`list_id`) ON DELETE CASCADE ON UPDATE CASCADE;


SET foreign_key_checks = 1;
