# Version 1.0.3b;
# Migration(upgrade).Uses only if UPDATE proccess executes!;
# Prev version 1.0.3a;

SET foreign_key_checks = 0;

ALTER TABLE `#__newsletter_mailbox_profiles` MODIFY COLUMN  `data` LONGBLOB;

ALTER TABLE `#__newsletter_sent` ADD COLUMN  `extra` LONGBLOB;

SET foreign_key_checks = 1;
