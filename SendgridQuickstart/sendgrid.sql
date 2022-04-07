CREATE TABLE IF NOT EXISTS sendgrid_email_trackers (
    `id` int(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `model_id` int(11) NOT NULL,
    `model_name` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
    `email_type` varchar(100) DEFAULT NULL,
    `email_transport` varchar(100) DEFAULT NULL,
    `multiple_email_model_name` varchar(100) DEFAULT NULL,
    `multiple_email_model_id` int(11) DEFAULT NULL,
    `to_email` varchar(100) NOT NULL,
    `tracker_id` varchar(100) NOT NULL,
    `status_code` int(11) DEFAULT NULL,
    `response` text DEFAULT NULL,
    `is_success` tinyint(1) NOT NULL DEFAULT 0,
    `is_process` tinyint(1) NOT NULL DEFAULT 0,
    `is_delivered` tinyint(1) NOT NULL DEFAULT 0,
    `is_clicked` tinyint(1) NOT NULL DEFAULT 0,
    `is_opened` tinyint(1) NOT NULL DEFAULT 0,
    `is_deferred` tinyint(1) NOT NULL DEFAULT 0,
    `is_bounced` tinyint(1) NOT NULL DEFAULT 0,
    `is_dropped` tinyint(1) NOT NULL DEFAULT 0,
    `is_debug` tinyint(1) NOT NULL DEFAULT 0,
    `sent_email_response` LONGTEXT DEFAULT NULL,
    `created` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `modified` DATETIME ON UPDATE CURRENT_TIMESTAMP
)  ENGINE=INNODB AUTO_INCREMENT=1;

CREATE TABLE `shared_s3v3_send_emails` (
	`id` INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
	`email_tracker_id` INT(11) NOT NULL DEFAULT '0',
	`model_id` INT(11) NOT NULL,
	`model_name` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
	`order_id` INT(11) NOT NULL,
	`email_type` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
	`is_send_mail` TINYINT(4) NOT NULL DEFAULT '0',
	`is_generating` TINYINT(4) NOT NULL DEFAULT '0',
	`is_sync` TINYINT(1) NOT NULL DEFAULT '0',
	`scheduled_time` DATETIME NULL DEFAULT NULL,
	`is_attempt_exceed_notify_admin` TINYINT(2) NOT NULL DEFAULT '0',
	`created` DATETIME NULL DEFAULT NULL,
	`modified` DATETIME NULL DEFAULT NULL,
	INDEX `scheduled_time` (`scheduled_time`) USING BTREE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sendgrid_email_tracker_events (
    `id` int(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `sendgrid_email_tracker_id` int(11) NOT NULL,
    `event_name` varchar(100) NOT NULL,
    `event_responses` text DEFAULT NULL,
    `created` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `modified` DATETIME ON UPDATE CURRENT_TIMESTAMP
)  ENGINE=INNODB AUTO_INCREMENT=1;
