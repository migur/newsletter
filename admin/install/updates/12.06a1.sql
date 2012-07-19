# Version 12.02a1;
# Migration(upgrade).Uses only if UPDATE proccess executes!;
# Prev version 12.01a1;

SET foreign_key_checks = 0;

ALTER TABLE `#__newsletter_extensions` ADD COLUMN `namespace` VARCHAR(255) DEFAULT '';
ALTER TABLE `#__newsletter_lists` ADD COLUMN `autoconfirm` SMALLINT;

SET foreign_key_checks = 1;
