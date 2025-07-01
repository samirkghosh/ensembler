CREATE TABLE IF NOT EXISTS `tbl_im_new` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` varchar(255) NOT NULL,
  `to_id` varchar(255) NOT NULL,
  `msgTime` datetime(5) NOT NULL,
  `msg` varchar(1000) NOT NULL,
  `status` int(10) NOT NULL,
  `notify` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4;

ALTER TABLE `tbl_im_new`
ADD PRIMARY KEY (`msg_id`);