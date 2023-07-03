<?php
require_once ('db.php');
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2021 magix-cms.com <support@magix-cms.com>
 #
 # OFFICIAL TEAM :
 #
 #   * Gerits Aurelien (Author - Developer) <aurelien@magix-cms.com> <contact@aurelien-gerits.be>
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
 * @category plugin
 * @package banner
 * @copyright MAGIX CMS Copyright (c) 2011 Gerits Aurelien, http://www.magix-dev.be, http://www.magix-cms.com
 * @license Dual licensed under the MIT or GPL Version 3 licenses.
 * @version 1.0
 * @create 20-12-2021
 * @author Aurélien Gérits <aurelien@magix-cms.com>
 * @name plugins_tabspanel_public
 */
class plugins_tabspanel_public extends plugins_tabspanel_db {
    /**
     * @var object
     */
    protected
        $template,
        $data,
        $imagesComponent;

    /**
     * @var string
     */
    protected
        $getlang;

    /**
     * plugins_banner_public constructor.
     * @param frontend_model_template|null $t
     */
    public function __construct($t = null)
    {
        $this->template = $t instanceof frontend_model_template ? $t : new frontend_model_template();
        $this->data = new frontend_model_data($this,$this->template);
        $this->getlang = $this->template->lang;
        $this->imagesComponent = new component_files_images($this->template);
    }

    /**
     * Assign data to the defined variable or return the data
     * @param string $type
     * @param string|int|null $id
     * @param string $context
     * @param boolean $assign
     * @return false|null|array
     */
    private function getItems(string $type, $id = null, $context = null, $assign = true)
    {
        return $this->data->getItems($type, $id, $context, $assign);
    }

	/**
	 * @return void
	 */
	private function initImageComponent() {
		if(!isset($this->imagesComponent)) $this->imagesComponent = new component_files_images($this->template);
	}

    public function setItemData($row,$current)
    {
        $string_format = new component_format_string();
        $data = [];
        $extwebp = 'webp';
        //if (!isset($this->imagePlaceHolder)) $this->imagePlaceHolder = $this->logo->getImagePlaceholder();

        if ($row != null) {
			$this->initImageComponent();

            if (isset($row['name'])) {
                $data['id'] = $row['id'];
                $data['tab_id'] = $row['tab_id'];
                $data['name'] = $row['name'];
                $data['content'] = $row['content'];
                //$data['iso'] = $row['iso_lang'];

                /*$data['active'] = false;

                if ($row['id_tp'] == $current['controller']['id']) {
                    $data['active'] = true;
                }*/

                if (isset($row['img'])) {
                    //$imgPrefix = $this->imagesComponent->prefix();
                    $fetchConfig = $this->imagesComponent->getConfigItems('tabspanel','tabspanel');

                    if (is_array($row['img'])) {
                        foreach ($row['img'] as $val) {
                            // # return filename without extension
                            //$pathinfo = pathinfo($val['name_img']);
                            //$filename = $pathinfo['filename'];

                            /*$data['imgs'][$item]['img']['alt'] = $val['alt_img'];
                            $data['imgs'][$item]['img']['title'] = $val['title_img'];
                            $data['imgs'][$item]['img']['caption'] = $val['caption_img'];*/
                            /*$data['imgs'][$item]['img']['name'] = $val['name_img'];
                            foreach ($fetchConfig as $key => $value) {
                                $imginfo = $this->imagesComponent->getImageInfos(component_core_system::basePath() . '/upload/tabspanel/' . $val['id_tp'] . '/' . $imgPrefix[$value['type_img']] . $val['name_img']);
                                $data['imgs'][$item]['img'][$value['type_img']]['src'] = '/upload/tabspanel/' . $val['id_tp'] . '/' . $imgPrefix[$value['type_img']] . $val['name_img'];
                                if (file_exists(component_core_system::basePath() . '/upload/tabspanel/' . $val['id_tp'] . '/' . $imgPrefix[$value['type_img']] . $filename . '.' . $extwebp)) {
                                    $data['imgs'][$item]['img'][$value['type_img']]['src_webp'] = '/upload/tabspanel/' . $val['id_tp'] . '/' . $imgPrefix[$value['type_img']] . $filename . '.' . $extwebp;
                                }
                                $data['imgs'][$item]['img'][$value['type_img']]['crop'] = $value['resize_img'];
                                //$data['imgs'][$item]['img'][$value['type_img']]['w'] = $value['width_img'];
                                $data['imgs'][$item]['img'][$value['type_img']]['w'] = $value['resize_img'] === 'basic' ? $imginfo['width'] : $value['width_img'];
                                //$data['imgs'][$item]['img'][$value['type_img']]['h'] = $value['height_img'];
                                $data['imgs'][$item]['img'][$value['type_img']]['h'] = $value['resize_img'] === 'basic' ? $imginfo['height'] : $value['height_img'];
                                $data['imgs'][$item]['img'][$value['type_img']]['ext'] = mime_content_type(component_core_system::basePath() . '/upload/tabspanel/' . $val['id_tp'] . '/' . $imgPrefix[$value['type_img']] . $val['name_img']);
                            }
                            $data['imgs'][$item]['default'] = $val['default_img'];*/

							$image = $this->imagesComponent->setModuleImage('tabspanel','tabspanel',$val['name_img'],$val['id_tp']);
							if($val['default_img']) {
								$data['img'] = $image;
								$image['default'] = 1;
							}
							$data['imgs'][] = $image;
                        }
						$data['img']['default'] = $this->imagesComponent->setModuleImage('tabspanel','tabspanel');
                    }
                }
            }
            return $data;
        }
    }

    /**
     * @param $params
     * @return array
     */
    public function getBuildPagesItems($params = []){
        $modelSystem = new frontend_model_core($this->template);
        $current = $modelSystem->setCurrentId();
        $newdata = array();
        $collection = $this->getItems('active',array('module_tp' => $params['controller'] ,'id_module' => $current['controller']['id'], 'iso' => $this->getlang),'all',false);
        foreach($collection as $key => $value){
            $imgCollection = $this->getItems('images', ['id' => $value['id_tp']], 'all', false);
            $newdata[$key]['id'] = $value['id_tp'];
            $newdata[$key]['tab_id'] = $value['tab_id_tp'];
            $newdata[$key]['name'] = $value['title_tp'];
            $newdata[$key]['content'] = $value['desc_tp'];
            $newdata[$key]['img'] = $imgCollection;
        }
        foreach ($newdata as &$item) {
            $newarr[] = $this->setItemData($item,null);
        }
        return $newarr;
    }
}