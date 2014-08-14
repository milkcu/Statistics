CREATE TABLE `typecho_visit_logs` (
  `vlid` int(10) unsigned NOT NULL auto_increment,
  `vagent` varchar(200) default NULL,
  `vurl` varchar(100) default NULL,
  `furl` varchar(100) default NULL,
  `vip` varchar(16) default NULL,
  `vtime` int(10) unsigned default '0',
  PRIMARY KEY  (`vlid`)
) ENGINE=MYISAM  DEFAULT CHARSET=%charset%;