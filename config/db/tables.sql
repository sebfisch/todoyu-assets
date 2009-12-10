--
-- Table structure for table `ext_assets_asset`
--

CREATE TABLE `ext_assets_asset` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_parent` int(10) unsigned NOT NULL DEFAULT '0',
  `parenttype` tinyint(1) NOT NULL,
  `date_update` int(10) unsigned NOT NULL DEFAULT '0',
  `id_user_create` smallint(5) unsigned NOT NULL DEFAULT '0',
  `date_create` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `file_ext` varchar(10) NOT NULL,
  `file_storage` varchar(100) NOT NULL,
  `file_name` varchar(100) NOT NULL,
  `file_size` int(10) unsigned NOT NULL DEFAULT '0',
  `id_old_version` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `file_mime` varchar(20) NOT NULL,
  `file_mime_sub` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`id_parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
