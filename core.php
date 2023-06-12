<?php
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
 * @name plugins_tabspanel_core
 */
class plugins_tabspanel_core extends plugins_tabspanel_admin {
    /**
	 * @var backend_controller_plugins $plugins;
	 * @var backend_model_plugins $modelPlugins;
	 * @var component_core_message $message;
	 * @var backend_model_language $modelLanguage;
	 * @var component_collections_language $collectionLanguage;
	 * @var component_files_upload $upload;
	 * @var component_files_images $imagesComponent;
	 * @var component_routing_url $routingUrl;
	 * @var file_finder $finder;
	 * @var filesystem_makefile $makeFiles;
	 * @var component_core_feedback $progress;
     */
	protected backend_controller_plugins $plugins;
	protected backend_model_plugins $modelPlugins;
	protected component_core_message $message;
	protected backend_model_language $modelLanguage;
	protected component_collections_language $collectionLanguage;
	protected component_files_upload $upload;
	protected component_files_images $imagesComponent;
	protected component_routing_url $routingUrl;
	protected file_finder $finder;
	protected filesystem_makefile $makeFiles;
	protected component_core_feedback $progress;

	/**
	 * @var string $controller
	 */
	protected string $controller;

    /**
     * @var string $mod_action
     * @var string $plugin
     * @var string $action
     * @var string $tab
     * @var string $img
     * @var string $img_multiple
     * @var string $order_img
     */
    public string
        $mod_action,
        $plugin,
		$action,
		$tab,
		$img,
		$img_multiple;

	/**
	 * @var int|string $id
	 */
	public $id;

	/**
	 * @var int $edit
	 * @var int $mod_edit
	 * @var int $id_img
	 */
	public int
		$edit,
		$mod_edit,
		$id_img;

	/**
	 * @var array $content
	 * @var array $tabspanel
	 * @var array $order_img
	 */
	public array
		$content,
		$tabspanel,
		$order_img;

	/**
	 *
	 */
    public function __construct() {
        parent::__construct();
		$this->plugins = new backend_controller_plugins();
        $this->modelPlugins = new backend_model_plugins();
		$this->message = new component_core_message($this->template);
		$this->modelLanguage = new backend_model_language($this->template);
		$this->collectionLanguage = new component_collections_language();
		$this->upload = new component_files_upload();
		$this->imagesComponent = new component_files_images($this->template);
		$this->routingUrl = new component_routing_url();
		$this->finder = new file_finder();
		$this->makeFiles = new filesystem_makefile();

		if (http_request::isRequest('action')) $this->action = form_inputEscape::simpleClean($_REQUEST['action']);
		if (http_request::isRequest('mod_action')) $this->mod_action = form_inputEscape::simpleClean($_REQUEST['mod_action']);

		// --- Get
		if (http_request::isGet('controller')) $this->controller = form_inputEscape::simpleClean($_GET['controller']);
		if (http_request::isGet('plugin')) $this->plugin = form_inputEscape::simpleClean($_GET['plugin']);
		if (http_request::isGet('tabs')) $this->tab = form_inputEscape::simpleClean($_GET['tabs']);
		if (http_request::isGet('edit')) $this->edit = form_inputEscape::numeric($_GET['edit']);
		if (http_request::isGet('mod_edit')) $this->mod_edit = form_inputEscape::numeric($_GET['mod_edit']);

		// --- Post
		if (http_request::isPost('id')) $this->id = form_inputEscape::simpleClean($_POST['id']);
		if (http_request::isPost('content')) {
			$array = $_POST['content'];
			/*foreach($array as $key => $arr) {
				foreach($arr as $k => $v) {
					$array[$key][$k] = ($k == 'desc_tp') ? form_inputEscape::cleanQuote($v) : form_inputEscape::simpleClean($v);
				}
			}*/
			array_walk_recursive($array,function(&$value,$key){
				$value = ($key == 'desc_tp') ? form_inputEscape::cleanQuote($value) : form_inputEscape::simpleClean($value);
			});
			$this->content = $array;
		}

		// --- Image Upload
		if (isset($_FILES['img_multiple']["name"])) $this->img_multiple = ($_FILES['img_multiple']["name"]);
		if (http_request::isPost('id_img')) $this->id_img = form_inputEscape::simpleClean($_POST['id_img']);

		// --- Order
		if (http_request::isPost('tabspanel')) $this->tabspanel = form_inputEscape::arrayClean($_POST['tabspanel']);
		if (http_request::isPost('image')) $this->order_img = form_inputEscape::arrayClean($_POST['image']);
    }

	// --- Database actions
	/**
	 * Assign data to the defined variable or return the data
	 * @param string $type
	 * @param array|int|null $id
	 * @param string|null $context
	 * @param string|bool $assign
	 * @return false|null|array
	 */
	protected function getItems(string $type, $id = null, ?string $context = null, $assign = true) {
		return $this->data->getItems($type, $id, $context, $assign);
	}
	// ----------

	// --- Methods
	/**
	 * @param string $type
	 * @param array $order
	 * @return void
	 */
	protected function order(string $type, array $order) {
		for ($i = 0; $i < count($order); $i++) {
			$this->update($type, ['id' => $order[$i], 'order' => $i]);
		}
	}

	/**
	 * @param array $data
	 * @return array
	 */
	protected function setItemTabsPanelData(array $data): array {
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
	// ----------

	/**
	 * @return void
	 * @throws Exception
	 */
    public function run() {
        if(isset($this->controller)) {
            switch ($this->controller) {
                case 'about':
                    $extends = $this->controller.(!isset($this->action) ? '/index.tpl' : '/pages/edit.tpl');
                    break;
                case 'category':
                case 'product':
                    $extends = 'catalog/'.$this->controller.'/edit.tpl';
                    break;
                case 'news':
					$extends = $this->controller. (isset($this->edit) ? '/edit.tpl' : '/index.tpl');
					break;
                case 'catalog':
                    $extends = $this->controller.'/index.tpl';
                    break;
                case 'pages':
                    $extends = $this->controller.'/edit.tpl';
                    break;
                default:
                    $extends = 'index.tpl';
            }
            $this->template->assign('extends',$extends);
            if(isset($this->mod_action)) {
				if(http_request::isMethod('GET')) {
					switch ($this->mod_action) {
						case 'add':
						case 'edit':
							$this->modelLanguage->getLanguage();
							if(isset($this->mod_edit)) {
								$collection = $this->getItems('content',$this->mod_edit,'all',false);
								$setEditData = $this->setItemTabsPanelData($collection);
								$this->template->assign('tabspanel', $setEditData[$this->mod_edit]);
								// --- pages images
								$this->getItems('images', $this->mod_edit, 'all');
							}
							$this->template->assign('edit',$this->mod_action === 'edit');
							$this->modelPlugins->display('mod/edit.tpl');
							break;
						case 'getImgDefault':
							if (isset($this->mod_edit)) {
								$imgDefault = $this->getItems('imgDefault', $this->mod_edit, 'one', false);
								print $imgDefault['id_img'];
							}
							break;
						case 'getImages':
							if (isset($this->mod_edit)) {
								$this->getItems('images', $this->mod_edit, 'all');
								$display = $this->modelPlugins->fetch('brick/img.tpl');
								$this->message->json_post_response(true, '', $display);
							}
							break;
					}
				}
				if(http_request::isMethod('POST')) {
					switch ($this->mod_action) {
						case 'add':
						case 'edit':
							if(!empty($this->content) ) {
								$notify = 'update';

								if (!isset($this->mod_edit)) {
									$this->insert('tabspanel',[
										'module' => $this->controller,
										'id_module' => $this->edit ?: NULL
									]);

									$lastTabspanel = $this->getItems('lastTabspanel', null,'one',false);
									$this->mod_edit = $lastTabspanel['id_tp'];
									$notify = 'add_redirect';
								}

								foreach ($this->content as $lang => $tabspanel) {
									$tabspanel['id_tp'] = $this->mod_edit;
									$tabspanel['id_lang'] = $lang;
									$tabspanel['published_tp'] = (int)isset($tabspanel['published_tp']);
									$tabspanel['title_tp'] = (!empty($tabspanel['title_tp']) ? $tabspanel['title_tp'] : NULL);
									$tabspanel['desc_tp'] = (!empty($tabspanel['desc_tp']) ? $tabspanel['desc_tp'] : NULL);
									$tabspanelLang = $this->getItems('content',['id' => $this->mod_edit,'id_lang' => $lang],'one',false);

									!empty($tabspanelLang) ? $this->update('content',$tabspanel) : $this->insert('content',$tabspanel);
								}
								$this->message->json_post_response(true,$notify);
							}
							elseif (isset($this->img_multiple)) {
								$this->template->configLoad();
								$this->progress = new component_core_feedback($this->template);

								usleep(200000);
								$this->progress->sendFeedback(array('message' => $this->template->getConfigVars('control_of_data'), 'progress' => 30));

								$defaultLanguage = $this->collectionLanguage->fetchData(array('context' => 'one', 'type' => 'default'));
								$page = $this->getItems('content', ['id' => $this->id, 'id_lang' => $defaultLanguage['id_lang']], 'one', false);
								$lastIndex = $this->getItems('lastImgId', ['id_tp' => $this->id], 'one', false);
								$lastIndex['index'] = $lastIndex['index'] ?? 0;

								$resultUpload = $this->upload->multipleImageUpload(
									'tabspanel','tabspanel','upload/tabspanel',["$this->id"],[
									'name' => http_url::clean($page['title_tp']),
									'suffix' => (int)$lastIndex['index'],
									'suffix_increment' => true,
									'progress' => $this->progress,
									'template' => $this->template
								]);

								if (!empty($resultUpload)) {
									$totalUpload = count($resultUpload);
									$percent = $this->progress->progress;
									$preparePercent = (90 - $percent) / $totalUpload;
									$i = 1;

									foreach ($resultUpload as $value) {
										if ($value['status']) {
											$percent = $percent + $preparePercent;

											usleep(200000);
											$this->progress->sendFeedback(['message' => sprintf($this->template->getConfigVars('creating_records'),$i,$totalUpload), 'progress' => $percent]);

											$this->insert('img',[
												'id_tp' => $this->id,
												'name_img' => $value['file']
											]);
										}
										$i++;
									}

									usleep(200000);
									$this->progress->sendFeedback(array('message' => $this->template->getConfigVars('creating_thumbnails_success'), 'progress' => 90));

									usleep(200000);
									$this->progress->sendFeedback(array('message' => $this->template->getConfigVars('upload_done'), 'progress' => 100, 'status' => 'success'));
								}
								else {
									usleep(200000);
									$this->progress->sendFeedback(array('message' => $this->template->getConfigVars('creating_thumbnails_error'), 'progress' => 100, 'status' => 'error', 'error_code' => 'error_data'));
								}
							}
							break;
						case 'setImgDefault':
							if (isset($this->id_img)) {
								$this->update('imageDefault',['id' => $this->mod_edit, 'id_img' => $this->id_img]);
								$this->message->json_post_response(true,'update');
							}
							break;
						case 'order':
							if (isset($this->tabspanel)) $this->order('order',$this->tabspanel);
							break;
						case 'orderImages':
							if (isset($this->order_img)) $this->order('orderImages',$this->order_img);
							break;
						case 'delete':
							if (isset($this->id)) {
								$makeFiles = new filesystem_makefile();
								$imgArray = explode(',',$this->id);

								if (isset($this->tabs) && $this->tabs === 'image') {
									$fetchConfig = $this->imagesComponent->getConfigItems('tabspanel','tabspanel');
									$defaultErased = false;
									$id_tp = false;
									$extWebp = 'webp';
									// Array of images to erased at the end
									$toRemove = [];

									foreach($imgArray as $value) {
										$img = $this->getItems('img',$value,'one',false);

										if(!empty($img) && !empty($img['id_tp']) && !empty($img['name_img'])) {
											// Get the tab's id
											$id_tp = $img['id_tp'];
											// Check if it's the default image that's going to be erased
											if($img['default_img']) $defaultErased = true;
											// Concat the image directory path
											$imgPath = $this->routingUrl->dirUpload('upload/tabspanel/'.$img['id_tp'],true);

											// Original file of the image
											$original = $imgPath.$img['name_img'];
											if(file_exists($original)) $toRemove[] = $original;

											foreach ($fetchConfig as $configValue) {
												$image = $imgPath.$configValue['prefix'].'_'.$img['name_img'];
												if(file_exists($image)) $toRemove[] = $image;

												// Check if the image with webp extension exist
												$imgData = pathinfo($img['name_img']);
												$filename = $imgData['filename'];
												$webpImg = $imgPath.$configValue['prefix'].'_'.$filename.'.'.$extWebp;
												if(file_exists($webpImg)) $toRemove[] = $webpImg;
											}
										}
									}

									// If files had been found
									if(!empty($toRemove)) {
										// Erased images
										$makeFiles->remove($toRemove);

										// Remove from database
										$this->delete('images',['id' => $this->id]);

										// Count the remaining images
										$images = $this->getItems('countImages',$id_tp,'one',false);

										// If there is at least one image left and the default image has been erased, set the first remaining image as default
										if($images['tot'] > 0 && $defaultErased) {
											$this->update('firstImageDefault',['id' => $id_tp]);
										}
									}
									$this->message->json_post_response(true, 'delete', ['id' => $this->mod_edit]);
								}
								else {
									foreach($imgArray as $value){
										if(isset($value) && $value > 0) {
											$imgPath = $this->routingUrl->dirUpload('upload/tabspanel/'.$this->id,true);

											if(file_exists($imgPath)) {
												try {
													$makeFiles->remove(array($imgPath));
												} catch(Exception $e) {
													$logger = new debug_logger(MP_LOG_DIR);
													$logger->log('php', 'error', 'An error has occurred : '.$e->getMessage(), debug_logger::LOG_MONTH);
												}
											}
										}
									}

									$this->delete('tabspanel',['id' => $this->id]);
									$this->message->json_post_response(true,'delete',['id' => $this->id]);
								}
							}
							break;
					}
				}
            }
            else {
				$this->modelLanguage->getLanguage();
				$defaultLanguage = $this->collectionLanguage->fetchData(['context'=>'one','type'=>'default']);
				$this->getItems('tabspanel',['lang' => $defaultLanguage['id_lang'], 'module' => $this->controller, 'id_module' => $this->edit ?: NULL],'all');
				$assign = [
					'id_tp',
					'default_img' => ['title' => 'name','type' => 'bin', 'input' => null, 'class' => ''],
					'title_tp' => ['title' => 'name'],
					'desc_tp' => ['title' => 'name','type' => 'bin', 'input' => null]
				];
				$this->data->getScheme(['mc_tabspanel','mc_tabspanel_content','mc_tabspanel_img'], ['id_tp','title_tp','desc_tp','default_img'], $assign);
				$this->modelPlugins->display('mod/index.tpl');
            }
        }
    }
}