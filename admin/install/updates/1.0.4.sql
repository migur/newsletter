# Version 1.0.4;
# Migration(upgrade).Uses only if UPDATE proccess executes!;
# Prev version 1.0.3;

SET foreign_key_checks = 0;

    ALTER TABLE `#__newsletter_mailbox_profiles` ADD COLUMN `extra` TEXT;

SET foreign_key_checks = 1;
