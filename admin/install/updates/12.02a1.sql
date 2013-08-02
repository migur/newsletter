# Version 12.02a1;
# Migration(upgrade).Uses only if UPDATE proccess executes!;
# Prev version 12.01a1;

SET foreign_key_checks = 0;



CREATE TABLE `#__newsletter_logs` (
  `log_id` INT(11) NOT NULL AUTO_INCREMENT,
  `message` TEXT(255),
  `date` DATETIME,
  `priority` INT(11),
  `category` VARCHAR(255) NOT NULL DEFAULT 'common',
  `params` TEXT,

  PRIMARY KEY (`log_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE INDEX `date_idfk` ON `#__newsletter_logs`(`date`);
CREATE INDEX `category_idfk` ON `#__newsletter_logs`(`category`);



SET foreign_key_checks = 1;
