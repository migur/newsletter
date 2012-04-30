# Version 12.02a1;
# Migration(upgrade).Uses only if UPDATE proccess executes!;
# Prev version 12.01a1;

SET foreign_key_checks = 0;

ALTER TABLE `#__newsletter_threads` ADD COLUMN  `target` VARCHAR(255);
ALTER TABLE `#__newsletter_threads` ADD COLUMN  `target_type` VARCHAR(255);
ALTER TABLE `#__newsletter_automailings` ADD COLUMN  `scope` ENUM('all','targets') DEFAULT NULL;

CREATE INDEX `target_fk` ON `#__newsletter_threads`(`target`, `target_type`);


SET foreign_key_checks = 1;
