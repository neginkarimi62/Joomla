DROP TABLE IF EXISTS `#__rbdigital_subs`;

CREATE TABLE IF NOT EXISTS `#__rbdigital_subs` (
  `id` int(11) NOT NULL,
  `libid` varchar(32) DEFAULT NULL,
  `has_activated` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`,`libid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
