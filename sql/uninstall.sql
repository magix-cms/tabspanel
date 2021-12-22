TRUNCATE TABLE `mc_tabspanel_img`;
DROP TABLE `mc_tabspanel_img`;
TRUNCATE TABLE `mc_tabspanel_content`;
DROP TABLE `mc_tabspanel_content`;
TRUNCATE TABLE `mc_tabspanel`;
DROP TABLE `mc_tabspanel`;

DELETE FROM `mc_config_img` WHERE `module_img` = 'plugins' AND `attribute_img` = 'tabspanel';

DELETE FROM `mc_admin_access` WHERE `id_module` IN (
    SELECT `id_module` FROM `mc_module` as m WHERE m.name = 'tabspanel'
);