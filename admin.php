<?php
require_once ('db.php');
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The tabspanelContent management system optimized for users
 # Copyright (C) 2008 - 2021 magix-cms.com <support@magix-cms.com>
 #
 # OFFICIAL TEAM :
 #
 #   * Aurelien Gerits (Author - Developer) <aurelien@magix-cms.com> <contact@aurelien-gerits.be>
 #
 # Redistributions of files must retain the above copyright notice.
 # This program is free software: you can redistribute it and/or modify
 # it under the terms of the GNU General Public License as published by
 # the Free Software Foundation, either version 3 of the License, or
 # (at your option) any later version.
 #
 # This program is distributed in the hope that it will be useful,
 # but WITHOUT ANY WARRANTY; without even the implied warranty of
 # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 # GNU General Public License for more details.
 #
 # You should have received a copy of the GNU General Public License
 # along with this program.  If not, see <http://www.gnu.org/licenses/>.
 #
 # -- END LICENSE BLOCK -----------------------------------
 #
 # DISCLAIMER
 #
 # Do not edit or add to this file if you wish to upgrade MAGIX CMS to newer
 # versions in the future. If you wish to customize MAGIX CMS for your
 # needs please refer to http://www.magix-cms.com for more information.
 */
/**
 * @category plugins
 * @package tabspanel
 * @copyright  MAGIX CMS Copyright (c) 2011 - 2013 Gerits Aurelien, http://www.magix-dev.be, http://www.magix-cms.com
 * @license Dual licensed under the MIT or GPL Version 3 licenses.
 * @version 2.0
 * @create 26-08-2011
 * @Update 12-04-2021
 * @author Gérits Aurélien <contact@magix-dev.be>
 * @name plugins_tabspanel_admin
 */
class plugins_tabspanel_admin extends plugins_tabspanel_db {
    /**
     * @var backend_model_template $template
     * @var backend_model_data $data
     */
    protected backend_model_template $template;
    protected backend_model_data $data;

	/**
	 * @param backend_model_template|null $t
	 */
    public function __construct(?backend_model_template $t = null) {
        $this->template = $t instanceof backend_model_template ? $t : new backend_model_template;
		$this->data = new backend_model_data($this);
	}

	/**
	 * Method to override the name of the plugin in the admin menu
	 * @return string
	 */
	public function getExtensionName(): string {
		return $this->template->getConfigVars('tabspanel_plugin');
	}
}