CREATE TABLE `students` (
	`id` INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) NULL DEFAULT NULL,
	`student_id` VARCHAR(35) NOT NULL,
	`first_name` VARCHAR(100) NULL DEFAULT NULL,
	`last_name` VARCHAR(100) NULL DEFAULT NULL,
	`email_address` VARCHAR(100) NULL DEFAULT NULL,
	`address_line_1` VARCHAR(250) NULL DEFAULT NULL,
	`address_line_2` VARCHAR(250) NULL DEFAULT NULL,
	`dob` VARCHAR(10) NULL DEFAULT NULL,
	`created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `import_mapper` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `db_field` varchar(255) NOT NULL,
  `excel_field_label` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `imported_files` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;