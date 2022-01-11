<?php
function smarty_function_tabspanel_data($params, $smarty){
	$modelTemplate = $smarty->tpl_vars['modelTemplate']->value instanceof frontend_model_template ? $smarty->tpl_vars['modelTemplate']->value : new frontend_model_template();
    $collection = new plugins_tabspanel_public($modelTemplate);
    $assign = isset($params['assign']) ? $params['assign'] : 'tabspanel';
    $smarty->assign($assign,$collection->getBuildPagesItems($params));
}