# Version 1.0.2a;
# Migration(upgrade).Uses only if UPDATE proccess executes!;
# Prev version 0.5.1a;

SET foreign_key_checks = 0;

    ALTER TABLE `#__newsletter_smtp_profiles` MODIFY `smtp_profile_id` INT(11) NOT NULL AUTO_INCREMENT;
    ALTER TABLE `#__newsletter_subscribers` ADD COLUMN `extra` TEXT;
    ALTER TABLE `#__newsletter_lists` ADD COLUMN `extra` TEXT;
    ALTER TABLE `#__newsletter_sub_list` ADD COLUMN `extra` TEXT;

SET foreign_key_checks = 1;
