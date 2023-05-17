<?php
/**
 * Class plugins_test_core
 * Fichier pour les plugins core
 */
class plugins_tabspanel_core extends plugins_tabspanel_admin
{
    /**
     * @var object
     */
    protected
        $modelPlugins,
        $plugins;

    /**
     * @var int
     */
    public
        $mod_edit;

    /**
     * @var string
     */
    public
        $mod_action,
        $plugin;

    /**
     * plugins_banner_core constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->modelPlugins = new backend_model_plugins();
        $this->plugins = new backend_controller_plugins();
        $formClean = new form_inputEscape();

        if (http_request::isGet('plugin')) $this->plugin = $formClean->simpleClean($_GET['plugin']);
        if (http_request::isRequest('mod_action')) $this->mod_action = $formClean->simpleClean($_REQUEST['mod_action']);
        if (http_request::isGet('mod_edit')) $this->mod_edit = $formClean->numeric($_GET['mod_edit']);
    }

    /**
     *
     */
    protected function runAction()
    {
        switch ($this->mod_action) {
            case 'add':
            case 'edit':
                if( isset($this->content) && !empty($this->content) ) {
                    $notify = 'update';
                    $img = '';

                    if (!isset($this->id)) {
                        $this->add([
                            'type' => 'tabspanel',
                            'data' => [
                                'module' => $this->controller,
                                'id_module' => $this->edit ?: NULL
                            ]
                        ]);

                        $lasttabspanel = $this->getItems('lastTabspanel', null,'one',false);
                        $this->id = $lasttabspanel['id_tp'];
                        $notify = 'add_redirect';
                    }

                    $formClean = new form_inputEscape();
                    foreach ($this->content as $lang => $tabspanel) {
                        $tabspanel['id_tp'] = $this->id;
                        $tabspanel['id_lang'] = $lang;
                        //$tabspanel['blank_banner'] = (!isset($tabspanel['blank_banner']) ? 0 : 1);
                        $tabspanel['published_tp'] = (!isset($tabspanel['published_tp']) ? 0 : 1);
                        $tabspanel['title_tp'] = (!empty($tabspanel['title_tp']) ? $tabspanel['title_tp'] : NULL);
                        $tabspanel['desc_tp'] = (!empty($tabspanel['desc_tp']) ? $tabspanel['desc_tp'] : NULL);
                        $tabspanelLang = $this->getItems('tabspanelContent',['id' => $this->id,'id_lang' => $lang],'one',false);

                        /*if($tabspanelLang) $tabspanel['id'] = $tabspanelLang['id_tabspanel_content'];
                        else $tabspanel['id_tp'] = $this->tabspanel['id'];*/
                        $config = ['type' => 'tabspanelContent', 'data' => $tabspanel];

                        $tabspanelLang ? $this->upd($config) : $this->add($config);
                    }
                    $this->message->json_post_response(true,$notify);
                }
                elseif (isset($this->img_multiple)) {;
                    $this->template->configLoad();
                    $this->progress = new component_core_feedback($this->template);

                    usleep(200000);
                    $this->progress->sendFeedback(array('message' => $this->template->getConfigVars('control_of_data'), 'progress' => 30));

                    $defaultLanguage = $this->collectionLanguage->fetchData(array('context' => 'one', 'type' => 'default'));
                    $page = $this->getItems('tabspanelContent', array('id' => $this->id, 'id_lang' => $defaultLanguage['id_lang']), 'one', false);
                    $newimg = $this->getItems('lastImgId', ['id_tp' => $this->id], 'one', false);
                    // If $newimg = NULL return 0
					$newimg['index'] = $newimg['index'] ?? 0;

                    /*$name = http_url::clean($page['title_tp']);
                    $resultUpload = $this->upload->setMultipleImageUpload(
                        'img_multiple',
                        array(
                            'name' => $name,
                            'prefix_name' => $newimg['id_img'],
                            'prefix_increment' => true,
                            'prefix' => array('s_', 'm_', 'l_'),
                            'module_img' => 'plugins',
                            'attribute_img' => 'tabspanel',
                            'original_remove' => false,
                            'progress' => $this->progress,
                            'template' => $this->template
                        ),
                        array(
                            'upload_root_dir' => 'upload/tabspanel', //string
                            'upload_dir' => $this->id //string ou array
                        ),
                        false
                    );*/

					$resultUpload = $this->upload->multipleImageUpload(
						'tabspanel','tabspanel','upload/tabspanel',["$this->id"],[
						'name' => http_url::clean($page['title_tp']),
						'suffix' => (int)$newimg['index'],
						'suffix_increment' => true,
						'progress' => $this->progress,
						'template' => $this->template
					],false);

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

                                $this->add(array(
                                    'type' => 'newImg',
                                    'data' => array(
                                        'id_tp' => $this->id,
                                        'name_img' => $value['file']
                                    )
                                ));
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
                else {
                    $this->modelLanguage->getLanguage();

                    if(isset($this->mod_edit)) {
                        $collection = $this->getItems('tabspanelContent',$this->mod_edit,'all',false);
                        $setEditData = $this->setItemtabspanelData($collection);
                        $this->template->assign('tabspanel', $setEditData[$this->mod_edit]);
                    }

                    $this->template->assign('edit',$this->mod_action === 'edit');
                    // --- pages images
                    $this->getItems('images', $this->mod_edit, 'all');
                    $this->modelPlugins->display('mod/edit.tpl');
                }
                break;
            case 'delete':
                if(isset($this->mod_edit)){
                    $this->del([
                        'type' => 'delImages',
                        'data' => ['id' => $this->id]
                    ]);
                    $this->message->json_post_response(true, 'delete', array('id' => $this->mod_edit));
                }
                /*if(isset($this->id) && !empty($this->id)) {
                    if($this->delete_image($this->id)) {
                        $this->del([
                            'type' => 'banner',
                            'data' => ['id' => $this->id]
                        ]);
                    }
                }*/

                /*
                $this->del(
                    array(
                        'type' => 'delImages',
                        'data' => array(
                            'id' => $this->id_pages
                        )
                    )
                );
                $this->message->json_post_response(true, 'delete', array('id' => $this->id_pages));
                */
                break;
            case 'order':
                if (isset($this->tabspanel) && is_array($this->tabspanel)) {
                    $this->order('product');
                }
                break;
            case 'setImgDefault':
                if (isset($this->id_img)) {
                    $this->upd(array(
                        'type' => 'imageDefault',
                        'data' => array(':id' => $this->mod_edit, ':id_img' => $this->id_img)
                    ));
                }
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
                    //$this->getImages($this->mod_edit);
                }
                break;
            case 'orderImages':
                if (isset($this->order_img)) {
                    $this->upd(
                        array(
                            'type' => 'order_img'
                        )
                    );
                }
                break;
        }
    }

    /**
     *
     */
    protected function adminList()
    {
        $this->modelLanguage->getLanguage();
        $defaultLanguage = $this->collectionLanguage->fetchData(['context'=>'one','type'=>'default']);
        $this->getItems('tabspanel',['lang' => $defaultLanguage['id_lang'], 'module' => $this->controller, 'id_module' => $this->edit ?: NULL],'all');
        $assign = [
            'id_tp',
            //'url_banner' => ['title' => 'name'],
            'img_tp' => ['title' => 'name','type' => 'bin', 'input' => null, 'class' => ''],
            'title_tp' => ['title' => 'name'],
            'desc_tp' => ['title' => 'name','type' => 'bin', 'input' => null]
        ];
        $this->data->getScheme(['mc_tabspanel', 'mc_tabspanel_content'], ['id_tp','title_tp','desc_tp'], $assign);
        $this->modelPlugins->display('mod/index.tpl');
    }

    /**
     * Execution du plugin dans un ou plusieurs modules core
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
                $this->runAction();
            }
            else {
                $this->adminList();
            }
        }
    }
}