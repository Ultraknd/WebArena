drop table if exists `nextpay_l2_order`;
 CREATE TABLE `nextpay_l2_order` (
  `order_id` int(11) NOT NULL default '0',
  `date_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `product_id` int(11) NOT NULL default '0',
  `volute` int(11) NOT NULL default '0',
  `product_count` int(11) NOT NULL default '0',
  `server` int(11) NOT NULL default '0',
  `char_name` varchar(255) NOT NULL default '',
  `profit` float NOT NULL default '0',
  `comment` varchar(255) default NULL,
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`order_id`)
) ENGINE=MyISAM;
