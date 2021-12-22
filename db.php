<?php
/**
 * Class plugins_tabspanel_db
 */
class plugins_tabspanel_db
{
	/**
	 * @param array $config
	 * @param bool $params
	 * @return mixed|null
	 */
    public function fetchData(array $config, $params = false)
	{
        $sql = '';

        if(is_array($config)) {
            if($config['context'] === 'all') {
            	switch ($config['type']) {
					case 'tabspanel':
						$sql = 'SELECT 
									ms.id_tp,
									msc.title_tp,
                                    IFNULL(mti.default_img,0) as img_tp,
									msc.desc_tp
 								FROM mc_tabspanel AS ms
								JOIN mc_tabspanel_content msc on (ms.id_tp = msc.id_tp)
                                LEFT JOIN mc_tabspanel_img AS mti ON ( ms.id_tp = mti.id_tp AND mti.default_img = 1 )
								JOIN mc_lang ml USING(id_lang)
								WHERE ml.id_lang = :lang
								  AND ms.module_tp = :module
                                  AND ms.id_module '.(empty($params['id_module']) ? 'IS NULL' : '= :id_module').'
								ORDER BY ms.order_tp';
						if(empty($params['id_module'])) unset($params['id_module']);
						break;
					case 'activetabspanel':
						$sql = 'SELECT 
									id_tp,
									img_tp,
									title_tp,
									desc_tp
 								FROM mc_tabspanel ms
								LEFT JOIN mc_tabspanel_content msc USING(id_tp)
								LEFT JOIN mc_lang ml USING(id_lang)
								WHERE iso_lang = :lang
								  AND ms.module_tp = :module_tp
								  AND ms.id_module '.(empty($params['id_module']) ? 'IS NULL' : '= :id_module').'
								  AND published_tp = 1
								ORDER BY order_tp';
                        if(empty($params['id_module'])) unset($params['id_module']);
						break;
					case 'tabspanelContent':
						$sql = 'SELECT ms.*, msc.*
                    			FROM mc_tabspanel ms
                    			JOIN mc_tabspanel_content msc USING(id_tp)
                    			JOIN mc_lang ml USING(id_lang)
                    			WHERE ms.id_tp = :id';
						break;
					/*case 'img':
						$sql = 'SELECT ms.id_tp, ms.img_tp FROM mc_tabspanel ms WHERE ms.img_tp IS NOT NULL';
						break;*/
                    case 'images':
                        $sql = 'SELECT img.*
						FROM mc_tabspanel_img AS img
						WHERE img.id_tp = :id ORDER BY order_img ASC';
                    break;
                    case 'imagesAll':
                        $sql = 'SELECT img.* FROM mc_tabspanel_img AS img';
                        break;
				}

                return $sql ? component_routing_db::layer()->fetchAll($sql,$params) : null;
            }
            elseif($config['context'] === 'one') {
				switch ($config['type']) {
					case 'tabspanelContent':
						$sql = 'SELECT * FROM mc_tabspanel_content WHERE id_tp = :id AND id_lang = :id_lang';
						break;
					case 'lastTabspanel':
						$sql = 'SELECT * FROM mc_tabspanel ORDER BY id_tp DESC LIMIT 0,1';
						break;
					/*case 'img':
						$sql = 'SELECT * FROM mc_tabspanel WHERE id_tp = :id';
						break;*/
                    case 'img':
                        $sql = 'SELECT * FROM mc_tabspanel_img WHERE `id_img` = :id';
                        break;
                    case 'lastImgId':
                        $sql = 'SELECT id_img FROM mc_tabspanel_img ORDER BY id_img DESC LIMIT 0,1';
                        break;
                    case 'imgDefault':
                        $sql = 'SELECT id_img FROM mc_tabspanel_img WHERE id_tp = :id AND default_img = 1';
                        break;
				}

                return $sql ? component_routing_db::layer()->fetch($sql,$params) : null;
            }
        }
    }

    /**
     * @param array $config
     * @param array $params
	 * @return bool|string
     */
    public function insert(array $config, $params = [])
    {
		$sql = '';

		switch ($config['type']) {
			case 'tabspanel':
				$sql = "INSERT INTO mc_tabspanel(module_tp, id_module, order_tp) 
						SELECT :module, :id_module, COUNT(id_tp) FROM mc_tabspanel WHERE module_tp = '".$params['module']."'";
				break;
			case 'tabspanelContent':
				$sql = 'INSERT INTO mc_tabspanel_content(id_tp, id_lang, title_tp, desc_tp, published_tp)
						VALUES (:id_tp, :id_lang, :title_tp, :desc_tp, :published_tp)';
				break;
            case 'newImg':
                $sql = 'INSERT INTO `mc_tabspanel_img`(id_tp,name_img,order_img,default_img) 
						SELECT :id_tp,:name_img,COUNT(id_img),IF(COUNT(id_img) = 0,1,0) FROM mc_tabspanel_img WHERE id_tp IN ('.$params['id_tp'].')';
                break;
		}

		if($sql === '') return 'Unknown request asked';

		try {
			component_routing_db::layer()->insert($sql,$params);
			return true;
		}
		catch (Exception $e) {
			return 'Exception : '.$e->getMessage();
		}
    }

	/**
	 * @param array $config
	 * @param array $params
	 * @return bool|string
	 */
    public function update(array $config, $params = [])
    {
		$sql = '';

		switch ($config['type']) {
			case 'tabspanelContent':
				$sql = 'UPDATE mc_tabspanel_content
						SET 
							title_tp = :title_tp,
							desc_tp = :desc_tp,
							published_tp = :published_tp
						WHERE id_tp = :id_tp 
						AND id_lang = :id_lang';
				break;
			case 'order':
				$sql = 'UPDATE mc_tabspanel 
						SET order_tp = :order_tp
						WHERE id_tp = :id_tp';
				break;
            case 'order_img':
                $sql = 'UPDATE mc_tabspanel_img SET order_img = :order_img
                		WHERE id_img = :id_img';
                break;
            case 'imageDefault':
                $sql = 'UPDATE mc_tabspanel_img
                		SET default_img = CASE id_img
							WHEN :id_img THEN 1
							ELSE 0
						END
						WHERE id_tp = :id';
                break;
            case 'firstImageDefault':
                $sql = 'UPDATE mc_tabspanel_img
                		SET default_img = 1
                		WHERE id_tp = :id 
						ORDER BY order_img ASC 
						LIMIT 1';
                break;
		}

		if($sql === '') return 'Unknown request asked';

		try {
			component_routing_db::layer()->update($sql,$params);
			return true;
		}
		catch (Exception $e) {
			return 'Exception : '.$e->getMessage();
		}
    }

	/**
	 * @param array $config
	 * @param array $params
	 * @return bool|string
	 */
	protected function delete(array $config, $params = [])
    {
		$sql = '';

		switch ($config['type']) {
			case 'tabspanel':
				$sql = 'DELETE FROM mc_tabspanel WHERE id_tp IN('.$params['id'].')';
				$params = [];
				break;
            case 'delImages':
                $sql = 'DELETE FROM `mc_tabspanel_img` WHERE `id_img` IN ('.$params['id'].')';
                $params = array();
                break;
		}

		if($sql === '') return 'Unknown request asked';

		try {
			component_routing_db::layer()->delete($sql,$params);
			return true;
		}
		catch (Exception $e) {
			return 'Exception : '.$e->getMessage();
		}
	}
}