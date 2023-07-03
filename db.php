<?php
/**
 * Class plugins_tabspanel_db
 */
class plugins_tabspanel_db {
	/**
	 * @var debug_logger $logger
	 */
	protected debug_logger $logger;
	
	/**
	 * @param array $config
	 * @param array $params
	 * @return array|bool
	 */
    public function fetchData(array $config, array $params = []) {
		if($config['context'] === 'all') {
			switch ($config['type']) {
				case 'tabspanel':
					$query = 'SELECT 
								ms.id_tp,
								msc.tab_id_tp,
								msc.title_tp,
								IFNULL(mti.default_img,0) as default_img,
								msc.desc_tp
							FROM mc_tabspanel AS ms
							LEFT JOIN mc_tabspanel_content msc on (ms.id_tp = msc.id_tp)
							LEFT JOIN mc_tabspanel_img AS mti ON ( ms.id_tp = mti.id_tp AND mti.default_img = 1 )
							LEFT JOIN mc_lang ml ON (msc.id_lang = ml.id_lang)
							WHERE (ml.id_lang = :lang OR ml.id_lang IS NULL)
							  AND ms.module_tp = :module
							  AND ms.id_module '.(empty($params['id_module']) ? 'IS NULL' : '= :id_module').'
							ORDER BY ms.order_tp';
					if(empty($params['id_module'])) unset($params['id_module']);
					break;
				case 'active':
					$query = 'SELECT 
								ms.id_tp,
								mtc.tab_id_tp,
								mtc.title_tp,
								mtc.desc_tp
							FROM mc_tabspanel ms
							JOIN mc_tabspanel_content mtc on (ms.id_tp = mtc.id_tp)
							JOIN mc_lang ml USING(id_lang)
							WHERE iso_lang = :iso
							  AND ms.module_tp = :module_tp
							  AND ms.id_module '.(empty($params['id_module']) ? 'IS NULL' : '= :id_module').'
							  AND mtc.published_tp = 1
							ORDER BY ms.order_tp';
					if(empty($params['id_module'])) unset($params['id_module']);
					break;
				case 'content':
					$query = 'SELECT ms.*, msc.*
							FROM mc_tabspanel ms
							JOIN mc_tabspanel_content msc USING(id_tp)
							JOIN mc_lang ml USING(id_lang)
							WHERE ms.id_tp = :id';
					break;
				/*case 'img':
					$query = 'SELECT ms.id_tp, ms.img_tp FROM mc_tabspanel ms WHERE ms.img_tp IS NOT NULL';
					break;*/
				case 'images':
					$query = 'SELECT img.*
					FROM mc_tabspanel_img AS img
					WHERE img.id_tp = :id ORDER BY order_img';
				break;
				case 'imagesAll':
					$query = 'SELECT img.* FROM mc_tabspanel_img AS img';
					break;
				default:
					return false;
			}

			try {
				return component_routing_db::layer()->fetchAll($query, $params);
			}
			catch (Exception $e) {
				if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
				$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			}
		}
		elseif($config['context'] === 'one') {
			switch ($config['type']) {
				case 'content':
					$query = 'SELECT * FROM mc_tabspanel_content WHERE id_tp = :id AND id_lang = :id_lang';
					break;
				case 'lastTabspanel':
					$query = 'SELECT * FROM mc_tabspanel ORDER BY id_tp DESC LIMIT 0,1';
					break;
				case 'img':
					$query = 'SELECT * FROM mc_tabspanel_img WHERE `id_img` = :id';
					break;
				case 'lastImgId':
					$query = 'SELECT id_img as `index` FROM mc_tabspanel_img WHERE id_tp = :id_tp ORDER BY id_img DESC LIMIT 0,1';
					break;
				case 'imgDefault':
					$query = 'SELECT id_img FROM mc_tabspanel_img WHERE id_tp = :id AND default_img = 1';
					break;
				case 'countImages':
					$query = 'SELECT count(id_img) as tot FROM mc_tabspanel_img WHERE id_tp = :id';
					break;
				default:
					return false;
			}

			try {
				return component_routing_db::layer()->fetch($query, $params);
			}
			catch (Exception $e) {
				if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
				$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			}
		}
		return false;
    }

    /**
     * @param string $type
     * @param array $params
	 * @return bool
     */
    public function insert(string $type, array $params = []): bool {
		switch ($type) {
			case 'tabspanel':
				$query = "INSERT INTO mc_tabspanel(module_tp, id_module, order_tp) 
						SELECT :module, :id_module, COUNT(id_tp) FROM mc_tabspanel WHERE module_tp = '".$params['module']."'";
				break;
			case 'content':
				$query = 'INSERT INTO mc_tabspanel_content(id_tp, id_lang, tab_id_tp, title_tp, desc_tp, published_tp)
						VALUES (:id_tp, :id_lang, :tab_id_tp, :title_tp, :desc_tp, :published_tp)';
				break;
            case 'img':
                $query = 'INSERT INTO `mc_tabspanel_img`(id_tp,name_img,order_img,default_img) 
						SELECT :id_tp,:name_img,COUNT(id_img),IF(COUNT(id_img) = 0,1,0) FROM mc_tabspanel_img WHERE id_tp IN ('.$params['id_tp'].')';
                break;
			default:
				return false;
		}

		try {
			component_routing_db::layer()->insert($query,$params);
			return true;
		}
		catch (Exception $e) {
			if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
			$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			return false;
		}
    }

	/**
	 * @param string $type
	 * @param array $params
	 * @return bool
	 */
    public function update(string $type, array $params = []): bool {
		switch ($type) {
			case 'content':
				$query = 'UPDATE mc_tabspanel_content
						SET 
							tab_id_tp = :tab_id_tp,
							title_tp = :title_tp,
							desc_tp = :desc_tp,
							published_tp = :published_tp
						WHERE id_tp = :id_tp 
						AND id_lang = :id_lang';
				break;
			case 'order':
				$query = 'UPDATE mc_tabspanel SET order_tp = :order WHERE id_tp = :id';
				break;
            case 'orderImages':
                $query = 'UPDATE mc_tabspanel_img SET order_img = :order WHERE id_img = :id';
                break;
            case 'imageDefault':
                $query = 'UPDATE mc_tabspanel_img
                		SET default_img = IF(id_img = :id_img, 1, 0)
						WHERE id_tp = :id';
                break;
            case 'firstImageDefault':
                $query = 'UPDATE mc_tabspanel_img
                		SET default_img = 1
                		WHERE id_tp = :id 
						ORDER BY order_img 
						LIMIT 1';
                break;
			default:
				return false;
		}

		try {
			component_routing_db::layer()->update($query,$params);
			return true;
		}
		catch (Exception $e) {
			if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
			$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			return false;
		}
    }

	/**
	 * @param string $type
	 * @param array $params
	 * @return bool
	 */
	protected function delete(string $type, array $params = []): bool {
		switch ($type) {
			case 'tabspanel':
				$query = 'DELETE FROM mc_tabspanel WHERE id_tp IN('.$params['id'].')';
				$params = [];
				break;
            case 'images':
                $query = 'DELETE FROM `mc_tabspanel_img` WHERE `id_img` IN ('.$params['id'].')';
                $params = [];
                break;
			default:
				return false;
		}

		try {
			component_routing_db::layer()->delete($query,$params);
			return true;
		}
		catch (Exception $e) {
			if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
			$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			return false;
		}
	}
}