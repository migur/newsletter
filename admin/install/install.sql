# Ver 0.5.1a;

SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `#__newsletter_extensions`;
DROP TABLE IF EXISTS `#__newsletter_newsletters_ext`;
DROP TABLE IF EXISTS `#__newsletter_queue`;
DROP TABLE IF EXISTS `#__newsletter_sent`;
DROP TABLE IF EXISTS `#__newsletter_template_styles`;
DROP TABLE IF EXISTS `#__newsletter_subscribers`;
DROP TABLE IF EXISTS `#__newsletter_smtp_profiles`;
DROP TABLE IF EXISTS `#__newsletter_newsletters`;
DROP TABLE IF EXISTS `#__newsletter_lists`;
DROP TABLE IF EXISTS `#__newsletter_sub_history`;
DROP TABLE IF EXISTS `#__newsletter_sub_list`;
DROP TABLE IF EXISTS `#__newsletter_downloads`;

SET foreign_key_checks = 1;

CREATE TABLE `#__newsletter_template_styles`
(
`t_style_id` INT(10) NOT NULL AUTO_INCREMENT,
`template` VARCHAR(50) DEFAULT '' NOT NULL,
`title` VARCHAR(255) DEFAULT '' NOT NULL,
`params` TEXT NOT NULL,

PRIMARY KEY (`t_style_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `#__newsletter_subscribers`
(
`subscriber_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
`name` VARCHAR(255) NOT NULL,
`email` VARCHAR(255) NOT NULL,
`state` TINYINT(1) DEFAULT '1' NOT NULL,
`html` TINYINT(1) DEFAULT '1' NOT NULL,
`user_id` INT(11) NOT NULL,
`created_on` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
`created_by` INT(11) DEFAULT '0' NOT NULL,
`modified_on` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
`modified_by` INT(11) DEFAULT '0' NOT NULL,
`locked_on` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
`locked_by` INT(11) DEFAULT '0' NOT NULL,
`confirmed` VARCHAR(255) NOT NULL,
`subscription_key` VARCHAR(40) NOT NULL,
`extra` text

PRIMARY KEY (`subscriber_id`,`user_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `#__newsletter_smtp_profiles`
(
`smtp_profile_id` INT(11) NOT NULL AUTO_INCREMENT,
`smtp_profile_name` VARCHAR(255),
`from_name` VARCHAR(255),
`from_email` VARCHAR(255),
`reply_to_email` VARCHAR(255),
`reply_to_name` VARCHAR(255),
`smtp_server` VARCHAR(255),
`smtp_port` INTEGER(2),
`is_ssl` ENUM("1","0") DEFAULT '0',
`pop_before_smtp` ENUM("1","0") DEFAULT '0',
`username` VARCHAR(255),
`password` VARCHAR(255),

PRIMARY KEY (`smtp_profile_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `#__newsletter_newsletters`
(
`newsletter_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
`name` VARCHAR(255) NOT NULL,
`subject` VARCHAR(255) NOT NULL,
`alias` VARCHAR(255) NOT NULL,
`smtp_profile_id` INT(11) NOT NULL,
`t_style_id` INT(10),
`plain` TEXT,
`params` TEXT,
`ordering` INT(11) NOT NULL,
`language` CHAR(7) NOT NULL,
`checked_out` INT(10) NOT NULL,
`checked_out_time` DATETIME NOT NULL,
`created` DATETIME NOT NULL,
`sent_started` DATETIME NOT NULL,
`type` INT(11) NOT NULL,

PRIMARY KEY (`newsletter_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `#__newsletter_lists`
(
`list_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
`name` VARCHAR(255) NOT NULL,
`state` TINYINT(1) DEFAULT '1' NOT NULL,
`description` TEXT NOT NULL,
`smtp_profile_id` INT(11) NOT NULL,
`ordering` BIGINT(20) NOT NULL,
`created_on` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
`created_by` INT(11) DEFAULT '0' NOT NULL,
`modified_on` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
`modified_by` INT(11) DEFAULT '0' NOT NULL,
`locked_on` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
`locked_by` INT(11) DEFAULT '0' NOT NULL,
`internal` TINYINT(3) DEFAULT '0' NOT NULL,
send_at_reg INT(11) DEFAULT '0' NOT NULL,
send_at_unsubscribe INT(11) DEFAULT '0' NOT NULL,
`extra` text

PRIMARY KEY (`list_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `#__newsletter_sent`
(
`sent_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
`subscriber_id` BIGINT(20) NOT NULL,
`list_id` BIGINT(20) NOT NULL,
`newsletter_id` BIGINT(20) NOT NULL,
`sent_date` DATETIME NOT NULL,
`recieved_date` DATETIME NOT NULL,
`bounced` ENUM("NO","HARD","SOFT","TECHNICAL") NOT NULL,
`html_content` TEXT,
`plaintext_content` TEXT,

PRIMARY KEY (`sent_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `#__newsletter_sub_history`
(
`history_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
`subscriber_id` BIGINT(20) NOT NULL,
`list_id` BIGINT(20) NOT NULL,
`newsletter_id` BIGINT(20) NOT NULL,
`date` DATETIME NOT NULL,
`action` INT(11) NOT NULL,
`text` TEXT NOT NULL,

PRIMARY KEY (`history_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `#__newsletter_sub_list`
(
`sublist_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
`subscriber_id` BIGINT(20) NOT NULL,
`list_id` BIGINT(20) NOT NULL,
`confirmed` VARCHAR(255) NOT NULL,
`extra` text

PRIMARY KEY (`sublist_id`),
UNIQUE KEY `unique-subscriber` (`subscriber_id`,`list_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `#__newsletter_newsletters_ext`
(
`newsletters_ext_id` INT(11) NOT NULL AUTO_INCREMENT,
`newsletter_id` BIGINT(20) NOT NULL,
`extension_id` INT(11),
`position` VARCHAR(255),
`params` TEXT,
`ordering` INT(11),
`native` INT(11),
`title` VARCHAR(255),
`showtitle` INT(11),

PRIMARY KEY (`newsletters_ext_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `#__newsletter_extensions`
(
`extension_id` INT(11) NOT NULL AUTO_INCREMENT,
`title` VARCHAR(255) DEFAULT '' NOT NULL,
`extension` VARCHAR(255) DEFAULT '' NOT NULL,
`params` TEXT,
`type` int(11) NOT NULL,

PRIMARY KEY (`extension_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `#__newsletter_queue`
(
`queue_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
`newsletter_id` INT(11) NOT NULL,
`subscriber_id` INT(11) NOT NULL,
`list_id` INT(11) NOT NULL,
`created` DATETIME NOT NULL,
`state` INT(11) NOT NULL,

PRIMARY KEY (`queue_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `#__newsletter_downloads`
(
`downloads_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
`filename` VARCHAR(255) NOT NULL,
`newsletter_id` INT(11) NOT NULL,

PRIMARY KEY (`downloads_id`)
) ENGINE=INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE INDEX `smtp_profile_id_idxfk` ON `#__newsletter_newsletters`(`smtp_profile_id`);
# ALTER TABLE `#__newsletter_newsletters` ADD CONSTRAINT `newsletters_smtp_profile_idfk` FOREIGN KEY `smtp_profile_id_idxfk` (`smtp_profile_id`) REFERENCES `#__newsletter_smtp_profiles` (`smtp_profile_id`);

CREATE INDEX t_style_id_idxfk ON #__newsletter_newsletters(t_style_id);
ALTER TABLE #__newsletter_newsletters ADD FOREIGN KEY (t_style_id) REFERENCES #__newsletter_template_styles (t_style_id) ON DELETE SET NULL ON UPDATE RESTRICT;

CREATE INDEX smtp_profile_id_idxfk ON #__newsletter_lists(smtp_profile_id);
# ALTER TABLE #__newsletter_lists ADD FOREIGN KEY (smtp_profile_id) REFERENCES #__newsletter_smtp_profiles (smtp_profile_id);

CREATE INDEX list_id_idxfk ON #__newsletter_sent(list_id);
# We can not use this key because the list_id is not ALWAYS present in sent newsletter;
# ALTER TABLE #__newsletter_sent ADD CONSTRAINT `newsletter_sent_list_idfk` FOREIGN KEY list_id_idxfk (list_id) REFERENCES #__newsletter_lists (list_id);

CREATE INDEX list_id_idxfk ON #__newsletter_sub_history(list_id);
# Do not use this index because it prevent to save the events without list_id;
# ALTER TABLE #__newsletter_sub_history ADD CONSTRAINT `sub_history_list_idfk` FOREIGN KEY list_id_idxfk_1 (list_id) REFERENCES #__newsletter_lists (list_id);

CREATE INDEX newsletter_id_idxfk ON #__newsletter_sub_history(newsletter_id);
ALTER TABLE #__newsletter_sub_history ADD FOREIGN KEY (newsletter_id) REFERENCES #__newsletter_newsletters (newsletter_id) ON DELETE SET NULL ON UPDATE RESTRICT;

CREATE INDEX list_id_idxfk ON #__newsletter_sub_list(list_id);
ALTER TABLE #__newsletter_sub_list ADD FOREIGN KEY (list_id) REFERENCES #__newsletter_lists (list_id) ON DELETE CASCADE ON UPDATE RESTRICT;

CREATE INDEX newsletter_idxfk ON #__newsletter_newsletters_ext(newsletter_id);
ALTER TABLE #__newsletter_newsletters_ext ADD FOREIGN KEY (newsletter_id) REFERENCES #__newsletter_newsletters (newsletter_id) ON DELETE CASCADE ON UPDATE RESTRICT;

CREATE INDEX extension_id_idxfk ON #__newsletter_newsletters_ext(extension_id);
# Do not use this index because it prevent to bind the Joomla native modules to newsletter;
# ALTER TABLE #__newsletter_newsletters_ext ADD CONSTRAINT `newsletters_ext_extension_idfk` FOREIGN KEY extension_idxfk (extension_id) REFERENCES #__newsletter_extensions (extension_id) ON DELETE CASCADE;

# Data for the table `#__newsletter_extensions`;
insert  into `#__newsletter_extensions`(`extension_id`,`title`,`extension`,`params`,`type`) values (1,'Article Module','mod_article','{}',1);
insert  into `#__newsletter_extensions`(`extension_id`,`title`,`extension`,`params`,`type`) values (2,'Image Module','mod_img','{}',1);
insert  into `#__newsletter_extensions`(`extension_id`,`title`,`extension`,`params`,`type`) values (3,'RSS Module','mod_rss','{}',1);
insert  into `#__newsletter_extensions`(`extension_id`,`title`,`extension`,`params`,`type`) values (4,'Text Module','mod_text','{}',1);
insert  into `#__newsletter_extensions`(`extension_id`,`title`,`extension`,`params`,`type`) values (5,'WYSIWYG Module','mod_wysiwyg','{}',1);
insert  into `#__newsletter_extensions`(`extension_id`,`title`,`extension`,`params`,`type`) values (6,'Google Analytics','ganalytics','{}',2);

# Example data for tables -----------------------------------------------------;

# Data for the table `#__newsletter_template_styles`;
INSERT  INTO `#__newsletter_template_styles`(`t_style_id`,`template`,`title`,`params`) VALUES (5,'doublecolumn1.xml','Standard doublecolumn template (custom)','{\"width_column1\":\"50%\",\"height_column1\":\"50%\",\"width_column2\":\"50%\",\"height_column2\":\"50%\",\"image_top\":\"administrator\\/components\\/com_newsletter\\/extensions\\/img\\/top_image.png\",\"image_top_alt\":\"The top image\",\"image_top_width\":\"600px\",\"image_top_height\":\"100px\",\"image_bottom\":\"administrator\\/components\\/com_newsletter\\/extensions\\/img\\/bottom_image.png\",\"image_bottom_alt\":\"The bottom image\",\"image_bottom_width\":\"600px\",\"image_bottom_height\":\"100px\",\"table_background\":\"#000000\",\"text_color\":\"#000000\",\"t_style_id\":\"5\"}');
INSERT  INTO `#__newsletter_template_styles`(`t_style_id`,`template`,`title`,`params`) VALUES (6,'singlecolumn1.xml','Standard singlecolumn template (custom)','{\"width_column1\":\"50%\",\"height_column1\":\"50%\",\"width_column2\":\"50%\",\"height_column2\":\"50%\",\"image_top\":\"administrator\\/components\\/com_newsletter\\/extensions\\/img\\/top_image.png\",\"image_top_alt\":\"The top image\",\"image_top_width\":\"600px\",\"image_top_height\":\"100px\",\"image_bottom\":\"administrator\\/components\\/com_newsletter\\/extensions\\/img\\/bottom_image.png\",\"image_bottom_alt\":\"The bottom image\",\"image_bottom_width\":\"600px\",\"image_bottom_height\":\"100px\",\"table_background\":\"#000000\",\"text_color\":\"#000000\",\"t_style_id\":\"6\"}');
INSERT  INTO `#__newsletter_template_styles`(`t_style_id`,`template`,`title`,`params`) VALUES (8,'threecolumn1.xml','Standard threecolumn template (custom)','{\"width_column1\":\"50%\",\"height_column1\":\"50%\",\"width_column2\":\"50%\",\"height_column2\":\"50%\",\"image_top\":\"administrator\\/components\\/com_newsletter\\/extensions\\/img\\/top_image.png\",\"image_top_alt\":\"The top image\",\"image_top_width\":\"600px\",\"image_top_height\":\"100px\",\"image_bottom\":\"administrator\\/components\\/com_newsletter\\/extensions\\/img\\/bottom_image.png\",\"image_bottom_alt\":\"The bottom image\",\"image_bottom_width\":\"600px\",\"image_bottom_height\":\"100px\",\"table_background\":\"#000000\",\"text_color\":\"#000000\",\"t_style_id\":\"8\"}');

# Data for the table `#__newsletter_subscribers`;
insert  into `#__newsletter_subscribers`(`subscriber_id`,`name`,`email`,`state`,`html`,`user_id`,`created_on`,`created_by`,`modified_on`,`modified_by`,`locked_on`,`locked_by`,`confirmed`,`subscription_key`) values (1,'John Doe','john-doe@example.com',1,1,0,'0000-00-00 00:00:00',0,'0000-00-00 00:00:00',0,'0000-00-00 00:00:00',0,'1','1234560000000011234567890');
insert  into `#__newsletter_subscribers`(`subscriber_id`,`name`,`email`,`state`,`html`,`user_id`,`created_on`,`created_by`,`modified_on`,`modified_by`,`locked_on`,`locked_by`,`confirmed`,`subscription_key`) values (2,'Jane Doe','jane-doe@example.com',1,1,0,'0000-00-00 00:00:00',0,'0000-00-00 00:00:00',0,'0000-00-00 00:00:00',0,'1','6543210000000021234567895');

# Data for the table `#__newsletter_lists`;
insert  into `#__newsletter_lists`(`list_id`,`name`,`state`,`description`,`smtp_profile_id`,`ordering`,`created_on`,`created_by`,`modified_on`,`modified_by`,`locked_on`,`locked_by`,`send_at_reg`,`send_at_unsubscribe`) values (2,'Doe Family holiday!',1,'The letter for Doe family members about birthday of Baby Doe!',0,0,'0000-00-00 00:00:00',0,'0000-00-00 00:00:00',0,'0000-00-00 00:00:00',0,0,0);

# Data for the table `#__newsletter_sub_list`;
insert  into `#__newsletter_sub_list`(`sublist_id`,`subscriber_id`,`list_id`,`confirmed`) values (1,2,2,'');
insert  into `#__newsletter_sub_list`(`sublist_id`,`subscriber_id`,`list_id`,`confirmed`) values (2,1,2,'');

# Data for the table `#__newsletter_newsletters`;
insert  into `#__newsletter_newsletters`(`newsletter_id`,`name`,`subject`,`alias`,`smtp_profile_id`,`t_style_id`,`plain`,`params`,`ordering`,`language`,`checked_out`,`checked_out_time`,`created`,`sent_started`,`type`) VALUES (96,'Birthday of Baby Doe!','Baby Doe','',0,5,'Meet the Baby Doe!\nCongratulations for [username]! \n\nTo unsubscribe: [unsubscription link]','{\"newsletter_from_name\":\"John Doe\",\"newsletter_from_email\":\"johndoe@example.com\",\"newsletter_to_name\":\"John Doe\",\"newsletter_to_email\":\"johndoe@example.com\"}',0,'',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',0);

# Data for the table `#__newsletter_newsletters_ext`;
insert  into `#__newsletter_newsletters_ext`(`newsletters_ext_id`,`newsletter_id`,`extension_id`,`position`,`params`,`ordering`,`native`,`title`,`showtitle`) values (6,96,4,'header_module_position','{\"text\":\"<p>Meet the Baby Doe!<\\/p>\\n<p>Congratulations for [username]!<\\/p>\"}',1,0,'Text Module',1);
