CREATE TABLE IF NOT EXISTS sendgrid_email_trackers (
    `id` int(20) NOT NULL AUTO_INCREMENT,
    `university_id` int(11) NOT NULL,
    `model_id` int(11) NOT NULL,
    `email_type` varchar(100) DEFAULT NULL,
    `email_transport` varchar(100) DEFAULT NULL,
    `multiple_email_model_name` varchar(100) DEFAULT NULL,
    `multiple_email_model_id` int(11) DEFAULT NULL,
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
    `sent_email_response` LONGTEXT DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB AUTO_INCREMENT=1;



CREATE TABLE IF NOT EXISTS sendgrid_email_tracker_events (
    `id` int(20) NOT NULL AUTO_INCREMENT,
    `sendgrid_email_tracker_id` int(11) NOT NULL,
    `event_name` varchar(100) NOT NULL,
    `event_responses` text DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB AUTO_INCREMENT=1;