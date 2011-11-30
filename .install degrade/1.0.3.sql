SET FOREIGN_KEY_CHECKS = 0;

DROP INDEX `newsletter_queue_state` ON jos_newsletter_queue;

DROP TABLE `jos_newsletter_mailbox_profiles`;

ALTER TABLE `jos_newsletter_smtp_profiles` DROP COLUMN `mailbox_profile_id`;

ALTER TABLE `jos_newsletter_smtp_profiles` MODIFY COLUMN `is_ssl` ENUM("1","0") DEFAULT '0';

SET FOREIGN_KEY_CHECKS = 1;
