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
 * @category plugins
 * @package tabspanel
 * @copyright  MAGIX CMS Copyright (c) 2011 - 2013 Gerits Aurelien, http://www.magix-dev.be, http://www.magix-cms.com
 * @license Dual licensed under the MIT or GPL Version 3 licenses.
 * @version 2.0
 * @create 26-08-2011
 * @Update 12-04-2021
 * @author Gérits Aurélien <contact@magix-dev.be>
 * @name plugins_tp_admin
 */
class plugins_tabspanel_admin extends plugins_tabspanel_db
{
    /**
     * @var object
     */
    protected
        $controller,
        $message,
        $template,
        $plugins,
        $modelLanguage,
        $collectionLanguage,
        $data,
        $header,
        $upload,
        $imagesComponent,
        $routingUrl,
        $finder,
        $makeFiles,$progress;

	/**
	 * @var string
	 */
	public
        $getlang,
        $action,
        $tab,
        $img,
        $img_multiple,$order_img,$id_img;

	/**
	 * @var int
	 */
	public
        $edit,
        $id,$content;

	/**
	 * @var array
	 */
	public
        $tabspanel = [];

    /**
     * plugins_tp_admin constructor.
     */
    /**
     * frontend_controller_home constructor.
     */
    public function __construct($t = null)
    {
        $this->template = $t ? $t : new backend_model_template;
        $this->message = new component_core_message($this->template);
		$this->plugins = new backend_controller_plugins();
		$this->modelLanguage = new backend_model_language($this->template);
		$this->collectionLanguage = new component_collections_language();
		$this->data = new backend_model_data($this);
		$this->header = new http_header();
		$this->upload = new component_files_upload();
		$this->imagesComponent = new component_files_images($this->template);
		$this->routingUrl = new component_routing_url();
        $this->finder = new file_finder();
        $this->makeFiles = new filesystem_makefile();
		$formClean = new form_inputEscape();

		// --- Get
		if (http_request::isGet('controller')) $this->controller = $formClean->simpleClean($_GET['controller']);
        if($this->controller === 'tabspanel') $this->controller = 'product';
		if (http_request::isGet('edit')) $this->edit = $formClean->numeric($_GET['edit']);
		if (http_request::isRequest('action')) $this->action = $formClean->simpleClean($_REQUEST['action']);
		if (http_request::isGet('tabs')) $this->tab = $formClean->simpleClean($_GET['tabs']);

		// --- Post
        if (http_request::isPost('content')) {
            $array = $_POST['content'];
            foreach($array as $key => $arr) {
                foreach($arr as $k => $v) {
                    $array[$key][$k] = ($k == 'desc_tp') ? $formClean->cleanQuote($v) : $formClean->simpleClean($v);
                }
            }
            $this->content = $array;
        }

		if (http_request::isPost('id')) $this->id = $formClean->simpleClean($_POST['id']);
		// --- Image Upload
        if (isset($_FILES['img_multiple']["name"])) $this->img_multiple = ($_FILES['img_multiple']["name"]);
        if (http_request::isPost('id_img')) $this->id_img = $formClean->simpleClean($_POST['id_img']);
		// --- Order
		if (http_request::isPost('tabspanel')) $this->tabspanel = $formClean->arrayClean($_POST['tabspanel']);
        if (http_request::isPost('image')) $this->order_img = $formClean->arrayClean($_POST['image']);
	}

	/**
	 * Method to override the name of the plugin in the admin menu
	 * @return string
	 */
	public function getExtensionName(): string
	{
		return $this->template->getConfigVars('tabspanel_plugin');
	}

	// --- Database actions

	/**
	 * Assign data to the defined variable or return the data
	 * @param string $type
	 * @param string|int|null $id
	 * @param string $context
	 * @param boolean $assign
	 * @return false|null|array
	 */
	protected function getItems(string $type, $id = null, $context = null, $assign = true)
    {
		return $this->data->getItems($type, $id, $context, $assign);
	}

    /**
     * Insert data
     * @param array $config
     */
    protected function add(array $config)
    {
        switch ($config['type']) {
            case 'tabspanel':
            case 'tabspanelContent':
            case 'newImg':
                parent::insert(
                    ['type' => $config['type']],
                    $config['data']
                );
                break;
        }
    }

    /**
     * Update data
     * @param array $config
     */
    protected function upd(array $config)
    {
        switch ($config['type']) {
            case 'img':
            case 'tabspanel':
            case 'tabspanelContent':
            case 'order':
            case 'firstImageDefault':
                parent::update(
                    ['type' => $config['type']],
                    $config['data']
                );
                break;
            case 'imageDefault':
                parent::update(
                    array(
                        'type' => 'imageDefault'
                    ),
                    $config['data']
                );
                $this->message->json_post_response(true,'update');
                break;
            case 'order_img':
                $p = $this->order_img;
                for ($i = 0; $i < count($p); $i++) {
                    parent::update(
                        array(
                            'type' => $config['type']
                        ),
                        array(
                            'id_img'       => $p[$i],
                            'order_img'    => $i
                        )
                    );
                }
                break;
        }
    }

    /**
     * Delete a record
     * @param array $data
     */
    protected function del(array $data)
    {
        switch ($data['type']) {
            case 'tabspanel':
                parent::delete(
                    ['type' => $data['type']],
                    $data['data']
                );
                $this->message->json_post_response(true,'delete',array('id' => $this->id));
                break;
            case 'delImages':
                $makeFiles = new filesystem_makefile();
                $newArr = array();
                $imgArray = explode(',',$data['data']['id']);
                $fetchConfig = $this->imagesComponent->getConfigItems(array('module_img'=>'plugins','attribute_img'=>'tabspanel'));
                $imgPrefix = $this->imagesComponent->prefix();
                $defaultErased = false;
                $id_pages = false;
                $extwebp = 'webp';

                foreach($imgArray as $key => $value){
                    $img = $this->getItems('img',$value,'one',false);
                    $id_pages = $img['id_tp'];
                    if($img['default_img']) $defaultErased = true;

                    if(isset($id_pages) && $id_pages != '') {
                        $imgPath = $this->upload->dirFileUpload(
                            array_merge(
                                array(
                                    'upload_root_dir' => 'upload/tabspanel',
                                    'upload_dir' => $img['id_tp'])
                                , array(
                                    'fileBasePath' => true
                                )
                            )
                        );

                        $newArr[$key]['img']['original'] = $imgPath . $img['name_img'];
                        if (file_exists($newArr[$key]['img']['original'])) {
                            $makeFiles->remove(array(
                                $newArr[$key]['img']['original']
                            ));
                        }
                        foreach ($fetchConfig as $configKey => $confiValue) {
                            $newArr[$key]['img'][$confiValue['type_img']] = $imgPath . $imgPrefix[$confiValue['type_img']] . $img['name_img'];
                            $imgData = pathinfo($img['name_img']);
                            $filename = $imgData['filename'];

                            if (file_exists($newArr[$key]['img'][$confiValue['type_img']])) {
                                $makeFiles->remove(array(
                                    $newArr[$key]['img'][$confiValue['type_img']]
                                ));
                            }
                            // Check if the image with webp extension exist
                            if (file_exists($imgPath . $imgPrefix[$confiValue['type_img']] . $filename . '.' . $extwebp)) {
                                $makeFiles->remove(array(
                                    $imgPath . $imgPrefix[$confiValue['type_img']] . $filename . '.' . $extwebp
                                ));
                            }
                        }
                    }
                }

                if($newArr && isset($data['data']['id'])) {
                    parent::delete(
                        array(
                            'type' => $data['type']
                        ),
                        $data['data']
                    );
                    $id_pages = $data['data']['id'];
                    $imgs = $this->getItems('images',$id_pages,'all',false);
                    if($imgs != null && $defaultErased) {
                        $this->upd(array(
                            'type' => 'firstImageDefault',
                            'data' => array(
                                ':id' => $id_pages
                            )
                        ));
                    }
                }
                break;
        }
    }

    // ---

    /**
     * @param $type
     */
    protected function order($type){
        /*switch ($type) {
            case 'home':
                for ($i = 0; $i < count($this->tabspanel); $i++) {
                    $this->upd(['type' => 'order', 'data' => ['id_tp' => $this->tabspanel[$i], 'order_tp' => $i]]);
                }
                break;
        }*/
        for ($i = 0; $i < count($this->tabspanel); $i++) {
            $this->upd(['type' => 'order', 'data' => ['id_tp' => $this->tabspanel[$i], 'order_tp' => $i]]);
        }
    }

	/**
	 * @param array $data
	 * @return array
	 */
	protected function setItemtabspanelData(array $data): array
	{
		$arr = [];
		if(!empty($data)) {
            foreach ($data as $tabspanel) {
                if (!array_key_exists($tabspanel['id_tp'], $arr)) {
                    $arr[$tabspanel['id_tp']] = [];
                    $arr[$tabspanel['id_tp']]['id_tp'] = $tabspanel['id_tp'];
                }

                $arr[$tabspanel['id_tp']]['content'][$tabspanel['id_lang']] = [
                    'id_lang' => $tabspanel['id_lang'],
                    'title_tp' => $tabspanel['title_tp'],
                    'desc_tp' => $tabspanel['desc_tp'],
                    'published_tp' => $tabspanel['published_tp']
                ];
            }
        }
		return $arr;
	}
}