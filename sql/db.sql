CREATE TABLE IF NOT EXISTS `mc_tabspanel` (
  `id_tp` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module_tp` varchar(25) NOT NULL DEFAULT 'product',
  `id_module` int(11) DEFAULT NULL,
  `order_tp` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_tp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mc_tabspanel_content` (
  `id_content` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_tp` int(11) unsigned NOT NULL,
  `id_lang` smallint(3) unsigned NOT NULL,
  `tab_id_tp` varchar(125) NOT NULL,
  `title_tp` varchar(125) NOT NULL,
  `desc_tp` text,
  `published_tp` smallint(1) unsigned NOT NULL default 0,
  PRIMARY KEY (`id_content`),
  KEY `id_lang` (`id_lang`),
  KEY `id_tp` (`id_tp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mc_tabspanel_img` (
    `id_img` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_tp` int(11) UNSIGNED NOT NULL,
    `name_img` varchar(150) NOT NULL,
    `default_img` smallint(1) NOT NULL DEFAULT 0,
    `order_img` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_img`),
    KEY `id_tp` (`id_tp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `mc_tabspanel_content`
    ADD CONSTRAINT `mc_tabspanel_content_ibfk_1` FOREIGN KEY (`id_tp`) REFERENCES `mc_tabspanel` (`id_tp`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `mc_tabspanel_content_ibfk_2` FOREIGN KEY (`id_lang`) REFERENCES `mc_lang` (`id_lang`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `mc_tabspanel_img`
    ADD CONSTRAINT `mc_tabspanel_img_ibfk_1` FOREIGN KEY (`id_tp`) REFERENCES `mc_tabspanel` (`id_tp`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO `mc_config_img` (`id_config_img`, `module_img`, `attribute_img`, `width_img`, `height_img`, `type_img`, `prefix_img`, `resize_img`) VALUES
  (null, 'tabspanel', 'tabspanel', '208', '208', 'small', 's', 'adaptive'),
  (null, 'tabspanel', 'tabspanel', '408', '408', 'medium', 'm', 'adaptive'),
  (null, 'tabspanel', 'tabspanel', '1000', '1000', 'large', 'l', 'basic');