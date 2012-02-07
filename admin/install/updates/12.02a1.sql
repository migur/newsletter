# Version 12.02a1;
# Migration(upgrade).Uses only if UPDATE proccess executes!;
# Prev version 12.01a1;

SET foreign_key_checks = 0;



CREATE TABLE `#__newsletter_logs` (
  `log_id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255),
  `content` TEXT,
  `data` BLOB,
  `created_on` DATETIME NOT NULL,
  `created_by` INT(11),
  `category` VARCHAR(255),
  `subject_table` VARCHAR(11),
  `subject_id` INT(11),

  PRIMARY KEY (`log_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE INDEX `created_on_idfk` ON `#__newsletter_logs`(`created_on`);
CREATE INDEX `subject_tableid_idfk` ON `#__newsletter_logs`(`subject_table`, `subject_id`);



CREATE TABLE `#__newsletter_log_users` (
  `log_user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `log_id` INT(11),
  `user_id` INT(11),
  `action` ENUM('viewed'),

  PRIMARY KEY (`log_user_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE INDEX `user_id_idxfk` ON `#__newsletter_log_users`(`user_id`, `action`);



SET foreign_key_checks = 1;
