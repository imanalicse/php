<table>
	<tr>
		<td>$ </td>
		<td> </td>
	</tr>
</table>
<?php
 ALTER TABLE `j1fdu_wa_services` ENGINE = InnoDB 
 
 ALTER TABLE `j1fdu_wa_services` ADD FOREIGN KEY ( `car_id` ) REFERENCES `airportbus`.`j1fdu_wa_cars` (
`id`
) ON DELETE CASCADE ;


// show table status of a database
SHOW TABLE STATUS FROM airportbus;

alter table j1fdu_wa_orders_customters drop foreign key `j1fdu_wa_orders_customters_ibfk_1`; 
ALTER TABLE `j1fdu_wa_orders_customters` DROP INDEX `booking_id` 
ALTER TABLE `j1fdu_wa_orders_customters` CHANGE `booking_id` `order_id` INT( 11 ) UNSIGNED NOT NULL 
 ALTER TABLE `j1fdu_wa_orders_customters` ADD INDEX ( `order_id` ) 
 
 ALTER TABLE `j1fdu_wa_orders_customters` ADD FOREIGN KEY ( `order_id` ) REFERENCES `airportbus`.`j1fdu_wa_orders` (`id`) ON DELETE CASCADE ;
 
 // complex query
 SELECT COUNT(CASE WHEN  hour > -1 THEN '1' END) AS tot, COUNT(CASE WHEN  hour > 10 THEN '2' END) AS greater_ten FROM j1fdu_wa_services_time