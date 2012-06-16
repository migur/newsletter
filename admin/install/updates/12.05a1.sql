# Version 12.02a1;
# Migration(upgrade).Uses only if UPDATE proccess executes!;
# Prev version 12.01a1;

SET foreign_key_checks = 0;

ALTER TABLE `#__newsletter_mailbox_profiles` MODIFY COLUMN  `is_ssl` ENUM('0', '1', '2') DEFAULT '0';
ALTER TABLE `#__newsletter_mailbox_profiles` ADD COLUMN  `validate_cert` ENUM('0', '1') DEFAULT '1';

SET foreign_key_checks = 1;
