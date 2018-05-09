create table t1(
        id      serial      primary key,
        data    text
);
CREATE TABLE  `t2` (
  `id` bigint(20) unsigned NOT NULL,
  `data2` text,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_t2_1` FOREIGN KEY (`id`) REFERENCES `t1` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

insert into t1 values( 1, 'one' );
insert into t2 values( 1, 'first' );

CREATE TABLE IF NOT EXISTS `wp_popularpostsdata` (
  `post_id` int(10) NOT NULL,
  `view_count` int(10) DEFAULT '1',
  `last_viewed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `product_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_product_image_1` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `wp_popularpostsdata` (
  `post_id` int(10) NOT NULL,
  `view_count` int(10) DEFAULT '1',
  `last_viewed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', 
   KEY `FK_wp_popularpostsdata_1` (`post_id`)
   CONSTRAINT `FK_ke_1` FOREIGN KEY (`post_id`) REFERENCES `wp_posts` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_image`
--
ALTER TABLE `product_image`
  ADD CONSTRAINT `FK_product_image_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE;